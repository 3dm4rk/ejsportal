<?php
// admin/users.php
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/functions.php';

// Handle add / edit / delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $role_id = (int)$_POST['role_id'];
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $full_name, $email, $role_id]);
        log_activity($_SESSION['user_id'], 'Created User', "Created user $username");
        $message = 'User added successfully.';
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $role_id = (int)$_POST['role_id'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role_id=?, is_active=? WHERE id=?");
        $stmt->execute([$full_name, $email, $role_id, $is_active, $id]);
        log_activity($_SESSION['user_id'], 'Updated User', "Updated user ID $id");
        $message = 'User updated successfully.';
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $user = get_user_by_id($id);
        if ($user && $user['username'] !== 'admin') {
            delete_user($id);
            log_activity($_SESSION['user_id'], 'Deleted User', "Deleted user ID $id");
            $message = 'User deleted successfully.';
        } else {
            $message = 'Cannot delete the main admin.';
        }
    }
}

$users = get_all_users();
$roles = get_all_roles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar">
        <h1>User Management</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <?php if ($message): ?>
        <div style="background:var(--success); color:#fff; padding:0.75rem; border-radius:8px; margin-bottom:1rem;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add User Form -->
    <div class="glass" style="padding:1.5rem; margin-bottom:2rem;">
        <h3><i class="fas fa-user-plus"></i> Add New User</h3>
        <form method="POST" style="display:flex; flex-wrap:wrap; gap:1rem; align-items:end; margin-top:1rem;">
            <input type="hidden" name="action" value="add">
            <div><label>Username</label><input type="text" name="username" required style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Password</label><input type="text" name="password" required style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Full Name</label><input type="text" name="full_name" required style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Email</label><input type="email" name="email" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Role</label>
                <select name="role_id" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);">
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add User</button>
        </form>
    </div>

    <!-- User List -->
    <div class="glass" style="padding:1.5rem;">
        <h3><i class="fas fa-users"></i> All Users</h3>
        <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="text-align:left; padding:0.5rem;">ID</th>
                    <th style="text-align:left; padding:0.5rem;">Username</th>
                    <th style="text-align:left; padding:0.5rem;">Full Name</th>
                    <th style="text-align:left; padding:0.5rem;">Email</th>
                    <th style="text-align:left; padding:0.5rem;">Role</th>
                    <th style="text-align:left; padding:0.5rem;">Active</th>
                    <th style="text-align:left; padding:0.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td style="padding:0.5rem;"><?= $u['id'] ?></td>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($u['username']) ?></td>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($u['full_name']) ?></td>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($u['role_name'] ?? 'None') ?></td>
                    <td style="padding:0.5rem;"><?= $u['is_active'] ? 'Yes' : 'No' ?></td>
                    <td style="padding:0.5rem;">
                        <!-- Edit form (inline) -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="text" name="full_name" value="<?= htmlspecialchars($u['full_name']) ?>" style="width:100px;">
                            <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" style="width:120px;">
                            <select name="role_id">
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label><input type="checkbox" name="is_active" <?= $u['is_active'] ? 'checked' : '' ?>> Active</label>
                            <button type="submit" class="btn btn-success" style="padding:0.2rem 0.5rem;"><i class="fas fa-edit"></i></button>
                        </form>
                        <?php if ($u['username'] !== 'admin'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-danger" style="padding:0.2rem 0.5rem;"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
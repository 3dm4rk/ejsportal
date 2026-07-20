<?php
// admin/roles.php
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_role'])) {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $stmt = $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
        log_activity($_SESSION['user_id'], 'Added Role', "Added role $name");
        $message = 'Role added.';
    } elseif (isset($_POST['delete_role'])) {
        $id = (int)$_POST['id'];
        // Prevent deletion of core roles (admin, hr, worker)
        $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $role = $stmt->fetch();
        if ($role && !in_array($role['name'], ['admin','hr','worker'])) {
            $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            log_activity($_SESSION['user_id'], 'Deleted Role', "Deleted role ID $id");
            $message = 'Role deleted.';
        } else {
            $message = 'Cannot delete core roles.';
        }
    }
}
$roles = get_all_roles();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Role Management - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>Role Management</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <?php if ($message): ?><div style="background:var(--success); color:#fff; padding:0.75rem; border-radius:8px; margin-bottom:1rem;"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <div class="glass" style="padding:1.5rem; margin-bottom:2rem;">
        <h3>Add New Role</h3>
        <form method="POST" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:end;">
            <div><label>Role Name</label><input type="text" name="name" required style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Description</label><input type="text" name="description" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <button type="submit" name="add_role" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
        </form>
    </div>
    <div class="glass" style="padding:1.5rem;">
        <h3>Existing Roles</h3>
        <table style="width:100%;">
            <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($roles as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td>
                        <?php if (!in_array($r['name'], ['admin','hr','worker'])): ?>
                        <form method="POST" onsubmit="return confirm('Delete this role?');">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button type="submit" name="delete_role" class="btn btn-danger" style="padding:0.2rem 0.5rem;"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php else: ?>
                        <span style="color:var(--text-secondary);">Protected</span>
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
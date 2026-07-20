<?php
// admin/settings.php
require_once '../includes/auth.php';
require_role('admin');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = substr($key, 8);
            $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $setting_key]);
        }
    }
    log_activity($_SESSION['user_id'], 'Updated Settings', 'System settings updated');
    $message = 'Settings saved.';
}
$settings = $pdo->query("SELECT * FROM system_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>System Settings - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>System Settings</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <?php if ($message): ?><div style="background:var(--success); color:#fff; padding:0.75rem; border-radius:8px; margin-bottom:1rem;"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <div class="glass" style="padding:1.5rem;">
        <form method="POST">
            <?php foreach ($settings as $key => $val): ?>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-weight:500;"><?= ucfirst(str_replace('_', ' ', $key)) ?></label>
                    <input type="text" name="setting_<?= $key ?>" value="<?= htmlspecialchars($val) ?>" style="width:100%; padding:0.5rem; border-radius:8px; background:var(--card-bg);">
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </form>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
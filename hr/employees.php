<?php
// hr/employees.php
require_once '../includes/auth.php';
require_role('hr');
require_once '../includes/functions.php';

$workers = get_users_by_role('worker');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employees - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>Employee List</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="glass" style="padding:1.5rem;">
        <table style="width:100%;">
            <thead><tr><th>Full Name</th><th>Username</th><th>Email</th></tr></thead>
            <tbody>
            <?php foreach ($workers as $w): ?>
                <tr><td><?= htmlspecialchars($w['full_name']) ?></td><td><?= htmlspecialchars($w['username']) ?></td><td><?= htmlspecialchars($w['email']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
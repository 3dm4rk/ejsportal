<?php
// worker/history.php
require_once '../includes/auth.php';
require_role('worker');
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$logs = $pdo->prepare("SELECT * FROM attendance_logs WHERE user_id = ? ORDER BY date DESC");
$logs->execute([$user_id]);
$logs = $logs->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Attendance History - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>My Attendance History</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="glass" style="padding:1.5rem; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead><tr><th>Date</th><th>Login</th><th>Logout</th><th>Total Hrs</th><th>Overtime</th><th>Status</th></tr></thead>
            <tbody>
            <?php if ($logs): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= format_date($log['date']) ?></td>
                        <td><?= format_time($log['job_login']) ?></td>
                        <td><?= format_time($log['job_logout']) ?></td>
                        <td><?= $log['total_hours'] ?></td>
                        <td><?= $log['overtime_hours'] ?></td>
                        <td><span style="background:<?= $log['status'] === 'Present' ? 'var(--success)' : ($log['status'] === 'Late' ? 'var(--warning)' : 'var(--danger)') ?>; padding:0.2rem 0.8rem; border-radius:20px; color:#fff;"><?= $log['status'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; padding:1rem;">No attendance records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
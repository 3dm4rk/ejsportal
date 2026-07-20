<?php
// admin/attendance.php
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/functions.php';

$filters = [];
if (isset($_GET['user_id']) && $_GET['user_id'] > 0) $filters['user_id'] = (int)$_GET['user_id'];
if (isset($_GET['month']) && isset($_GET['year'])) {
    $filters['month'] = (int)$_GET['month'];
    $filters['year'] = (int)$_GET['year'];
}
$logs = get_attendance_logs($filters);
$users = get_all_users();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Logs - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>Attendance Logs</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1rem; align-items:end;">
        <div><label>Employee</label>
            <select name="user_id" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);">
                <option value="0">All</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($_GET['user_id'] ?? 0) == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>Month</label><input type="month" name="month_year" value="<?= isset($_GET['month_year']) ? $_GET['month_year'] : date('Y-m') ?>" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="attendance.php" class="btn btn-secondary">Clear</a>
    </form>
    <div class="glass" style="padding:1.5rem; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead><tr>
                <th>Date</th><th>Employee</th><th>Login</th><th>Logout</th>
                <th>Total Hrs</th><th>Overtime</th><th>Status</th>
            </tr></thead>
            <tbody>
            <?php if ($logs): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= format_date($log['date']) ?></td>
                        <td><?= htmlspecialchars($log['full_name']) ?></td>
                        <td><?= format_time($log['job_login']) ?></td>
                        <td><?= format_time($log['job_logout']) ?></td>
                        <td><?= $log['total_hours'] ?></td>
                        <td><?= $log['overtime_hours'] ?></td>
                        <td><span style="background:<?= $log['status'] === 'Present' ? 'var(--success)' : ($log['status'] === 'Late' ? 'var(--warning)' : 'var(--danger)') ?>; padding:0.2rem 0.8rem; border-radius:20px; color:#fff;"><?= $log['status'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding:1rem;">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
<?php
// admin/index.php
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/functions.php';

// Get statistics
$total_employees = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active=1")->fetchColumn();
$currently_working = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM attendance_logs WHERE date = CURDATE() AND job_login IS NOT NULL AND job_logout IS NULL")->fetchColumn();
$on_break = $pdo->query("SELECT COUNT(DISTINCT br.attendance_id) FROM break_records br 
                          JOIN attendance_logs al ON br.attendance_id = al.id 
                          WHERE al.date = CURDATE() AND br.break_in IS NOT NULL AND br.break_out IS NULL")->fetchColumn();
$at_lunch = $pdo->query("SELECT COUNT(DISTINCT lr.attendance_id) FROM lunch_records lr 
                          JOIN attendance_logs al ON lr.attendance_id = al.id 
                          WHERE al.date = CURDATE() AND lr.lunch_in IS NOT NULL AND lr.lunch_out IS NULL")->fetchColumn();
$logged_out = $total_employees - $currently_working; // approximation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Admin Dashboard</h1>
            <div class="topbar-actions">
                <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
                <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card glass">
                <div class="stat-label">Total Employees</div>
                <div class="stat-value"><?= $total_employees ?></div>
            </div>
            <div class="stat-card glass">
                <div class="stat-label">Currently Working</div>
                <div class="stat-value"><?= $currently_working ?></div>
            </div>
            <div class="stat-card glass">
                <div class="stat-label">On Break</div>
                <div class="stat-value"><?= $on_break ?></div>
            </div>
            <div class="stat-card glass">
                <div class="stat-label">At Lunch</div>
                <div class="stat-value"><?= $at_lunch ?></div>
            </div>
            <div class="stat-card glass">
                <div class="stat-label">Logged Out</div>
                <div class="stat-value"><?= $logged_out ?></div>
            </div>
        </div>

        <!-- Quick actions -->
        <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
            <a href="users.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Manage Users</a>
            <a href="attendance.php" class="btn btn-success"><i class="fas fa-clock"></i> Attendance Logs</a>
            <a href="reports.php" class="btn btn-warning"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="settings.php" class="btn btn-secondary"><i class="fas fa-cog"></i> Settings</a>
        </div>

        <!-- Recent activity (example) -->
        <div class="glass" style="padding:1.5rem;">
            <h3><i class="fas fa-history"></i> Recent Activity</h3>
            <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--glass-border);">
                        <th style="text-align:left; padding:0.5rem;">User</th>
                        <th style="text-align:left; padding:0.5rem;">Action</th>
                        <th style="text-align:left; padding:0.5rem;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs = $pdo->query("SELECT al.*, u.full_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 10");
                    while ($log = $logs->fetch()): ?>
                    <tr>
                        <td style="padding:0.5rem;"><?= htmlspecialchars($log['full_name'] ?? 'System') ?></td>
                        <td style="padding:0.5rem;"><?= htmlspecialchars($log['action']) ?></td>
                        <td style="padding:0.5rem;"><?= format_datetime($log['created_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
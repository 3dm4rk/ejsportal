<?php
// worker/index.php
require_once '../includes/auth.php';
require_role('worker');
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$attendance = get_attendance_for_user($user_id, $today);
$is_logged_in = $attendance && $attendance['job_login'] && !$attendance['job_logout'];

// Handle attendance actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $now = date('Y-m-d H:i:s');
    // For biometric simulation, we just accept
    switch ($action) {
        case 'job_login':
            if (!$attendance) {
                // Create new attendance record
                $stmt = $pdo->prepare("INSERT INTO attendance_logs (user_id, job_login, date) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $now, $today]);
                $attendance_id = $pdo->lastInsertId();
                log_activity($user_id, 'Job Login', "Logged in at $now");
            }
            break;
        case 'break_in':
            if ($attendance) {
                $stmt = $pdo->prepare("INSERT INTO break_records (attendance_id, break_in) VALUES (?, ?)");
                $stmt->execute([$attendance['id'], $now]);
                log_activity($user_id, 'Break In', "Break started at $now");
            }
            break;
        case 'break_out':
            // Update latest break record
            $stmt = $pdo->prepare("UPDATE break_records SET break_out = ?, duration_minutes = TIMESTAMPDIFF(MINUTE, break_in, ?) WHERE attendance_id = ? AND break_out IS NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$now, $now, $attendance['id']]);
            log_activity($user_id, 'Break Out', "Break ended at $now");
            break;
        case 'lunch_in':
            if ($attendance) {
                $stmt = $pdo->prepare("INSERT INTO lunch_records (attendance_id, lunch_in) VALUES (?, ?)");
                $stmt->execute([$attendance['id'], $now]);
                log_activity($user_id, 'Lunch In', "Lunch started at $now");
            }
            break;
        case 'lunch_out':
            $stmt = $pdo->prepare("UPDATE lunch_records SET lunch_out = ?, duration_minutes = TIMESTAMPDIFF(MINUTE, lunch_in, ?) WHERE attendance_id = ? AND lunch_out IS NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$now, $now, $attendance['id']]);
            log_activity($user_id, 'Lunch Out', "Lunch ended at $now");
            break;
        case 'job_logout':
            if ($attendance) {
                $total_hours = calculate_total_hours($attendance['job_login'], $now);
                $overtime = calculate_overtime($total_hours);
                $status = get_attendance_status($attendance['job_login']);
                $stmt = $pdo->prepare("UPDATE attendance_logs SET job_logout = ?, total_hours = ?, overtime_hours = ?, status = ? WHERE id = ?");
                $stmt->execute([$now, $total_hours, $overtime, $status, $attendance['id']]);
                log_activity($user_id, 'Job Logout', "Logged out at $now");
            }
            break;
    }
    header('Location: index.php');
    exit;
}

// Get today's attendance with break/lunch details
$break_records = $attendance ? get_break_records($attendance['id']) : [];
$lunch_records = $attendance ? get_lunch_records($attendance['id']) : [];
$current_break = $attendance ? $pdo->prepare("SELECT * FROM break_records WHERE attendance_id = ? AND break_out IS NULL ORDER BY id DESC LIMIT 1") : null;
if ($current_break) {
    $current_break->execute([$attendance['id']]);
    $current_break = $current_break->fetch();
}
$current_lunch = $attendance ? $pdo->prepare("SELECT * FROM lunch_records WHERE attendance_id = ? AND lunch_out IS NULL ORDER BY id DESC LIMIT 1") : null;
if ($current_lunch) {
    $current_lunch->execute([$attendance['id']]);
    $current_lunch = $current_lunch->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker Dashboard - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Worker Dashboard</h1>
            <div class="topbar-actions">
                <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
                <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:1rem; margin-bottom:2rem;">
            <div class="stat-card glass"><i class="fas fa-fingerprint"></i> Biometric: <span id="biometricStatus"><?= $is_logged_in ? 'Active' : 'Inactive' ?></span></div>
            <div class="stat-card glass"><i class="fas fa-clock"></i> Status: <?= $is_logged_in ? 'Working' : 'Logged Out' ?></div>
        </div>

        <div class="glass" style="padding:1.5rem; margin-bottom:2rem;">
            <h3>Today's Attendance</h3>
            <div style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-top:1rem;">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="job_login">
                    <button type="submit" class="btn btn-primary" <?= $is_logged_in ? 'disabled' : '' ?>><i class="fas fa-sign-in-alt"></i> Job Login</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="break_in">
                    <button type="submit" class="btn btn-warning" <?= (!$is_logged_in || $current_break) ? 'disabled' : '' ?>><i class="fas fa-pause"></i> Break In</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="break_out">
                    <button type="submit" class="btn btn-success" <?= (!$is_logged_in || !$current_break) ? 'disabled' : '' ?>><i class="fas fa-play"></i> Break Out</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="lunch_in">
                    <button type="submit" class="btn btn-warning" <?= (!$is_logged_in || $current_lunch) ? 'disabled' : '' ?>><i class="fas fa-utensils"></i> Lunch In</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="lunch_out">
                    <button type="submit" class="btn btn-success" <?= (!$is_logged_in || !$current_lunch) ? 'disabled' : '' ?>><i class="fas fa-utensils"></i> Lunch Out</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="job_logout">
                    <button type="submit" class="btn btn-danger" <?= !$is_logged_in ? 'disabled' : '' ?>><i class="fas fa-sign-out-alt"></i> Job Logout</button>
                </form>
            </div>
            <div style="margin-top:1rem; font-size:0.9rem; color:var(--text-secondary);">
                <i class="fas fa-info-circle"></i> Click "Biometric" button on your fingerprint scanner (simulated).
            </div>
        </div>

        <div class="glass" style="padding:1.5rem;">
            <h3>Today's Activity</h3>
            <?php if ($attendance): ?>
                <p><strong>Login:</strong> <?= format_datetime($attendance['job_login']) ?></p>
                <?php if ($attendance['job_logout']): ?>
                    <p><strong>Logout:</strong> <?= format_datetime($attendance['job_logout']) ?></p>
                    <p><strong>Total Hours:</strong> <?= $attendance['total_hours'] ?> hrs</p>
                    <p><strong>Overtime:</strong> <?= $attendance['overtime_hours'] ?> hrs</p>
                <?php endif; ?>
                <?php if ($break_records): ?>
                    <p><strong>Breaks:</strong></p>
                    <ul>
                    <?php foreach ($break_records as $br): ?>
                        <li><?= format_time($br['break_in']) ?> - <?= format_time($br['break_out']) ?> (<?= $br['duration_minutes'] ?> min)</li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if ($lunch_records): ?>
                    <p><strong>Lunches:</strong></p>
                    <ul>
                    <?php foreach ($lunch_records as $lr): ?>
                        <li><?= format_time($lr['lunch_in']) ?> - <?= format_time($lr['lunch_out']) ?> (<?= $lr['duration_minutes'] ?> min)</li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <p>No attendance record for today.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
<?php
// hr/index.php
require_once '../includes/auth.php';
require_role('hr');
require_once '../includes/functions.php';

$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$employee_filter = $_GET['employee'] ?? 0;

// Get all workers
$workers = get_users_by_role('worker');

// Build calendar data for each day
$first_day = mktime(0,0,0, $month, 1, $year);
$days_in_month = date('t', $first_day);
$start_day_of_week = date('w', $first_day); // 0=Sunday

// Fetch attendance for the month for each worker or selected
$attendance_data = [];
$user_ids = $employee_filter ? [$employee_filter] : array_column($workers, 'id');
if ($user_ids) {
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM attendance_logs 
                            WHERE user_id IN ($placeholders) 
                            AND MONTH(date) = ? AND YEAR(date) = ?");
    $params = array_merge($user_ids, [$month, $year]);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $attendance_data[$row['user_id']][$row['date']] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HR Dashboard - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>HR Dashboard - Attendance Calendar</h1>
            <div class="topbar-actions">
                <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
                <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:2rem; align-items:center;">
            <select name="employee" class="form-input" style="padding:0.5rem; border-radius:10px; background:var(--card-bg);">
                <option value="0">All Employees</option>
                <?php foreach ($workers as $w): ?>
                    <option value="<?= $w['id'] ?>" <?= $employee_filter == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="month" name="month_year" value="<?= sprintf('%04d-%02d', $year, $month) ?>" style="padding:0.5rem; border-radius:10px; background:var(--card-bg);">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="reports.php?export=pdf&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-success"><i class="fas fa-file-pdf"></i> Export PDF</a>
            <a href="reports.php?export=excel&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
        </form>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
            <?php
            $day_names = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            foreach ($day_names as $name) {
                echo "<div class='calendar-header'>$name</div>";
            }
            // Empty cells before first day
            for ($i = 0; $i < $start_day_of_week; $i++) {
                echo "<div class='calendar-day empty'></div>";
            }
            // Days
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
                // Gather attendance for this day across filtered users
                $day_data = [];
                foreach ($user_ids as $uid) {
                    if (isset($attendance_data[$uid][$date_str])) {
                        $day_data[] = $attendance_data[$uid][$date_str];
                    }
                }
                $statuses = array_column($day_data, 'status');
                $status_summary = '';
                if (count($statuses) > 0) {
                    $present = count(array_filter($statuses, fn($s) => $s === 'Present'));
                    $late = count(array_filter($statuses, fn($s) => $s === 'Late'));
                    $absent = count(array_filter($statuses, fn($s) => $s === 'Absent'));
                    $status_summary = "👤 $present | ⏰ $late | ❌ $absent";
                } else {
                    $status_summary = 'No records';
                }
                echo "<div class='calendar-day'>
                        <div class='day-number'>$day</div>
                        <div class='attendance-summary'>$status_summary</div>
                      </div>";
            }
            ?>
        </div>

        <!-- Detailed view for selected day (simplified) -->
        <div style="margin-top:2rem;" class="glass" style="padding:1.5rem;">
            <h3>Details for <?= date('F Y', strtotime("$year-$month-01")) ?></h3>
            <p>Click on a day to see individual logs (implement via JS).</p>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
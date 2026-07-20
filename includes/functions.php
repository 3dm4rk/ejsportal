<?php
// includes/functions.php
require_once __DIR__ . '/../config/database.php';

function get_user_by_id($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function get_attendance_for_user($user_id, $date) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ?");
    $stmt->execute([$user_id, $date]);
    return $stmt->fetch();
}

function calculate_total_hours($login, $logout) {
    if (!$login || !$logout) return 0;
    $diff = strtotime($logout) - strtotime($login);
    return round($diff / 3600, 2);
}

function calculate_overtime($total_hours, $expected_hours = 8) {
    return max(0, $total_hours - $expected_hours);
}

function get_attendance_status($login_time, $expected_start = '09:00:00') {
    if (!$login_time) return 'Absent';
    $login_ts = strtotime($login_time);
    $expected_ts = strtotime($expected_start);
    $late_minutes = round(($login_ts - $expected_ts) / 60);
    if ($late_minutes > 15) return 'Late';
    return 'Present';
}

function format_datetime($datetime) {
    if (!$datetime) return '-';
    return date('M d, Y h:i A', strtotime($datetime));
}
function format_date($date) {
    return date('M d, Y', strtotime($date));
}
function format_time($time) {
    if (!$time) return '-';
    return date('h:i A', strtotime($time));
}

function get_break_records($attendance_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM break_records WHERE attendance_id = ? ORDER BY id");
    $stmt->execute([$attendance_id]);
    return $stmt->fetchAll();
}

function get_lunch_records($attendance_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM lunch_records WHERE attendance_id = ? ORDER BY id");
    $stmt->execute([$attendance_id]);
    return $stmt->fetchAll();
}

function get_users_by_role($role_name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u 
                            JOIN roles r ON u.role_id = r.id 
                            WHERE r.name = ? AND u.is_active = 1");
    $stmt->execute([$role_name]);
    return $stmt->fetchAll();
}

// Get all roles
function get_all_roles() {
    global $pdo;
    return $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
}

// Get all users with role names
function get_all_users() {
    global $pdo;
    return $pdo->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.id")->fetchAll();
}

// Get attendance logs with user info (for admin/hr)
function get_attendance_logs($filters = []) {
    global $pdo;
    $sql = "SELECT al.*, u.full_name, u.username FROM attendance_logs al 
            JOIN users u ON al.user_id = u.id WHERE 1=1";
    $params = [];
    if (!empty($filters['user_id'])) {
        $sql .= " AND al.user_id = ?";
        $params[] = $filters['user_id'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= " AND al.date >= ?";
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND al.date <= ?";
        $params[] = $filters['date_to'];
    }
    if (!empty($filters['month']) && !empty($filters['year'])) {
        $sql .= " AND MONTH(al.date) = ? AND YEAR(al.date) = ?";
        $params[] = $filters['month'];
        $params[] = $filters['year'];
    }
    $sql .= " ORDER BY al.date DESC, al.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Delete user
function delete_user($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
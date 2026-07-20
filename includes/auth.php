<?php
// includes/auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u 
                            LEFT JOIN roles r ON u.role_id = r.id 
                            WHERE u.username = ? AND u.is_active = 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['role_id'] = $user['role_id'];
        log_activity($user['id'], 'Login', 'User logged in');
        return true;
    }
    return false;
}

function logout() {
    log_activity($_SESSION['user_id'] ?? 0, 'Logout', 'User logged out');
    session_destroy();
    header('Location: ../index.php');
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ../index.php');
        exit;
    }
}

function has_role($role_name) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role_name;
}

function require_role($role_name) {
    require_login();
    if (!has_role($role_name)) {
        header('HTTP/1.0 403 Forbidden');
        die('Access denied.');
    }
}

function log_activity($user_id, $action, $details = '') {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $details, $ip]);
}

// CSRF token generation and validation
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
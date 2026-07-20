<?php
// dashboard.php
require_once 'includes/auth.php';
require_login();

$role = $_SESSION['role'];
switch ($role) {
    case 'admin':
        header('Location: admin/');
        break;
    case 'hr':
        header('Location: hr/');
        break;
    case 'worker':
        header('Location: worker/');
        break;
    default:
        logout();
        break;
}
exit;
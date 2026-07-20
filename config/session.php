<?php
// config/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>
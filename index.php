<?php
// index.php
require_once 'config/session.php';
require_once 'includes/auth.php';

// If already logged in, redirect
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        if (login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EJS Portal - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container glass" style="max-width:400px; margin:10vh auto; padding:2.5rem;">
        <h1 style="text-align:center; color:var(--accent);"><i class="fas fa-users-cog"></i> EJS Portal</h1>
        <p style="text-align:center; color:var(--text-secondary); margin-bottom:2rem;">Sign in to your account</p>
        <?php if ($error): ?>
            <div style="background:var(--danger); color:#fff; padding:0.75rem; border-radius:8px; margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.25rem; font-weight:500;">Username</label>
                <input type="text" name="username" class="form-input" style="width:100%; padding:0.75rem; border-radius:10px; border:1px solid var(--glass-border); background:var(--card-bg); color:var(--text-primary);" required>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.25rem; font-weight:500;">Password</label>
                <input type="password" name="password" class="form-input" style="width:100%; padding:0.75rem; border-radius:10px; border:1px solid var(--glass-border); background:var(--card-bg); color:var(--text-primary);" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; font-size:1.1rem;">Sign In</button>
        </form>
        <div style="text-align:center; margin-top:1.5rem; font-size:0.9rem; color:var(--text-secondary);">
            <i class="fas fa-shield-alt"></i> Secure Login
        </div>
    </div>
</body>
</html>
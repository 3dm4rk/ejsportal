<?php
// admin/reports.php
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/functions.php';

// Simple statistics
$total_employees = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active=1")->fetchColumn();
$total_present = $pdo->query("SELECT COUNT(*) FROM attendance_logs WHERE date = CURDATE() AND status = 'Present'")->fetchColumn();
$total_late = $pdo->query("SELECT COUNT(*) FROM attendance_logs WHERE date = CURDATE() AND status = 'Late'")->fetchColumn();
$total_absent = $pdo->query("SELECT COUNT(*) FROM attendance_logs WHERE date = CURDATE() AND status = 'Absent'")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>Reports & Analytics</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="stats-grid">
        <div class="stat-card glass"><div class="stat-label">Total Employees</div><div class="stat-value"><?= $total_employees ?></div></div>
        <div class="stat-card glass"><div class="stat-label">Present Today</div><div class="stat-value"><?= $total_present ?></div></div>
        <div class="stat-card glass"><div class="stat-label">Late Today</div><div class="stat-value"><?= $total_late ?></div></div>
        <div class="stat-card glass"><div class="stat-label">Absent Today</div><div class="stat-value"><?= $total_absent ?></div></div>
    </div>
    <div class="glass" style="padding:1.5rem;">
        <h3>Export Options</h3>
        <p>Here you can add export to PDF/Excel using libraries like TCPDF or PhpSpreadsheet.</p>
        <a href="#" class="btn btn-success"><i class="fas fa-file-pdf"></i> Export PDF</a>
        <a href="#" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
<?php
// hr/reports.php
require_once '../includes/auth.php';
require_role('hr');
require_once '../includes/functions.php';

// This is where you'd integrate TCPDF or PhpSpreadsheet for export.
// For now, we just show a filter and a download button.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HR Reports - EJS Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="main-content">
    <div class="topbar"><h1>HR Reports</h1>
        <div class="topbar-actions">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="glass" style="padding:1.5rem;">
        <h3>Export Attendance Report</h3>
        <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:end;">
            <div><label>Month</label><input type="month" name="month" value="<?= date('Y-m') ?>" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);"></div>
            <div><label>Employee</label>
                <select name="employee" style="padding:0.5rem; border-radius:8px; background:var(--card-bg);">
                    <option value="0">All</option>
                    <?php foreach (get_users_by_role('worker') as $w): ?>
                        <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="export" value="pdf" class="btn btn-success"><i class="fas fa-file-pdf"></i> PDF</button>
            <button type="submit" name="export" value="excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
        </form>
        <p style="margin-top:1rem; color:var(--text-secondary);"><i class="fas fa-info-circle"></i> Note: PDF/Excel generation requires external libraries. This is a placeholder.</p>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>
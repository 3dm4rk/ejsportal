<?php
// includes/header.php
// This file must be included in every dashboard page after the <body> tag.
// It renders the sidebar and topbar (except the topbar content which is handled per page).
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">EJS Portal</div>
    <div class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <a href="../admin/" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../admin/users.php" class="<?= $current_page === 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Users</a>
            <a href="../admin/roles.php" class="<?= $current_page === 'roles.php' ? 'active' : '' ?>"><i class="fas fa-user-tag"></i> Roles</a>
            <a href="../admin/attendance.php" class="<?= $current_page === 'attendance.php' ? 'active' : '' ?>"><i class="fas fa-clock"></i> Attendance</a>
            <a href="../admin/reports.php" class="<?= $current_page === 'reports.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="../admin/settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a>
            <a href="../admin/activity_logs.php" class="<?= $current_page === 'activity_logs.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> Activity Logs</a>
        <?php elseif ($role === 'hr'): ?>
            <a href="../hr/" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Calendar</a>
            <a href="../hr/employees.php" class="<?= $current_page === 'employees.php' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> Employees</a>
            <a href="../hr/reports.php" class="<?= $current_page === 'reports.php' ? 'active' : '' ?>"><i class="fas fa-file-export"></i> Reports</a>
        <?php elseif ($role === 'worker'): ?>
            <a href="../worker/" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="fas fa-fingerprint"></i> Dashboard</a>
            <a href="../worker/history.php" class="<?= $current_page === 'history.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> My History</a>
        <?php endif; ?>
        <a href="../logout.php" class=""><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>
<button id="sidebarToggle" style="position:fixed; top:1rem; left:1rem; z-index:1100; background:var(--accent); color:#fff; border:none; border-radius:8px; padding:0.5rem 0.8rem; display:none; cursor:pointer;">
    <i class="fas fa-bars"></i>
</button>
<script>
    // Show toggle button on mobile
    if (window.innerWidth <= 768) {
        document.getElementById('sidebarToggle').style.display = 'block';
    }
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>
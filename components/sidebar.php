<?php
    function is_active($page) {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page === $page) ? 'active' : '';
    }
?>

<aside class="sidebar">
    <div class="sidebar-brand">IRMS Portal</div>
    <ul class="sidebar-menu">
        
        <li><a href="dashboard.php" class="<?= is_active("dashboard.php") ?>">Dashboard</a></li>

        <?php if($_SESSION['role'] === 'admin'): ?>
        <li><a href="programmes.php" class="<?= is_active("programmes.php") ?>">Programmes</a></li>
        <li><a href="students.php" class="<?= is_active("students.php") ?>">Students</a></li>
        <li><a href="assessors.php" class="<?= is_active("assessors.php") ?>">Assessors</a></li>
        <li><a href="internships.php" class="<?= is_active("internships.php") ?>">Internships</a></li>
        <?php endif; ?>

        <li><a href="settings.php" class="<?= is_active("settings.php") ?>">Settings</a></li>
    </ul>
</aside>
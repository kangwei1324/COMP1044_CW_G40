<?php
    function is_active($page) {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page === $page) ? 'active' : '';
    }
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-text">IRMS Portal</span>
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            <span class="icon">☰</span>
        </button>
    </div>
    <ul class="sidebar-menu">
        
        <li>
            <a href="dashboard.php" class="<?= is_active("dashboard.php") ?>">
                <span class="icon">📊</span>
                <span class="link-text">Dashboard</span>
            </a>
        </li>

        <?php if($_SESSION['role'] === 'admin'): ?>
        <li>
            <a href="programmes.php" class="<?= is_active("programmes.php") ?>">
                <span class="icon">📜</span>
                <span class="link-text">Programmes</span>
            </a>
        </li>
        <li>
            <a href="students.php" class="<?= is_active("students.php") ?>">
                <span class="icon">👨‍🎓</span>
                <span class="link-text">Students</span>
            </a>
        </li>
        <li>
            <a href="assessors.php" class="<?= is_active("assessors.php") ?>">
                <span class="icon">👨‍🏫</span>
                <span class="link-text">Assessors</span>
            </a>
        </li>
        <li>
            <a href="internships.php" class="<?= is_active("internships.php") ?>">
                <span class="icon">💼</span>
                <span class="link-text">Internships</span>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="settings.php" class="<?= is_active("settings.php") ?>">
                <span class="icon">⚙️</span>
                <span class="link-text">Settings</span>
            </a>
        </li>
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            // Check saved preference
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                sidebar.classList.add('collapsed');
            }
            
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                // Save preference
                if (sidebar.classList.contains('collapsed')) {
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }
            });
        });
    </script>
</aside>
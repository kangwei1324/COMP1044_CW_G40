<header class="top-nav">
    <div class="user-profile">
        Welcome, <?= htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?>
    </div>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>
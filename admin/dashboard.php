<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include '../components/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header_admin.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Admin Dashboard</h1>
                </div>

                <div class="card">
                    <h2>System Overview</h2>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Welcome to the Internship Result Management System. Use the sidebar to navigate the different management modules.</p>
                    
                    <div class="stats-grid">
                        <!-- Mock Stat Cards -->
                        <div class="stat-card stat-card-primary">
                            <h3 class="stat-label">Total Students</h3>
                            <p class="stat-value">124</p>
                        </div>
                        <div class="stat-card stat-card-success">
                            <h3 class="stat-label">Assessors</h3>
                            <p class="stat-value">18</p>
                        </div>
                        <div class="stat-card stat-card-warning">
                            <h3 class="stat-label">Pending Evaluations</h3>
                            <p class="stat-value">45</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

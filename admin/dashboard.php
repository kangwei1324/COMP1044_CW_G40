<?php include '../includes/auth_check.php' ?>

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
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <!-- Mock Stat Cards -->
                        <div style="background: #eff6ff; padding: 20px; border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            <h3 style="color: var(--primary-color);">Total Students</h3>
                            <p style="font-size: 24px; font-weight: bold; margin-top: 10px;">124</p>
                        </div>
                        <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 4px solid var(--success-color);">
                            <h3 style="color: var(--success-color);">Assessors</h3>
                            <p style="font-size: 24px; font-weight: bold; margin-top: 10px;">18</p>
                        </div>
                        <div style="background: #fffbeb; padding: 20px; border-radius: 8px; border-left: 4px solid var(--warning-color);">
                            <h3 style="color: var(--warning-color);">Pending Evaluations</h3>
                            <p style="font-size: 24px; font-weight: bold; margin-top: 10px;">45</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

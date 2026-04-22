<?php
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    function get_count($conn, $sql) {
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_row();
            return $row[0];
        }
        return 0;
    }

    $student_stmt = "SELECT COUNT(*) FROM student";
    $assessor_stmt = "SELECT COUNT(*) FROM user WHERE role IN ('lecturer', 'industry_supervisor')";
    $pending_evals_stmt = "SELECT COUNT(*) FROM internships i WHERE (SELECT COUNT(*) FROM assessment a WHERE i.internship_id = a.internship_id) < 2";
    $student_count = get_count($conn, $student_stmt);
    $assessor_count = get_count($conn, $assessor_stmt);
    $pending_evals_count = get_count($conn, $pending_evals_stmt);

    
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
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Admin Dashboard</h1>
                </div>

                <div class="card">
                    <h2>System Overview</h2>
                    <p class="card-description">Welcome to the Internship Result Management System. Use the sidebar to navigate the different management modules.</p>
                    
                    <div class="stats-grid">
                        <!-- Mock Stat Cards -->
                        <div class="stat-card stat-card-primary">
                            <h3 class="stat-label">Total Students</h3>
                            <p class="stat-value"><?= htmlspecialchars($student_count) ?></p>
                        </div>
                        <div class="stat-card stat-card-success">
                            <h3 class="stat-label">Assessors</h3>
                            <p class="stat-value"><?= htmlspecialchars($assessor_count) ?></p>
                        </div>
                        <div class="stat-card stat-card-warning">
                            <h3 class="stat-label">Internships Pending Evaluations</h3>
                            <p class="stat-value"><?= htmlspecialchars($pending_evals_count) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

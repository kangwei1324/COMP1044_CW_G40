<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessor Dashboard - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- sidebar -->
        <?php include '../components/sidebar_assessor.php' ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
            <?php include '../components/header_assessor.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Assigned Students</h1>
                </div>

                <div class="card">
                    <h2>Your Evaluations</h2>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Select a student from your assigned list to begin the evaluation process.</p>
                    
                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">Student ID</th>
                                    <th class="table-header-cell">Name</th>
                                    <th class="table-header-cell">Programme</th>
                                    <th class="table-header-cell">Company</th>
                                    <th class="table-header-cell">Status</th>
                                    <th class="table-header-cell">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-body-row">
                                    <td class="table-cell">STU1001</td>
                                    <td class="table-cell">John Doe</td>
                                    <td class="table-cell">Computer Science</td>
                                    <td class="table-cell">TechCorp Inc.</td>
                                    <td class="table-cell">
                                        <span class="badge badge-warning">Pending</span>
                                    </td>
                                    <td class="table-cell">
                                        <a href="evaluate.php?student_id=STU1001" class="btn btn-primary" style="padding: 8px 16px; width: auto; font-size: 13px;">Evaluate</a>
                                    </td>
                                </tr>
                                <tr class="table-body-row">
                                    <td class="table-cell">STU1002</td>
                                    <td class="table-cell">Jane Smith</td>
                                    <td class="table-cell">Information Tech</td>
                                    <td class="table-cell">Cloud Solutions Ltd</td>
                                    <td class="table-cell">
                                        <span class="badge badge-success">Completed</span>
                                    </td>
                                    <td class="table-cell">
                                        <a href="view_result.php?student_id=STU1002" class="action-edit" style="font-weight: 600; font-size: 14px;">View Result</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

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
                    
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                    <th style="padding: 12px 16px; font-weight: 600;">Student ID</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Name</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Programme</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Company</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Status</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 16px;">STU1001</td>
                                    <td style="padding: 16px;">John Doe</td>
                                    <td style="padding: 16px;">Computer Science</td>
                                    <td style="padding: 16px;">TechCorp Inc.</td>
                                    <td style="padding: 16px;">
                                        <span style="background-color: #fffbeb; color: #b45309; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600;">Pending</span>
                                    </td>
                                    <td style="padding: 16px;">
                                        <a href="evaluate.html?student_id=STU1001" class="btn btn-primary" style="padding: 8px 16px; display:inline-block; text-decoration:none; font-size: 13px;">Evaluate</a>
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 16px;">STU1002</td>
                                    <td style="padding: 16px;">Jane Smith</td>
                                    <td style="padding: 16px;">Information Tech</td>
                                    <td style="padding: 16px;">Cloud Solutions Ltd</td>
                                    <td style="padding: 16px;">
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600;">Completed</span>
                                    </td>
                                    <td style="padding: 16px;">
                                        <a href="view_result.html?student_id=STU1002" style="color: var(--primary-color); text-decoration: none; font-weight: 600; font-size: 14px;">View Result</a>
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

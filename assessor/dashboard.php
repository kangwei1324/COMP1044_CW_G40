<?php
    // Error display for debugging
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    $required_role = 'assessor';
    include '../includes/auth_check.php';
    include '../config/db.php';
    include '../includes/functions.php';

    $user_id = $_SESSION['user_id'];
    //echo "<h1>Debug: Logged in as User ID: " . $user_id . "</h1>";

    $result = get_student_assesor($conn, $user_id);

    // Evaluation mode
    $edit_mode = false;
    $edit_username = $edit_fullname = $edit_email = "";
    $action = $_POST['action'] ?? '';
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
                    <p class="card-description">Select a student from your assigned list to begin the evaluation process.</p>
                    
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
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                        $status = htmlspecialchars($row['status']);
                                        //$badge_class = ($status === 'Pending') ? 'badge-warning' : 'badge-success';
                                    ?>

                                    <tr class="table-body-row">
                                        <td class="table-cell"><?= htmlspecialchars($row['student_id']) ?></td>
                                        <td class="table-cell"><?= htmlspecialchars($row['student_name']) ?></td>
                                        <td class="table-cell"><?= htmlspecialchars($row['programme_name']) ?></td>
                                        <td class="table-cell"><?= htmlspecialchars($row['company_name']) ?></td>
            
                                        <td class="table-cell">
                                            <?php if ($status === 'Pending'): ?>
                                                <span class = "badge badge-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="table-cell">
                                            <?php if ($status === 'Pending'): ?>
                                                <a href="evaluate.php?student_id=STU1001" class="btn btn-primary btn-auto btn-sm">Evaluate</a>
                                            <?php else: ?>
                                                <a href="view_result.php?student_id=STU1002" class="action-edit font-semibold font-14">View Result</a>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            
                            <!--                            
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
                                        <a href="evaluate.php?student_id=STU1001" class="btn btn-primary btn-auto btn-sm">Evaluate</a>
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
                                        <a href="view_result.php?student_id=STU1002" class="action-edit font-semibold font-14">View Result</a>
                                    </td>
                                </tr>
                            </tbody>
                            -->
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

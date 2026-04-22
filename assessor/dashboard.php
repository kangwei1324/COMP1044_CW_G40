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
    $result = get_student_assessor($conn, $user_id);
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
        <?php include '../components/sidebar.php' ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
            <?php include '../components/header.php'; ?>

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
                                    <th class="table-header-cell">Semester / Year</th>
                                    <th class="table-header-cell">Status</th>
                                    <th class="table-header-cell">Action</th>
                                </tr>
                            </thead>

                            
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php
                                            $status = htmlspecialchars($row['status']);
                                            $student_id = htmlspecialchars($row['student_id']);
                                            $internship_id = htmlspecialchars($row['internship_id']);
                                        ?>

                                        <tr class="table-body-row">
                                            <td class="table-cell"><?= $student_id ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['programme_name']) ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['company_name']) ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['semester']) . " / " . htmlspecialchars($row['internship_year']) ?></td>
                
                                            <td class="table-cell">
                                                <?php if ($status === 'Pending'): ?>
                                                    <span class="badge badge-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Completed</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="table-cell">
                                                <?php if ($status === 'Pending'): ?>
                                                    <a href="evaluate.php?internship_id=<?= $internship_id ?>&student_id=<?= $student_id ?>" class="btn btn-primary btn-auto btn-sm">Evaluate</a>
                                                <?php else: ?>
                                                    <a href="view_result.php?internship_id=<?= $internship_id ?>&student_id=<?= $student_id ?>" class="action-edit font-semibold font-14">View Result</a>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="table-cell text-center" style="padding: 40px;">
                                            <p class="text-muted">No students assigned for evaluation.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

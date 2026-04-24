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
    
    // Pagination State
    $limit  = 10;
    $page   = (int) ($_GET['page'] ?? 1);
    if ($page < 1) $page = 1;
    $total_students = count_student_assessor($conn, $user_id);
    $total_pages    = ceil($total_students / $limit);
    if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

    $offset = ($page - 1) * $limit;

    $result = get_student_assessor_paged($conn, $user_id, $limit, $offset);
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

                <?php if (isset($_GET['success']) && $_GET['success'] === 'evaluated'): ?>
                    <div class="alert alert-success">Evaluation submitted successfully!</div>
                <?php endif; ?>

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
                                            $student_name = htmlspecialchars($row['student_name']);
                                            $internship_id = htmlspecialchars($row['internship_id']);
                                        ?>

                                        <tr class="table-body-row">
                                            <td class="table-cell"><?= $student_id ?></td>
                                            <td class="table-cell"><?= $student_name ?></td>
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
                                                    <a href="evaluate.php?internship_id=<?= $internship_id ?>&student_id=<?= $student_id ?>&student_name=<?= $student_name ?>" class="btn btn-primary btn-auto btn-sm">Evaluate</a>
                                                <?php else: ?>
                                                    <a href="view_result.php?internship_id=<?= $internship_id ?>&student_id=<?= $student_id ?>&student_name=<?= $student_name ?>" class="action-edit font-semibold font-14">View Result</a>
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

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_students) ?> of <?= $total_students ?> assigned students
                            </div>
                            
                            <!-- Prev -->
                            <a href="?page=<?= $page - 1 ?>" 
                               class="pagination-item <?= ($page <= 1) ? 'disabled' : '' ?>"
                               <?= ($page <= 1) ? 'onclick="return false;"' : '' ?>>
                                &laquo; Prev
                            </a>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?= $i ?>" 
                                   class="pagination-item <?= ($i === $page) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <a href="?page=<?= $page + 1 ?>" 
                               class="pagination-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>"
                               <?= ($page >= $total_pages) ? 'onclick="return false;"' : '' ?>>
                                Next &raquo;
                            </a>
                        </div>
                    <?php elseif ($total_students > 0): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing all <?= $total_students ?> assigned students
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

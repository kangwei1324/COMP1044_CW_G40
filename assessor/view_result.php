<?php
    // Error display for debugging
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    $required_role = 'assessor';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    // Safe defaults — prevents undefined variable warnings if no result is found
    $student_name = 'Unknown';
    $student_id   = 'Unknown';

    if (isset($_GET['internship_id'])) {
        $internship_id = (int) $_GET['internship_id'];

    } else {
        // If credentials not found, kick user back to dashboard
        header("Location: dashboard.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $result = get_student_result($conn, $user_id, $internship_id);
    $has_result = false;

    if ($result && $result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $has_result = true;
            $student_id = htmlspecialchars($row['student_id']);
            $student_name = htmlspecialchars($row['student_name']);
            $programme_name = htmlspecialchars($row['programme_name']);
            $company_name = htmlspecialchars($row['company_name']);
            $semester = htmlspecialchars($row['semester']);
            $internship_year = htmlspecialchars($row['internship_year']);

            $task_projects = htmlspecialchars($row['task_projects']);
            $task_projects_comment = htmlspecialchars($row['task_projects_comment']);

            $health_safety = htmlspecialchars($row['health_safety']);
            $health_safety_comment = htmlspecialchars($row['health_safety_comment']);

            $theoretical_knowledge = htmlspecialchars($row['theoretical_knowledge']);
            $theoretical_knowledge_comment = htmlspecialchars($row['theoretical_knowledge_comment']);

            $report_presentation = htmlspecialchars($row['report_presentation']);
            $report_presentation_comment = htmlspecialchars($row['report_presentation_comment']);

            $clarity_of_language = htmlspecialchars($row['clarity_of_language']);
            $clarity_of_language_comment = htmlspecialchars($row['clarity_of_language_comment']);

            $lifelong_learning = htmlspecialchars($row['lifelong_learning']);
            $lifelong_learning_comment = htmlspecialchars($row['lifelong_learning_comment']);

            $project_management = htmlspecialchars($row['project_management']);
            $project_management_comment = htmlspecialchars($row['project_management_comment']);

            $time_management = htmlspecialchars($row['time_management']);
            $time_management_comment = htmlspecialchars($row['time_management_comment']);

            $comments = nl2br(htmlspecialchars($row['overall_comments']));

            $total_marks = htmlspecialchars($row['total_marks']);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Result - University of Nottingham</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <div>
                        <h1 class="page-title"><?= "Evaluation Result: " . $student_name . " (" . $student_id . ")" ?></h1>
                    </div>
                    <div>
                        <?php if ($has_result): ?>
                        <button class="btn btn-secondary btn-auto btn-sm back-link" onclick="window.print()">🖨️ Print Report</button>
                        <?php endif; ?>
                        <button onclick="window.history.back()" class="btn btn-secondary btn-auto btn-sm back-link">&larr; Back</button>
                    </div>
                </div>

                <?php if ($has_result): ?>
                <div class="card">
                    <div class="result-header-grid">
                        <div>
                            <p class="subtitle">Programme</p>
                            <p class="font-medium"><?= $programme_name ?></p>
                        </div>
                        <div>
                            <p class="subtitle">Company & Period</p>
                            <p class="font-medium"><?= $company_name . " (" . $semester . ", " . $internship_year . ")" ?></p>
                        </div>
                    </div>

                    <h3 class="mb-20">Marks Breakdown</h3>
                    
                    <table class="irms-result-table">
                        <tbody>
                            <tr>
                                <td>1. Undertaking Tasks/Projects (10%)</td>
                                <td class="text-right font-semibold"><?= $task_projects ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $task_projects_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>2. Health and Safety (10%)</td>
                                <td class="text-right font-semibold"><?= $health_safety ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $health_safety_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>3. Connectivity and Theoretical Knowledge (10%)</td>
                                <td class="text-right font-semibold"><?= $theoretical_knowledge ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $theoretical_knowledge_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>4. Presentation of Report (15%)</td>
                                <td class="text-right font-semibold"><?= $report_presentation ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $report_presentation_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>5. Clarity of Language (10%)</td>
                                <td class="text-right font-semibold"><?= $clarity_of_language ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $clarity_of_language_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>6. Lifelong Learning Activities (15%)</td>
                                <td class="text-right font-semibold"><?= $lifelong_learning ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $lifelong_learning_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>7. Project Management (15%)</td>
                                <td class="text-right font-semibold"><?= $project_management ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $project_management_comment ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>8. Time Management (15%)</td>
                                <td class="text-right font-semibold"><?= $time_management ?></td>
                                <td class="comment-row">
                                    <span class="comment-label">Comments:</span>
                                    <span class="comment-text"><?= $time_management_comment ?></span>
                                </td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Score</td>
                                <td class="text-right"><?= $total_marks ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="feedback-box">
                        <h4>General Comments</h4>
                        <p><?= $comments ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <div class="card">
                        <div class="text-center" style="padding: 40px;">
                            <p class="text-muted">No evaluation record found for this student.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
    include '../config/db.php';
    include '../includes/functions.php';

    // Check if all credentials are in the URL
    if (isset($_GET['internship_id']) && isset($_GET['student_id']) && isset($_GET['student_name'])) {
        $internship_id = htmlspecialchars($_GET['internship_id']);
        $student_id = htmlspecialchars($_GET['student_id']);
        $student_name = htmlspecialchars($_GET['student_name']);

    } else {
        // If credentials not found, kick user back to dashboard
        header("Location: dashboard.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $result = get_student_assessor($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Student - IRMS</title>
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
                        <!-- Print the title: Student Name (student id) using php -->
                        <h1 class="page-title"><?= $student_name . " (" . $student_id . ")" ?></h1>
                    </div>
                    <a href="dashboard.php" class="btn btn-secondary btn-auto btn-sm back-link">&larr; Back to Dashboard</a>
                </div>

                <div class="card">
                    <h3 class="card-header-sep">Internship Assessment Form</h3>
                    <p class="card-description mb-24">Please enter the scores based on the predefined criteria. The system will automatically calculate the final marks.</p>

                    <form id="evaluationForm" method="post" action="submit_eval.php">
                        <!-- Include the internship_id and student_id as hidden for processing purpose -->
                        <input type="hidden" name="internship_id" value="<?= $internship_id ?>">
                        <input type="hidden" name="student_id" value="<?= $student_id ?>">
                        <div class="score-row">
                            <label class="font-medium">1. Undertaking Tasks/Projects (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_tasks" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_tasks" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">2. Health and Safety Requirements at Workplace (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_health" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_health" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">3. Connectivity and Use of Theoretical Knowledge (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_knowledge" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_knowledge" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">4. Presentation of the Report as Written Document (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_report" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_report" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">5. Clarity of Language and Illustration (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_language" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_language" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">6. Lifelong Learning Activities (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_lifelong" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_lifelong" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">7. Project Management (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_project" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_project" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>
                        <div class="score-row score-row-last">
                            <label class="font-medium">8. Time Management (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_time" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_time" rows="2" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>

                        <!-- Auto Calculated Total -->
                        <div class="total-score-row">
                            <label class="total-score-label">Total Calculated Score (100%)</label>
                            <input type="text" id="total_score" class="form-control total-score-input" readonly value="0%">
                        </div>

                        <!-- Qualitative Feedback -->
                        <div class="form-group">
                            <label>General Comments & Feedback</label>
                            <textarea class="form-control" name="comments" rows="5" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>

                        <div class="mt-2 text-right">
                            <button type="submit" name="submit_evaluation" class="btn btn-primary btn-auto">Submit Evaluation</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Client-side Validation Logic -->
    <script src="../assets/js/validation.js"></script>
</body>
</html>

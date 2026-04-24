<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
    include '../config/db.php';
    include '../includes/functions.php';

    $errors = [];
    $user_id = $_SESSION['user_id'];

    // Retrieve state from GET or POST
    $internship_id = $_GET['internship_id'] ?? $_POST['internship_id'] ?? null;
    $student_id = $_GET['student_id'] ?? $_POST['student_id'] ?? null;
    $student_name = $_GET['student_name'] ?? $_POST['student_name'] ?? null;

    if (!$internship_id || !$student_id || !$student_name) {
        header("Location: dashboard.php");
        exit;
    }

    // Security Check: Ensure this internship is assigned to THIS assessor
    $is_assigned = false;
    $assigned_result = get_student_assessor($conn, $user_id);
    if ($assigned_result) {
        while ($row = $assigned_result->fetch_assoc()) {
            if ($row['internship_id'] == $internship_id) {
                $is_assigned = true;
                break;
            }
        }
    }

    if (!$is_assigned) {
        header("Location: dashboard.php");
        exit;
    }

    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $score_tasks = $_POST['score_tasks'] ?? '';
        $comment_tasks = $_POST['comment_tasks'] ?? '';

        $score_health = $_POST['score_health'] ?? '';
        $comment_health = $_POST['comment_health'] ?? '';

        $score_knowledge = $_POST['score_knowledge'] ?? '';
        $comment_knowledge = $_POST['comment_knowledge'] ?? '';

        $score_report = $_POST['score_report'] ?? '';
        $comment_report = $_POST['comment_report'] ?? '';

        $score_language = $_POST['score_language'] ?? '';
        $comment_language = $_POST['comment_language'] ?? '';

        $score_lifelong = $_POST['score_lifelong'] ?? '';
        $comment_lifelong = $_POST['comment_lifelong'] ?? '';

        $score_project = $_POST['score_project'] ?? '';
        $comment_project = $_POST['comment_project'] ?? '';

        $score_time = $_POST['score_time'] ?? '';
        $comment_time = $_POST['comment_time'] ?? '';

        $comments = $_POST['comments'] ?? '';

        // Server-side Validation
        if (!is_numeric($score_tasks) || $score_tasks < 0 || $score_tasks > 10) $errors[] = "Task Projects score must be between 0 and 10.";
        if (!is_numeric($score_health) || $score_health < 0 || $score_health > 10) $errors[] = "Health & Safety score must be between 0 and 10.";
        if (!is_numeric($score_knowledge) || $score_knowledge < 0 || $score_knowledge > 10) $errors[] = "Theoretical Knowledge score must be between 0 and 10.";
        if (!is_numeric($score_report) || $score_report < 0 || $score_report > 15) $errors[] = "Report Presentation score must be between 0 and 15.";
        if (!is_numeric($score_language) || $score_language < 0 || $score_language > 10) $errors[] = "Clarity of Language score must be between 0 and 10.";
        if (!is_numeric($score_lifelong) || $score_lifelong < 0 || $score_lifelong > 15) $errors[] = "Lifelong Learning score must be between 0 and 15.";
        if (!is_numeric($score_project) || $score_project < 0 || $score_project > 15) $errors[] = "Project Management score must be between 0 and 15.";
        if (!is_numeric($score_time) || $score_time < 0 || $score_time > 15) $errors[] = "Time Management score must be between 0 and 15.";

        if (empty($errors)) {
            if (submit_evaluation(
                $conn, $user_id, $internship_id,
                $score_tasks, $comment_tasks, $score_health, $comment_health,
                $score_knowledge, $comment_knowledge, $score_report, $comment_report,
                $score_language, $comment_language, $score_lifelong, $comment_lifelong,
                $score_project, $comment_project, $score_time, $comment_time, $comments
            )) {
                header("Location: dashboard.php?success=evaluated");
                exit();
            } else {
                $errors[] = "System error: Could not submit evaluation. You might have already evaluated this student.";
            }
        }
    }

    $internship_id_safe = htmlspecialchars($internship_id);
    $student_id_safe = htmlspecialchars($student_id);
    $student_name_safe = htmlspecialchars($student_name);
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
        <?php include '../components/sidebar.php' ?>

        <main class="main-content">
            <?php include '../components/header.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <div>
                        <h1 class="page-title"><?= $student_name_safe . " (" . $student_id_safe . ")" ?></h1>
                    </div>
                    <a href="dashboard.php" class="btn btn-secondary btn-auto btn-sm back-link">&larr; Back to Dashboard</a>
                </div>

                <?php if (!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="card">
                    <h3 class="card-header-sep">Internship Assessment Form</h3>
                    <p class="card-description mb-24">Please enter the scores based on the predefined criteria. The system will automatically calculate the final marks.</p>

                    <form id="evaluationForm" method="post" action="evaluate.php">
                        <input type="hidden" name="internship_id" value="<?= $internship_id_safe ?>">
                        <input type="hidden" name="student_id" value="<?= $student_id_safe ?>">
                        <input type="hidden" name="student_name" value="<?= $student_name_safe ?>">
                        
                        <div class="score-row">
                            <label class="font-medium">1. Undertaking Tasks/Projects (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_tasks" value="<?= htmlspecialchars($_POST['score_tasks'] ?? '') ?>" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_tasks" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_tasks'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">2. Health and Safety Requirements at Workplace (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_health" value="<?= htmlspecialchars($_POST['score_health'] ?? '') ?>" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_health" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_health'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">3. Connectivity and Use of Theoretical Knowledge (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_knowledge" value="<?= htmlspecialchars($_POST['score_knowledge'] ?? '') ?>" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_knowledge" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_knowledge'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">4. Presentation of the Report as Written Document (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_report" value="<?= htmlspecialchars($_POST['score_report'] ?? '') ?>" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_report" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_report'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">5. Clarity of Language and Illustration (Max 10%)</label>
                            <input type="number" step="1" min="0" max="10" class="form-control score-input" name="score_language" value="<?= htmlspecialchars($_POST['score_language'] ?? '') ?>" required placeholder="0-10">
                            <textarea class="form-control comment-row" name="comment_language" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_language'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">6. Lifelong Learning Activities (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_lifelong" value="<?= htmlspecialchars($_POST['score_lifelong'] ?? '') ?>" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_lifelong" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_lifelong'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row">
                            <label class="font-medium">7. Project Management (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_project" value="<?= htmlspecialchars($_POST['score_project'] ?? '') ?>" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_project" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_project'] ?? '') ?></textarea>
                        </div>
                        <div class="score-row score-row-last">
                            <label class="font-medium">8. Time Management (Max 15%)</label>
                            <input type="number" step="1" min="0" max="15" class="form-control score-input" name="score_time" value="<?= htmlspecialchars($_POST['score_time'] ?? '') ?>" required placeholder="0-15">
                            <textarea class="form-control comment-row" name="comment_time" rows="2" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comment_time'] ?? '') ?></textarea>
                        </div>

                        <div class="total-score-row">
                            <label class="total-score-label">Total Calculated Score (100%)</label>
                            <input type="text" id="total_score" class="form-control total-score-input" readonly value="0%">
                        </div>

                        <div class="form-group">
                            <label>General Comments & Feedback</label>
                            <textarea class="form-control" name="comments" rows="5" placeholder="Provide qualitative comments to justify the scores..."><?= htmlspecialchars($_POST['comments'] ?? '') ?></textarea>
                        </div>

                        <div class="mt-2 text-right">
                            <button type="submit" name="submit_evaluation" class="btn btn-primary btn-auto">Submit Evaluation</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    console.log("LOG: Script has started!");

    document.addEventListener('input', function (event) {
        // Check if the user is typing in a textarea
        if (event.target.tagName.toLowerCase() === 'textarea') {
            const key = 'FINAL_TEST_' + event.target.name;
            const val = event.target.value;
            localStorage.setItem(key, val);
            console.log("LOG: Saved " + key);
        }
    });

    window.addEventListener('load', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(el => {
            const saved = localStorage.getItem('FINAL_TEST_' + el.name);
            if (saved) {
                el.value = saved;
                console.log("LOG: Restored " + el.name);
            }
        });
    });
</script>
</body>
</html>
<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
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
        <?php include '../components/sidebar_assessor.php' ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
            <?php include '../components/header_assessor.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <div>
                        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
                        <h1 class="page-title">Evaluate Student: John Doe (STU1001)</h1>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-header-sep">Internship Assessment Form</h3>
                    <p class="card-description mb-24">Please enter the scores based on the predefined criteria. The system will automatically calculate the final marks.</p>

                    <form id="evaluationForm">
                        <div class="score-row">
                            <label class="font-medium">1. Undertaking Tasks/Projects (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_tasks" required placeholder="0-10">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">2. Health and Safety Requirements at Workplace (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_health" required placeholder="0-10">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">3. Connectivity and Use of Theoretical Knowledge (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_knowledge" required placeholder="0-10">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">4. Presentation of the Report as Written Document (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_report" required placeholder="0-15">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">5. Clarity of Language and Illustration (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_language" required placeholder="0-10">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">6. Lifelong Learning Activities (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_lifelong" required placeholder="0-15">
                        </div>
                        <div class="score-row">
                            <label class="font-medium">7. Project Management (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_project" required placeholder="0-15">
                        </div>
                        <div class="score-row score-row-last">
                            <label class="font-medium">8. Time Management (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_time" required placeholder="0-15">
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
                            <button type="submit" class="btn btn-primary btn-auto">Submit Evaluation</button>
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

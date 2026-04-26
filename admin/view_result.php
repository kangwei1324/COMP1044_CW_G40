<?php
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    if (isset($_GET['internship_id'])) {
        $internship_id = (int) $_GET['internship_id'];
    } else {
        header("Location: internships.php");
        exit;
    }

    $result = get_all_student_results($conn, $internship_id);
    $evaluations = [];
    $student_info = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (empty($student_info)) {
                $student_info = [
                    'student_name' => $row['student_name'],
                    'student_id' => $row['student_id'],
                    'programme_name' => $row['programme_name'],
                    'company_name' => $row['company_name'],
                    'semester' => $row['semester'],
                    'internship_year' => $row['internship_year']
                ];
            }
            $evaluations[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Combined Results - University of Nottingham</title>
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
                        <h1 class="page-title">
                            <?= !empty($student_info) ? "Combined Evaluation Result: " . htmlspecialchars($student_info['student_name']) . " (" . htmlspecialchars($student_info['student_id']) . ")" : "Evaluation Results" ?>
                        </h1>
                    </div>
                    <div class="header-actions">
                        <?php if (!empty($evaluations)): ?>
                        <button class="btn btn-secondary btn-auto btn-sm" onclick="window.print()">🖨️ Print Report</button>
                        <?php endif; ?>
                        <button onclick="window.location.href='internships.php'" class="btn btn-secondary btn-auto btn-sm">&larr; Back to Internships</button>
                    </div>
                </div>

                <?php if (!empty($evaluations)): ?>
                    <div class="card mb-20">
                        <div class="result-header-grid">
                            <div>
                                <p class="subtitle">Programme</p>
                                <p class="font-medium"><?= htmlspecialchars($student_info['programme_name']) ?></p>
                            </div>
                            <div>
                                <p class="subtitle">Company & Period</p>
                                <p class="font-medium"><?= htmlspecialchars($student_info['company_name']) . " (" . htmlspecialchars($student_info['semester']) . ", " . htmlspecialchars($student_info['internship_year']) . ")" ?></p>
                            </div>
                            <?php if(count($evaluations) == 2): ?>
                                <div>
                                    <p class="subtitle">Total Combined Score</p>
                                    <p class="font-medium text-success" style="font-size: 1.25rem;">
                                        <?= htmlspecialchars($evaluations[0]['total_marks'] + $evaluations[1]['total_marks']) ?> / 200
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php foreach ($evaluations as $eval): ?>
                        <div class="card mb-20">
                            <h3 class="mb-20">
                                Evaluation by <?= htmlspecialchars($eval['assessor_name']) ?> 
                                <span class="badge <?= $eval['assessor_role'] == 'lecturer' ? 'badge-primary' : 'badge-success' ?>" style="vertical-align: middle; margin-left: 10px;">
                                    <?= ucwords(str_replace('_', ' ', $eval['assessor_role'])) ?>
                                </span>
                            </h3>
                            
                            <table class="irms-result-table">
                                <tbody>
                                    <tr>
                                        <td>1. Undertaking Tasks/Projects (10%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['task_projects']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['task_projects_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2. Health and Safety (10%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['health_safety']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['health_safety_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3. Connectivity and Theoretical Knowledge (10%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['theoretical_knowledge']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['theoretical_knowledge_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4. Presentation of Report (15%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['report_presentation']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['report_presentation_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5. Clarity of Language (10%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['clarity_of_language']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['clarity_of_language_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6. Lifelong Learning Activities (15%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['lifelong_learning']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['lifelong_learning_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7. Project Management (15%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['project_management']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['project_management_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8. Time Management (15%)</td>
                                        <td class="text-right font-semibold"><?= htmlspecialchars($eval['time_management']) ?></td>
                                        <td class="comment-row">
                                            <span class="comment-label">Comments:</span>
                                            <span class="comment-text"><?= htmlspecialchars($eval['time_management_comment']) ?></span>
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td>Score given by <?= htmlspecialchars($eval['assessor_name']) ?></td>
                                        <td class="text-right"><?= htmlspecialchars($eval['total_marks']) ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="feedback-box" style="margin-top: 15px;">
                                <h4>General Comments</h4>
                                <p><?= nl2br(htmlspecialchars($eval['overall_comments'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="text-center" style="padding: 40px;">
                            <p class="text-muted">No evaluation record found for this internship.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Result - IRMS</title>
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
                        <button onclick="window.history.back()" class="back-link btn-link-reset">&larr; Back</button>
                        <h1 class="page-title">Evaluation Result: Jane Smith (STU1002)</h1>
                    </div>
                    <button class="btn btn-secondary btn-auto btn-sm" onclick="window.print()">🖨️ Print Report</button>
                </div>

                <div class="card">
                    <div class="result-header-grid">
                        <div>
                            <p class="subtitle">Programme</p>
                            <p class="font-medium">Bachelor of Information Technology</p>
                        </div>
                        <div>
                            <p class="subtitle">Company & Period</p>
                            <p class="font-medium">Cloud Solutions Ltd (Sem 2, 2024)</p>
                        </div>
                    </div>

                    <h3 class="mb-20">Marks Breakdown</h3>
                    
                    <table class="irms-result-table">
                        <tbody>
                            <tr>
                                <td>1. Undertaking Tasks/Projects (10%)</td>
                                <td class="text-right font-semibold">8.5</td>
                            </tr>
                            <tr>
                                <td>2. Health and Safety (10%)</td>
                                <td class="text-right font-semibold">9.0</td>
                            </tr>
                            <tr>
                                <td>3. Connectivity and Theoretical Knowledge (10%)</td>
                                <td class="text-right font-semibold">8.0</td>
                            </tr>
                            <tr>
                                <td>4. Presentation of Report (15%)</td>
                                <td class="text-right font-semibold">13.0</td>
                            </tr>
                            <tr>
                                <td>5. Clarity of Language (10%)</td>
                                <td class="text-right font-semibold">9.0</td>
                            </tr>
                            <tr>
                                <td>6. Lifelong Learning Activities (15%)</td>
                                <td class="text-right font-semibold">12.5</td>
                            </tr>
                            <tr>
                                <td>7. Project Management (15%)</td>
                                <td class="text-right font-semibold">13.5</td>
                            </tr>
                            <tr>
                                <td>8. Time Management (15%)</td>
                                <td class="text-right font-semibold">14.0</td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Score</td>
                                <td class="text-right">87.5%</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="feedback-box">
                        <h4>General Comments</h4>
                        <p>Jane demonstrated exceptional technical skills during her internship at Cloud Solutions Ltd. Her ability to execute project management strategies while keeping code logic clean was highly impressive. Excellent time management.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

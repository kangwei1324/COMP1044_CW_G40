<?php include '../includes/auth_check.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Result - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Print Styles */
        @media print {
            .sidebar, .top-nav, .logout-btn, .page-header button {
                display: none !important;
            }
            .main-content { margin: 0; padding: 0; }
            .card { box-shadow: none; border: 1px solid #000; }
        }
    </style>
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
                        <button onclick="window.history.back()" style="background:none; border:none; color: var(--text-muted); text-decoration: underline; font-size: 14px; margin-bottom: 8px; display: inline-block; cursor: pointer;">&larr; Back</button>
                        <h1 class="page-title">Evaluation Result: Jane Smith (STU1002)</h1>
                    </div>
                    <button class="btn btn-primary" style="width: auto; background: white; border: 1px solid #e2e8f0; color: #0f172a;" onclick="window.print()">🖨️ Print Report</button>
                </div>

                <div class="card">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
                        <div>
                            <p style="color: var(--text-muted); font-size: 13px;">Programme</p>
                            <p style="font-weight: 500;">Bachelor of Information Technology</p>
                        </div>
                        <div>
                            <p style="color: var(--text-muted); font-size: 13px;">Company & Period</p>
                            <p style="font-weight: 500;">Cloud Solutions Ltd (Sem 2, 2024)</p>
                        </div>
                    </div>

                    <h3 style="margin-bottom: 20px;">Marks Breakdown</h3>
                    
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <tbody>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">1. Undertaking Tasks/Projects (10%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">8.5</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">2. Health and Safety (10%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">9.0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">3. Connectivity and Theoretical Knowledge (10%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">8.0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">4. Presentation of Report (15%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">13.0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">5. Clarity of Language (10%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">9.0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">6. Lifelong Learning Activities (15%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">12.5</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">7. Project Management (15%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">13.5</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 0;">8. Time Management (15%)</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: 600;">14.0</td>
                            </tr>
                            <tr>
                                <td style="padding: 16px 0; font-weight: 700; color: var(--primary-color); font-size: 18px;">Total Score</td>
                                <td style="padding: 16px 0; text-align: right; font-weight: 700; color: var(--primary-color); font-size: 18px;">87.5%</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: 8px;">General Comments</h4>
                        <p style="color: var(--text-dark); line-height: 1.6;">Jane demonstrated exceptional technical skills during her internship at Cloud Solutions Ltd. Her ability to execute project management strategies while keeping code logic clean was highly impressive. Excellent time management.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

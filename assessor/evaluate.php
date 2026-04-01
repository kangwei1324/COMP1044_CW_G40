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
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">IRMS Assessor</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Dashboard / List</a></li>
                <li><a href="settings.php">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-nav">
                <div class="user-profile">
                    Welcome, Assessor
                </div>
                <a href="../index.html" class="logout-btn">Logout</a>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div>
                        <a href="dashboard.html" style="color: var(--text-muted); text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">&larr; Back to Dashboard</a>
                        <h1 class="page-title">Evaluate Student: John Doe (STU1001)</h1>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">Internship Assessment Form</h3>
                    <p style="color: var(--text-muted); margin-bottom: 24px;">Please enter the scores based on the predefined criteria. The system will automatically calculate the final marks.</p>

                    <form id="evaluationForm">
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">1. Undertaking Tasks/Projects (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_tasks" required placeholder="0-10">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">2. Health and Safety Requirements at Workplace (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_health" required placeholder="0-10">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">3. Connectivity and Use of Theoretical Knowledge (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_knowledge" required placeholder="0-10">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">4. Presentation of the Report as Written Document (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_report" required placeholder="0-15">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">5. Clarity of Language and Illustration (Max 10%)</label>
                            <input type="number" step="0.1" min="0" max="10" class="form-control score-input" name="score_language" required placeholder="0-10">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">6. Lifelong Learning Activities (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_lifelong" required placeholder="0-15">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 16px;">
                            <label style="font-weight: 500;">7. Project Management (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_project" required placeholder="0-15">
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 24px;">
                            <label style="font-weight: 500;">8. Time Management (Max 15%)</label>
                            <input type="number" step="0.1" min="0" max="15" class="form-control score-input" name="score_time" required placeholder="0-15">
                        </div>

                        <!-- Auto Calculated Total -->
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center; margin-bottom: 30px;">
                            <label style="font-weight: 700; font-size: 18px; color: var(--primary-color);">Total Calculated Score (100%)</label>
                            <input type="text" id="total_score" class="form-control" readonly style="font-size: 18px; font-weight: bold; background-color: #f1f5f9; color: var(--primary-color);" value="0%">
                        </div>

                        <!-- Qualitative Feedback -->
                        <div class="form-group">
                            <label>General Comments & Feedback</label>
                            <textarea class="form-control" name="comments" rows="5" placeholder="Provide qualitative comments to justify the scores..."></textarea>
                        </div>

                        <div style="margin-top: 30px; text-align: right;">
                            <button type="submit" class="btn btn-primary" style="width: auto; padding: 14px 32px; font-size: 16px;">Submit Evaluation</button>
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

<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include '../components/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header_admin.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Manage Student Profiles</h1>
                    <button class="btn btn-primary" style="width: auto;" onclick="document.getElementById('addForm').style.display='block'">+ Add Student</button>
                </div>

                <!-- Add Form (Hidden by default) -->
                <div class="card" id="addForm" style="display: none; background: #f8fafc; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 20px;">Register New Student</h3>
                    <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Student ID (Matric No.)</label>
                            <input type="text" class="form-control" placeholder="e.g. STU1001">
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" placeholder="e.g. John Doe">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Programme</label>
                            <select class="form-control">
                                <option value="">Select a Programme...</option>
                                <option value="1">Bachelor of Computer Science (Hons)</option>
                                <option value="2">Bachelor of Information Technology</option>
                            </select>
                        </div>
                        <div style="grid-column: span 2; display: flex; gap: 12px; justify-content: flex-end;">
                            <button type="button" class="btn" style="width: auto; background: white; border: 1px solid #e2e8f0; color: #64748b;" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="button" class="btn btn-primary" style="width: auto;">Save Student</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <!-- Search Bar Mockup -->
                    <div style="margin-bottom: 20px; display: flex; gap: 12px;">
                        <input type="text" class="form-control" placeholder="Search by Student ID or Name..." style="max-width: 300px;">
                        <button class="btn btn-primary" style="width: auto; padding: 12px 16px;">Search</button>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                    <th style="padding: 12px 16px; font-weight: 600;">Student ID</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Name</th>
                                    <th style="padding: 12px 16px; font-weight: 600;">Programme</th>
                                    <th style="padding: 12px 16px; font-weight: 600; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 16px; font-weight: 600; color: var(--text-dark);">STU1001</td>
                                    <td style="padding: 16px;">John Doe</td>
                                    <td style="padding: 16px; color: var(--text-muted);">Bachelor of Computer Science</td>
                                    <td style="padding: 16px; text-align: right;">
                                        <a href="#" style="color: var(--primary-color); margin-right: 12px; text-decoration: none; font-size: 14px; font-weight: 500;">Edit</a>
                                        <a href="#" style="color: var(--danger-color); text-decoration: none; font-size: 14px; font-weight: 500;">Delete</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

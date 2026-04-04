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

                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">Student ID</th>
                                    <th class="table-header-cell">Name</th>
                                    <th class="table-header-cell">Programme</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-body-row">
                                    <td class="table-cell-medium" style="color: var(--text-dark);">STU1001</td>
                                    <td class="table-cell">John Doe</td>
                                    <td class="table-cell-muted">Bachelor of Computer Science</td>
                                    <td class="table-actions-cell">
                                        <a href="#" class="action-edit">Edit</a>
                                        <a href="#" class="action-revoke">Delete</a>
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

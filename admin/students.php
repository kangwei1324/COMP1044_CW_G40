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
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add Student</button>
                </div>

                <!-- Add Form (Hidden by default) -->
                <div class="card collapse-form" id="addForm">
                    <h3 class="mb-20">Register New Student</h3>
                    <form class="form-grid">
                        <div class="form-group">
                            <label>Student ID (Matric No.)</label>
                            <input type="text" class="form-control" placeholder="e.g. STU1001">
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" placeholder="e.g. John Doe">
                        </div>
                        <div class="form-group form-span-2">
                            <label>Programme</label>
                            <select class="form-control">
                                <option value="">Select a Programme...</option>
                                <option value="1">Bachelor of Computer Science (Hons)</option>
                                <option value="2">Bachelor of Information Technology</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="button" class="btn btn-primary btn-auto">Save Student</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="search-container">
                        <input type="text" class="form-control max-w-300" placeholder="Search by Student ID or Name...">
                        <button class="btn btn-primary btn-auto btn-sm">Search</button>
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
                                    <td class="table-cell-medium text-dark">STU1001</td>
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

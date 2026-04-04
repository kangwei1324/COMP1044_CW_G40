<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Programmes - IRMS</title>
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
                    <h1 class="page-title">Manage Academic Programmes</h1>
                    <button class="btn btn-primary" style="width: auto;" onclick="document.getElementById('addForm').style.display='block'">+ Add New Programme</button>
                </div>

                <!-- Add Form (Hidden by default) -->
                <div class="card" id="addForm" style="display: none; background: #f8fafc; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 16px;">Add New Programme</h3>
                    <form style="display: flex; gap: 16px; align-items: flex-end;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label>Programme Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Bachelor of Computer Science">
                        </div>
                        <button type="button" class="btn btn-primary" style="width: auto;">Save</button>
                        <button type="button" class="btn" style="width: auto; background: white; border: 1px solid #e2e8f0; color: #64748b;" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">ID</th>
                                    <th class="table-header-cell">Programme Name</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-body-row">
                                    <td class="table-cell-muted">1</td>
                                    <td class="table-cell-medium">Bachelor of Computer Science (Hons)</td>
                                    <td class="table-actions-cell">
                                        <a href="#" class="action-edit">Edit</a>
                                        <a href="#" class="action-revoke">Delete</a>
                                    </td>
                                </tr>
                                <tr class="table-body-row">
                                    <td class="table-cell-muted">2</td>
                                    <td class="table-cell-medium">Bachelor of Information Technology</td>
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

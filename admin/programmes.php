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
                    <button class="btn btn-primary w-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add New Programme</button>
                </div>

                <div class="card collapse-form" id="addForm">
                    <h3 class="mb-20">Add New Programme</h3>
                    <form class="display-flex gap-16 align-end">
                        <div class="form-group flex-1 mb-0">
                            <label>Programme Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Bachelor of Computer Science">
                        </div>
                        <button type="button" class="btn btn-primary btn-auto">Save</button>
                        <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
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

<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
    include '../config/db.php';

    $sql = "SELECT * FROM user WHERE role='industry_supervisor' OR role='lecturer'";
    $result = $conn->query($sql);
    
    // while ($row = $result->fetch_assoc()) {
    //     echo"<tr>";
    //     echo"<td>" . $row['user_id'] . "</td>";
    //     echo"</tr>";
    //     echo"<tr>";
    //     echo"<td>" . $row['username'] . "</td>";
    //     echo"</tr>";
    //     echo"<tr>";
    //     echo"<td>" . $row['fullname'] . "</td>";
    //     echo"</tr>";
    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Assessors - IRMS</title>
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
                    <h1 class="page-title">Manage Assessor Accounts</h1>
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add Assessor</button>
                </div>

                <!-- Add Form -->
                <div class="card collapse-form" id="addForm">
                    <h3 class="mb-20">Register New Assessor</h3>
                    <form class="form-grid">
                        <div class="form-group">
                            <label>Username (Login ID)</label>
                            <input type="text" class="form-control" placeholder="e.g. drsmith">
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Dr. Alan Smith">
                        </div>
                        <div class="form-group form-span-2">
                            <label>Temporary Password</label>
                            <input type="password" class="form-control" placeholder="Set a temporary password">
                            <small class="card-description d-block mt-4">Assessors can change this upon their first login in the Settings menu.</small>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="button" class="btn btn-primary btn-auto">Create Account</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">System ID</th>
                                    <th class="table-header-cell">Username</th>
                                    <th class="table-header-cell">Full Name</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-body-row">
                                    <td class="table-cell-muted">U002</td>
                                    <td class="table-cell-medium">assessor_smith</td>
                                    <td class="table-cell">Dr. Alan Smith</td>
                                    <td class="table-actions-cell">
                                        <a href="#" class="action-edit">Edit</a>
                                        <a href="#" class="action-revoke">Revoke Access</a>
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

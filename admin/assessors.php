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
                    <button class="btn btn-primary" style="width: auto;" onclick="document.getElementById('addForm').style.display='block'">+ Add Assessor</button>
                </div>

                <!-- Add Form -->
                <div class="card" id="addForm" style="display: none; background: #f8fafc; border: 1px solid var(--border-color);">
                    <h3 style="margin-bottom: 20px;">Register New Assessor</h3>
                    <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Username (Login ID)</label>
                            <input type="text" class="form-control" placeholder="e.g. drsmith">
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Dr. Alan Smith">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Temporary Password</label>
                            <input type="password" class="form-control" placeholder="Set a temporary password">
                            <small style="color: var(--text-muted); display: block; margin-top: 6px;">Assessors can change this upon their first login in the Settings menu.</small>
                        </div>
                        <div style="grid-column: span 2; display: flex; gap: 12px; justify-content: flex-end;">
                            <button type="button" class="btn" style="width: auto; background: white; border: 1px solid #e2e8f0; color: #64748b;" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="button" class="btn btn-primary" style="width: auto;">Create Account</button>
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

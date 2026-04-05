<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
    include '../config/db.php';

    // Initialize variables
    $errors = [];
    // Only allow user to create assessor roles
    // This also prevents users from using inspect element and adding their own invalid role.
    $allowed_roles = ["industry_supervisor", "lecturer"];
    $success_msg = "";

    // ADD NEW ASSESSOR (CREATE)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // 1. "Clean" the input (Sanitization)
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $fullname = trim($_POST['fullname']);
        $role     = trim($_POST['role']);
        $password = $_POST['password']; // Don't trim passwords as spaces might be intentional

        // 2. Basic Validation
        if (empty($username)) $errors[] = "Username is required.";
        if (empty($email))    $errors[] = "Email is required.";
        if (empty($fullname)) $errors[] = "Full name is required.";
        if (empty($role))     $errors[] = "Please select a role.";
        if (empty($password)) $errors[] = "Temporary password is required.";

        // 3. Advanced Validation
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (!in_array($role, $allowed_roles)) {
            $errors[] = "Invalid role selected";
        }

        // 4. Database Security (Insertion)
        if (empty($errors)) {
            // Hash the password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the statement
            $stmt = $conn->prepare("INSERT INTO user (username, email, password, fullname, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hash, $fullname, $role);

            // Execute and check for errors (like duplicate username/email)
            if ($stmt->execute()) {
                $success_msg = $fullname . " account created successfully!";
            } else {
                if ($conn->errno === 1062) { // 1062 = Duplicate entry
                    $errors[] = "Error: That Username or Email is already registered.";
                } else {
                    $errors[] = "System error: Something went wrong, please try again later.";
                }
            }
            $stmt->close();
        }
    }

    // DELETE ASSESSOR (Revoke Access)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int) $_GET['delete_id']; // Cast to int — kills any injection attempt

        // Guard 1: Prevent deleting yourself
        if ($delete_id === (int) $_SESSION['user_id']) {
            $errors[] = "You cannot revoke your own access.";

        // Guard 2: Prevent deleting any admin account
        } else {
            $check_stmt = $conn->prepare("SELECT role FROM user WHERE user_id = ?");
            $check_stmt->bind_param("i", $delete_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $target_user  = $check_result->fetch_assoc();
            $check_stmt->close();

            if (!$target_user) {
                $errors[] = "User not found.";
            } elseif ($target_user['role'] === 'admin') {
                $errors[] = "Admin accounts cannot be deleted from this page.";
            } else {
                // Safe to delete
                $del_stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
                $del_stmt->bind_param("i", $delete_id);
                if ($del_stmt->execute()) {
                    $success_msg = "Assessor account has been revoked successfully.";
                } else {
                    $errors[] = "System error: Something went wrong, please try again later.";
                }
                $del_stmt->close();
            }
        }
    }

    // DISPLAY ASSESSORS
    // We run this AFTER the Insert/Delete so the table reflects the latest state
    $sql = "SELECT user_id, username, fullname FROM user WHERE role='industry_supervisor' OR role='lecturer' ORDER BY user_id DESC";
    $result = $conn->query($sql);
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
                    
                    <!-- Feedback Messages -->
                    <?php if (!empty($errors)): ?>
                        <?php foreach($errors as $error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($success_msg): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
                    <?php endif; ?>

                    <form action="" method="post" class="form-grid">
                        <div class="form-group">
                            <label for="username">Username (Login ID)</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="e.g. drsmith" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" placeholder="e.g. Dr. Alan Smith" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="e.g. alan12@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Select a Role</option>
                                <option value="industry_supervisor">Industry Supervisor</option>
                                <option value="lecturer">Lecturer</option>
                            </select>
                        </div>
                        <div class="form-group form-span-2">
                            <label for="password">Temporary Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Set a temporary password" required>
                            <small class="card-description d-block mt-4">Assessors can change this upon their first login in the Settings menu.</small>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-auto">Create Account</button>
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
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="table-body-row">
                                        <td class="table-cell-muted">U<?= sprintf("%03d", $row['user_id']) ?></td>
                                        <td class="table-cell-medium"><?= htmlspecialchars($row['username']) ?></td>
                                        <td class="table-cell"><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td class="table-actions-cell">
                                            <a href="#" class="action-edit">Edit</a>
                                            <a href="?delete_id=<?= $row['user_id'] ?>" class="action-revoke" onclick="return confirm('Revoke access for this assessor? This cannot be undone.')">Revoke Access</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php
    // settings_core.php
    // Shared password-change logic for both admin/settings.php and assessor/settings.php.
    // $required_role must be set by the calling file before this include.

    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    // 2. Initialize State
    $errors = [];
    $success_msg = "";

    // 3. Handle Success Messages from URL (PRG Pattern)
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'password_changed') $success_msg = "Password changed successfully.";
    }

    // 4. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password     = $_POST['current_password']     ?? '';
        $new_password         = $_POST['new_password']         ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $errors[] = "All fields are required.";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long.";
        } else {
            $user = get_user($conn, $_SESSION['user_id']);

            if ($user) {
                $user_id = (int) $user['user_id'];

                if (password_verify($current_password, $user['password'])) {
                    if ($current_password === $new_password) {
                        $errors[] = "New password cannot be the same as your current password.";
                    } elseif ($new_password === $confirm_new_password) {
                        $hash        = password_hash($new_password, PASSWORD_DEFAULT);
                        $redirect_url = null;

                        try {
                            $stmt = $conn->prepare("UPDATE `user` SET password = ? WHERE user_id = ?");
                            $stmt->bind_param("si", $hash, $user_id);
                            if ($stmt->execute()) {
                                $redirect_url = "settings.php?success=password_changed";
                            }
                        } catch (mysqli_sql_exception $e) {
                            $errors[] = "System error: Failed to update password. Please try again later.";
                        } finally {
                            if (isset($stmt)) $stmt->close();
                        }

                        if ($redirect_url) {
                            header("Location: $redirect_url");
                            exit;
                        }
                    } else {
                        $errors[] = "New Password and Confirm New Password do not match.";
                    }
                } else {
                    $errors[] = "Password entered does not match current password.";
                }
            } else {
                $errors[] = "User not found.";
            }
        }
    }

    // Determine page title based on role
    $page_title = ($_SESSION['role'] === 'admin') ? 'Admin Settings - University of Nottingham' : 'Assessor Settings - University of Nottingham';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- sidebar -->
        <?php include '../components/sidebar.php' ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
            <?php include '../components/header.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">My Account Settings</h1>
                </div>

                <!-- Global Feedback Messages -->
                <?php if (!empty($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($success_msg): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
                <?php endif; ?>

                <div class="card max-w-600">
                    <h3 class="card-header-sep">Change Password</h3>

                    <form action="" method="post">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min. 8 characters)" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_new_password" class="form-control" placeholder="Confirm new password" required>
                        </div>

                        <div class="mt-30">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

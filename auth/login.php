<?php
    // Start a session to remember current logged-in user
    session_start();

    include '../config/db.php';

    $error ="";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Prepare sql statement
        $stmt = $conn->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);

        // Execute prepared statement
        $stmt->execute();

        // Get results
        $results = $stmt->get_result();
        $user = $results->fetch_assoc();
        $stmt->close();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store user info in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // Redirect to page based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../assessor/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internship Result Management System</title>
    <!-- Import Inter Font for Premium Look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SVG Icons injected inline or via icon sets later. For now, basic CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1>University of Nottingham</h1>
                <p>Internship Result Management System</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" id="error-alert">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <div class="mt-24 font-14 text-muted-p text-center">
                Protected system for University Staff & Assessors.
            </div>
        </div>
    </div>
    <script src="../assets/js/form_validation.js"></script>
</body>
</html>
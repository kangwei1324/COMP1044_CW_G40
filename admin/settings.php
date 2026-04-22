<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Account Settings</h1>
                </div>

                <div class="card max-w-600">
                    <h3 class="card-header-sep">Security</h3>
                    
                    <form>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" required>
                        </div>

                        <!-- Info Update Option -->
                        <h3 class="card-header-sep mt-40">Profile Info</h3>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="Admin User" required>
                        </div>

                        <div class="mt-24">
                            <button type="button" class="btn btn-primary btn-auto">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

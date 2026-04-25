<?php
    $required_role = 'assessor';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessor Settings - IRMS</title>
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

                <div class="card max-w-600">
                    <h3 class="card-header-sep">Change Password</h3>
                    
                    <form>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter temporary or current password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" required>
                        </div>

                        <div class="mt-30">
                            <button type="button" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/validation.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Find the password form (it should be the only form on the settings page)
        const passwordForm = document.querySelector('form'); 
    
        if (passwordForm) {
            passwordForm.onsubmit = function(event) {
                // 2. Grab the values using the 'name' attributes from your HTML
                const newPwd = document.getElementsByName('new_password')[0].value;
                const confirmPwd = document.getElementsByName('confirm_password')[0].value;

                // 3. Point 3: The Match Check
                if (newPwd !== confirmPwd) {
                    alert("Validation Error: The 'New Password' and 'Confirm New Password' do not match.");
                
                    // Stop the form from submitting
                    event.preventDefault(); 
                    return false;
                }
            
                // If they match, the form continues to the PHP naturally
                return true; 
            };
        }
    });
</script>
</body>
</html>

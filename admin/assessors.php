<?php
    // 1. Guard and Config
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    // 2. Initialize State
    $errors = [];
    // Only allow user to create assessor roles
    // This also prevents users from using inspect element and adding their own invalid role when adding new assessors.
    $allowed_roles = ["industry_supervisor", "lecturer"];
    $success_msg = "";

    // editing mode
    $edit_mode = false;
    $edit_username = $edit_fullname = $edit_email = "";
    $action = $_POST['action'] ?? '';

    // 3. Handle Success Messages from URL (PRG Pattern)
    if (isset($_GET['success'])) {

        if ($_GET['success'] === 'added') {
            $success_msg = "New assessor account has been created successfully!";
        } elseif ($_GET['success'] === 'revoked') {
            $success_msg = "Assessor account has been revoked successfully!";
        } elseif ($_GET['success'] === 'edited') {
            $success_msg = "Assessor account has been edited successfully!";
        }
        
    }

    // 4. Pagination & Search State
    $search = trim($_GET['search'] ?? '');
    $limit  = 10;
    $page   = (int) ($_GET['page'] ?? 1);
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $total_assessors = count_assessors($conn, $search);
    $total_pages     = ceil($total_assessors / $limit);
    if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

    // 4. Handle Edit Trigger (GET)
    if(isset($_GET['edit_id'])) {
        $edit_id = (int) $_GET['edit_id'];

        if ($edit_id === $_SESSION['user_id']) {
            $errors[] = "You cannot edit your own account here.";
        } else {

            $target_user = get_user($conn, $edit_id);

            if (!$target_user) {
                $errors[] = "User not found.";
            } elseif ($target_user['role'] === 'admin') {
                $errors[] = "Admin accounts cannot be edited from this page.";
            } else {
                // safe to edit
                $edit_mode = true;
                $edit_username = $_POST['username'] ?? $target_user['username'];
                $edit_fullname = $_POST['fullname'] ?? $target_user['fullname'];
                $edit_email    = $_POST['email']    ?? $target_user['email'];

            }
        }
    }



    // 5. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {

        // 1. Clean Common Inputs
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');

        // 2. Common Validation
        if (empty($username)) $errors[] = "Username is required.";
        if (empty($email))    $errors[] = "Email is required.";
        if (empty($fullname)) $errors[] = "Full name is required.";

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // 3. Action Specific Validation
        if ($action === 'add') {
            $role = trim($_POST['role'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($role))     $errors[] = "Please select a role.";
            if (empty($password)) $errors[] = "Temporary password is required.";
            if (!in_array($role, $allowed_roles)) $errors[] = "Invalid role selected.";
        }

        // 4. stop immediately if any errors occured
        if (!empty($errors)) {
            // Do Nothing.
        } else {
            // 5. Database Actions

            // Edit Assessor
            if ($action === 'edit') {

                $edit_id = (int) ($_POST['edit_id'] ?? 0);

                // Make sure user cannot bypass UI state to POST to a forbidden ID
                $target_edit_user = get_user($conn, $edit_id);
                if (!$target_edit_user) {
                    $errors[] = "User not found.";
                } elseif ($target_edit_user['role'] === 'admin') {
                    $errors[] = "Cannot edit an admin account.";
                } elseif ($edit_id === (int) $_SESSION['user_id']) {
                    $errors[] = "Cannot edit your own account here.";
                } else {

                    // Check if any changes were made
                    if ($username === $target_edit_user['username'] && 
                        $email === $target_edit_user['email'] && 
                        $fullname === $target_edit_user['fullname']) {
                        
                        $errors[] = "No changes were made to the assessor information.";
                    } else {
                        $edit_stmt = $conn->prepare("UPDATE user SET username=?, email=?, fullname=? WHERE user_id=?");
                        $edit_stmt->bind_param("sssi", $username, $email, $fullname, $edit_id);

                        $redirect_url = null;

                        try {

                            if ($edit_stmt->execute()) {

                                if (isset($_POST['reset_password'])) {
                                    $password = "password123";
                                    $hash = password_hash($password, PASSWORD_DEFAULT);
                                    $reset_password_stmt = $conn->prepare("UPDATE user SET password=? WHERE user_id=?");
                                    $reset_password_stmt->bind_param("si", $hash, $edit_id);
                                    $reset_password_stmt->execute();
                                }

                                $redirect_url = "assessors.php?success=edited";
                            
                            }

                        } catch (mysqli_sql_exception) {

                            if ($conn->errno === 1062) { // 1062 = Duplicate entry
                                $errors[] = "Error: That Username or Email is already registered.";
                            } else {
                                $errors[] = "System error: Something went wrong, please try again later.";
                            }

                        } finally {

                            if (isset($edit_stmt)) $edit_stmt->close();
                            if (isset($reset_password_stmt)) {
                                $reset_password_stmt->close();
                            }
                        }

                        if ($redirect_url) {
                            header("Location: $redirect_url");
                            exit;
                        }
                    }
                }
                
            } elseif ($action === 'add') {
                // Add New Assessor

                // 4. Database Security (Insertion)
                // Hash the password
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the statement
                $add_stmt = $conn->prepare("INSERT INTO user (username, email, password, fullname, role) VALUES (?, ?, ?, ?, ?)");
                $add_stmt->bind_param("sssss", $username, $email, $hash, $fullname, $role);

                // 5. Execute and Redirect (The "PRG" Pattern)
                $redirect_url = null;
                try {
                    if ($add_stmt->execute()) {
                        
                        // We send a 'success' flag in the URL for the next page to catch
                        $redirect_url = "assessors.php?success=added";

                    }
                } catch (mysqli_sql_exception) {
                    if ($conn->errno === 1062) { // 1062 = Duplicate entry
                        $errors[] = "Error: That Username or Email is already registered.";
                    } else {
                        $errors[] = "System error: Something went wrong, please try again later.";
                    }
                } finally {
                    if (isset($add_stmt)) $add_stmt->close();
                }

                if ($redirect_url) {
                    header("Location: $redirect_url");
                    exit;
                }
            }
        }
    }

    // 6. Handle Deletions (GET)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int) $_GET['delete_id']; // Cast to int — kills any injection attempt

        // Guard 1: Prevent deleting yourself
        if ($delete_id === (int) $_SESSION['user_id']) {
            $errors[] = "You cannot revoke your own access.";

        // Guard 2: Prevent deleting any admin account
        } else {

            $target_user  = get_user($conn, $delete_id);


            if (!$target_user) {
                $errors[] = "User not found.";
            } elseif ($target_user['role'] === 'admin') {
                $errors[] = "Admin accounts cannot be deleted from this page.";
            } else {
                // Safe to delete
                $del_stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
                $del_stmt->bind_param("i", $delete_id);

                $redirect_url = null;
                try {
                    if ($del_stmt->execute()) {
                        $redirect_url = "assessors.php?success=revoked";
                    } else {
                        $errors[] = "System error: Something went wrong, please try again later.";
                    }

                } catch (mysqli_sql_exception) {
                    if ($conn->errno == 1451) {
                        $errors[] = "Cannot delete: This user is already assigned to students.";
                    } else {
                        $errors[] = "System error occurred.";
                    }
                } finally {
                    if (isset($del_stmt)) $del_stmt->close();
                }

                if ($redirect_url) {
                    header("Location: $redirect_url");
                    exit;
                }
            }
        }
    }

    // 7. Fetch Paged Records for the Table
    $result = get_assessors_paged($conn, $limit, $offset, $search);
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
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Manage Assessor Accounts</h1>
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add Assessor</button>
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

                <!-- Add Form -->
                <div class="card collapse-form" id="addForm" <?= ($action === 'add' && !empty($errors)) ? 'style="display:block;"' : '' ?>>
                    <h3 class="mb-20">Register New Assessor</h3>

                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="username">Username (Login ID)</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?= $action === 'add' ? htmlspecialchars($_POST['username'] ?? '') : '' ?>" placeholder="e.g. drsmith" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" value="<?= $action === 'add' ? htmlspecialchars($_POST['fullname'] ?? '') : '' ?>" placeholder="e.g. Dr. Alan Smith" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= $action === 'add' ? htmlspecialchars($_POST['email'] ?? '') : '' ?>" placeholder="e.g. alan12@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Select a Role</option>
                                <option value="industry_supervisor" <?= ($action === 'add' && ($_POST['role'] ?? '') === 'industry_supervisor') ? 'selected' : '' ?>>Industry Supervisor</option>
                                <option value="lecturer" <?= ($action === 'add' && ($_POST['role'] ?? '') === 'lecturer') ? 'selected' : '' ?>>Lecturer</option>
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

                <!-- Edit Form -->
                <div class="card collapse-form" style="display:<?= ($edit_mode || ($action === 'edit' && !empty($errors))) ? 'block' : 'none' ?>;" id="editForm">
                    <h3 class="mb-20">Edit Assessor</h3>

                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="edit_id" value="<?= $edit_id ?? '' ?>">
                        <div class="form-group">
                            <label for="username">Username (Login ID)</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($edit_username) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" value="<?= htmlspecialchars($edit_fullname) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($edit_email) ?>" required>
                        </div>
                        <div class="form-group form-span-2">
                            <label for="password">Reset Password</label>
                            <input type="checkbox" name="reset_password" id="reset_password">
                            <small class="card-description d-block mt-4">Reset Password to "password123"</small>
                            <small class="card-description d-block mt-4">Assessors can change this in the Settings menu.</small>
                        </div>
                        <div class="form-actions">
                            <a href="assessors.php" class="btn btn-secondary btn-auto">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-auto">Confirm</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <form action="" method="get" class="search-container">
                        <input type="text" name="search" class="form-control max-w-400" 
                               placeholder="Search by Name, Username or Email..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary btn-auto">Search</button>
                        <?php if (!empty($search)): ?>
                            <a href="assessors.php" class="btn btn-secondary btn-auto">Clear</a>
                        <?php endif; ?>
                    </form>

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
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="table-body-row">
                                            <td class="table-cell-muted">U<?= sprintf("%03d", $row['user_id']) ?></td>
                                            <td class="table-cell-medium"><?= htmlspecialchars($row['username']) ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['fullname']) ?></td>
                                            <td class="table-actions-cell">
                                                <a href="?edit_id=<?= $row['user_id'] ?>" class="action-edit">Edit</a>
                                                <a href="?delete_id=<?= $row['user_id'] ?>" class="action-revoke" onclick="return confirm('Revoke access for this assessor? This cannot be undone.')">Revoke Access</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="table-body-row">
                                        <td colspan="4" class="table-cell text-center">No assessor accounts found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_assessors) ?> of <?= $total_assessors ?> assessors
                            </div>
                            
                            <!-- Prev -->
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" 
                               class="pagination-item <?= ($page <= 1) ? 'disabled' : '' ?>"
                               <?= ($page <= 1) ? 'onclick="return false;"' : '' ?>>
                                &laquo; Prev
                            </a>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                                   class="pagination-item <?= ($i === $page) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" 
                               class="pagination-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>"
                               <?= ($page >= $total_pages) ? 'onclick="return false;"' : '' ?>>
                                Next &raquo;
                            </a>
                        </div>
                    <?php elseif ($total_assessors > 0): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing all <?= $total_assessors ?> assessors
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

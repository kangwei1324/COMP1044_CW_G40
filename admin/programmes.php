<?php
    // 1. Guard and Config
    $required_role = 'admin';
    include '../includes/auth_check.php';
    include '../config/db.php';

    // 2. Initialize State
    $errors = [];
    $success_msg = "";
    $edit_mode = false;
    $prog_name_value = "";
    $action = $_POST['action'] ?? '';

    // 3. Handle Success Messages from URL (PRG Pattern)
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'added')   $success_msg = "New programme added successfully!";
        if ($_GET['success'] === 'deleted') $success_msg = "Programme deleted successfully!";
        if ($_GET['success'] === 'edited')  $success_msg = "Programme updated successfully!";
    }

    // 4. Handle Edit Trigger (GET)
    if (isset($_GET['edit_id'])) {
        $edit_id = (int)$_GET['edit_id'];
        $stmt = $conn->prepare("SELECT * FROM programme WHERE programme_id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $programme = $res->fetch_assoc();
        $stmt->close();

        if ($programme) {
            $edit_mode = true;
            $prog_name_value = $_POST['prog_name'] ?? $programme['programme_name'];
        } else {
            $errors[] = "Programme not found.";
        }
    }

    // 5. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {
        $stmt = null; // Clear any previous statement from GET handlers
        $prog_name = trim($_POST['prog_name'] ?? '');

        if (empty($prog_name)) {
            $errors[] = "Programme name is required.";
        } else {
            $redirect_url = null;
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO programme (programme_name) VALUES (?)");
                    $stmt->bind_param("s", $prog_name);
                    $success_tag = "added";
                } elseif ($action === 'edit') {
                    $edit_id = (int)$_POST['edit_id'];
                    
                    if (isset($programme) && $prog_name === $programme['programme_name']) {
                        $errors[] = "Error: The programme name is still the same.";
                    } else {
                        $stmt = $conn->prepare("UPDATE programme SET programme_name = ? WHERE programme_id = ?");
                        $stmt->bind_param("si", $prog_name, $edit_id);
                        $success_tag = "edited";
                    }
                }

                if (isset($stmt) && $stmt->execute()) {
                    $redirect_url = "programmes.php?success=" . $success_tag;
                }
            } catch (mysqli_sql_exception $e) {
                if ($conn->errno === 1062) {
                    $errors[] = "Error: This programme name already exists.";
                } else {
                    $errors[] = "Database error: " . $e->getMessage();
                }
            } finally {
                if (isset($stmt)) $stmt->close();
            }

            if ($redirect_url) {
                header("Location: $redirect_url");
                exit;
            }
        }
    }

    // 6. Handle Deletions (GET)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $redirect_url = null;

        try {
            $stmt = $conn->prepare("DELETE FROM programme WHERE programme_id = ?");
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $redirect_url = "programmes.php?success=deleted";
            }
        } catch (mysqli_sql_exception $e) {
            if ($conn->errno === 1451) {
                $errors[] = "Cannot delete: Students are currently enrolled in this programme.";
            } else {
                $errors[] = "Database error occurred.";
            }
        } finally {
            if (isset($stmt)) $stmt->close();
        }

        if ($redirect_url) {
            header("Location: $redirect_url");
            exit;
        }
    }

    // 7. Fetch all records for the table
    $result = $conn->query("SELECT * FROM programme ORDER BY programme_name ASC");
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
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add New Programme</button>
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

                <!-- Add/Edit Form -->
                <div class="card collapse-form" id="addForm" style="display: <?= ($action === 'add' && !empty($errors)) ? 'block' : 'none' ?>;">
                    <h3 class="mb-20">Add New Programme</h3>
                    <form action="" method="post" class="display-flex gap-16 align-end">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group flex-1 mb-0">
                            <label for="prog_name_add">Programme Name</label>
                            <input type="text" name="prog_name" id="prog_name_add" class="form-control" value="<?= $action === 'add' ? htmlspecialchars($_POST['prog_name'] ?? '') : '' ?>" placeholder="e.g. Bachelor of Computer Science" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-auto">Save</button>
                        <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                    </form>
                </div>

                <div class="card collapse-form" id="editForm" style="display: <?= ($edit_mode || ($action === 'edit' && !empty($errors))) ? 'block' : 'none' ?>;">
                    <h3 class="mb-20">Edit Programme</h3>
                    <form action="" method="post" class="display-flex gap-16 align-end">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="edit_id" value="<?= $edit_id ?? '' ?>">
                        <div class="form-group flex-1 mb-0">
                            <label for="prog_name_edit">Programme Name</label>
                            <input type="text" name="prog_name" id="prog_name_edit" class="form-control" value="<?= htmlspecialchars($prog_name_value) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-auto">Update</button>
                        <a href="programmes.php" class="btn btn-secondary btn-auto">Cancel</a>
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
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="table-body-row">
                                            <td class="table-cell-muted"><?= $row['programme_id'] ?></td>
                                            <td class="table-cell-medium"><?= htmlspecialchars($row['programme_name']) ?></td>
                                            <td class="table-actions-cell">
                                                <a href="?edit_id=<?= $row['programme_id'] ?>" class="action-edit">Edit</a>
                                                <a href="?delete_id=<?= $row['programme_id'] ?>" class="action-revoke" onclick="return confirm('Delete this programme? This cannot be undone if students are enrolled.')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="table-body-row">
                                        <td colspan="3" class="table-cell text-center">No programmes found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

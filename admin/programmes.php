<?php
    // 1. Guard and Config
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

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

    // 4. Pagination & Search State
    $search = trim($_GET['search'] ?? '');
    $limit  = 10;
    $page   = (int) ($_GET['page'] ?? 1);
    if ($page < 1) $page = 1;

    $total_programmes = count_programmes($conn, $search);
    $total_pages      = ceil($total_programmes / $limit);
    if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

    $offset = ($page - 1) * $limit;

    // 5. Handle Edit Trigger (GET)
    if (isset($_GET['edit_id'])) {
        $edit_id = (int)$_GET['edit_id'];
        $programme = get_programme($conn, $edit_id);

        if ($programme) {
            $edit_mode = true;
            $prog_name_value = $_POST['prog_name'] ?? $programme['programme_name'];
        } else {
            $errors[] = "Programme not found.";
        }
    }

    // 6. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {
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

                    // Fresh DB lookup — do not rely on GET block's $programme variable
                    $existing_programme = get_programme($conn, $edit_id);

                    if (!$existing_programme) {
                        $errors[] = "Programme not found.";
                    } elseif ($prog_name === $existing_programme['programme_name']) {
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
                    $errors[] = "System error: Something went wrong, please try again later.";
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

    // 7. Handle Deletions (GET)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];

        // Verify the programme exists first
        $target_programme = get_programme($conn, $delete_id);

        if (!$target_programme) {
            $errors[] = "Programme not found.";
        } else {
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
                    $errors[] = "System error: Something went wrong, please try again later.";
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

    // 8. Fetch paged records for the table
    $result = get_programmes_paged($conn, $limit, $offset, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Programmes - IRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <form action="" method="get" class="search-container">
                        <input type="text" name="search" class="form-control max-w-400" 
                               placeholder="Search by Programme Name..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary btn-auto">Search</button>
                        <?php if (!empty($search)): ?>
                            <a href="programmes.php" class="btn btn-secondary btn-auto">Clear</a>
                        <?php endif; ?>
                    </form>
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

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_programmes) ?> of <?= $total_programmes ?> programmes
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
                    <?php elseif ($total_programmes > 0): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing all <?= $total_programmes ?> programmes
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

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
    $action = $_POST['action'] ?? '';

    // Edit form sticky variables
    $edit_student_name_value = "";
    $edit_programme_id_value = "";

    // 3. Handle Success Messages from URL (PRG Pattern)
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'added')   $success_msg = "New student profile has been created successfully!";
        if ($_GET['success'] === 'deleted') $success_msg = "Student profile has been deleted successfully!";
        if ($_GET['success'] === 'edited')  $success_msg = "Student profile has been updated successfully!";
    }

    // 4. Handle Edit Trigger (GET)
    if (isset($_GET['edit_id'])) {
        $edit_id = (int) $_GET['edit_id'];

        $student = get_student($conn, $edit_id);

        if ($student) {
            $edit_mode = true;
            // Sticky inputs: use POST value if re-rendering after validation failure, else use DB value
            $edit_student_name_value = $_POST['student_name'] ?? $student['student_name'];
            $edit_programme_id_value = $_POST['programme_id'] ?? $student['programme_id'];
        } else {
            $errors[] = "Student not found.";
        }
    }

    // 5. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {

        // Clean & collect inputs
        $student_name  = trim($_POST['student_name'] ?? '');
        $programme_id  = (int) ($_POST['programme_id'] ?? 0);

        // Common validation
        if (empty($student_name)) $errors[] = "Student full name is required.";
        if ($programme_id <= 0)   $errors[] = "Please select a valid programme.";

        // Action-specific validation
        if ($action === 'add') {
            $student_id = trim($_POST['student_id'] ?? '');
            if (empty($student_id))           $errors[] = "Student ID (Matric No.) is required.";
            if (!ctype_digit($student_id))    $errors[] = "Student ID must be numeric.";
        }

        if (empty($errors)) {
            $redirect_url = null;

            if ($action === 'add') {
                $student_id = (int) $student_id;

                $stmt = $conn->prepare("INSERT INTO student (student_id, student_name, programme_id) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $student_id, $student_name, $programme_id);

                try {
                    if ($stmt->execute()) {
                        $redirect_url = "students.php?success=added";
                    }
                } catch (mysqli_sql_exception) {
                    if ($conn->errno === 1062) {
                        $errors[] = "Error: A student with this Matric No. already exists.";
                    } elseif ($conn->errno === 1452) {
                        $errors[] = "Error: The selected programme does not exist.";
                    } else {
                        $errors[] = "System error: Something went wrong, please try again later.";
                    }
                } finally {
                    if (isset($stmt)) $stmt->close();
                }

            } elseif ($action === 'edit') {
                $edit_id = (int) ($_POST['edit_id'] ?? 0);

                // Re-verify the student still exists before updating
                $existing = get_student($conn, $edit_id);

                if (!$existing) {
                    $errors[] = "Student not found.";
                } elseif ($student_name === $existing['student_name'] && $programme_id == $existing['programme_id']) {
                    $errors[] = "No changes were made to the student profile.";
                } else {
                    $stmt = $conn->prepare("UPDATE student SET student_name = ?, programme_id = ? WHERE student_id = ?");
                    $stmt->bind_param("sii", $student_name, $programme_id, $edit_id);

                    try {
                        if ($stmt->execute()) {
                            $redirect_url = "students.php?success=edited";
                        }
                    } catch (mysqli_sql_exception) {
                        if ($conn->errno === 1452) {
                            $errors[] = "Error: The selected programme does not exist.";
                        } else {
                            $errors[] = "System error: Something went wrong, please try again later.";
                        }
                    } finally {
                        if (isset($stmt)) $stmt->close();
                    }
                }
            }

            if ($redirect_url) {
                header("Location: $redirect_url");
                exit;
            }
        }
    }

    // 6. Handle Deletions (GET)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int) $_GET['delete_id'];

        // Verify the student exists before attempting delete
        $target_student = get_student($conn, $delete_id);

        if (!$target_student) {
            $errors[] = "Student not found.";
        } else {
            $redirect_url = null;
            $del_stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
            $del_stmt->bind_param("i", $delete_id);

            try {
                if ($del_stmt->execute()) {
                    $redirect_url = "students.php?success=deleted";
                } else {
                    $errors[] = "System error: Something went wrong, please try again later.";
                }
            } catch (mysqli_sql_exception) {
                if ($conn->errno === 1451) {
                    $errors[] = "Cannot delete: This student is currently assigned to an active internship.";
                } else {
                    $errors[] = "System error: Something went wrong, please try again later.";
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

    // 7. Fetch all records for the table and the programme dropdown
    $result = $conn->query("SELECT s.student_id, s.student_name, p.programme_name
                            FROM student s
                            JOIN programme p ON s.programme_id = p.programme_id
                            ORDER BY s.student_name ASC");

    $programmes = $conn->query("SELECT * FROM programme ORDER BY programme_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include '../components/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <?php include '../components/header_admin.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Manage Student Profiles</h1>
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Add Student</button>
                </div>

                <!-- Global Feedback Messages -->
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($success_msg): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
                <?php endif; ?>

                <!-- Add Form -->
                <div class="card collapse-form" id="addForm" <?= ($action === 'add' && !empty($errors)) ? 'style="display:block;"' : '' ?>>
                    <h3 class="mb-20">Register New Student</h3>
                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="student_id_add">Student ID (Matric No.)</label>
                            <input type="text" name="student_id" id="student_id_add" class="form-control"
                                   value="<?= $action === 'add' ? htmlspecialchars($_POST['student_id'] ?? '') : '' ?>"
                                   placeholder="e.g. 20701234" required>
                        </div>
                        <div class="form-group">
                            <label for="student_name_add">Full Name</label>
                            <input type="text" name="student_name" id="student_name_add" class="form-control"
                                   value="<?= $action === 'add' ? htmlspecialchars($_POST['student_name'] ?? '') : '' ?>"
                                   placeholder="e.g. Ahmad Faris Bin Zulkifli" required>
                        </div>
                        <div class="form-group form-span-2">
                            <label for="programme_id_add">Programme</label>
                            <select name="programme_id" id="programme_id_add" class="form-control" required>
                                <option value="">Select a Programme...</option>
                                <?php
                                    // Store programmes in an array so we can reuse for the edit form
                                    $programmes_list = [];
                                    while ($prog = $programmes->fetch_assoc()) {
                                        $programmes_list[] = $prog;
                                    }
                                    foreach ($programmes_list as $prog):
                                        $selected = ($action === 'add' && ($_POST['programme_id'] ?? '') == $prog['programme_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $prog['programme_id'] ?>" <?= $selected ?>><?= htmlspecialchars($prog['programme_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-auto">Save Student</button>
                        </div>
                    </form>
                </div>

                <!-- Edit Form -->
                <div class="card collapse-form" id="editForm" style="display: <?= ($edit_mode || ($action === 'edit' && !empty($errors))) ? 'block' : 'none' ?>;">
                    <h3 class="mb-20">Edit Student Profile</h3>
                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="edit_id" value="<?= $edit_id ?? '' ?>">
                        <div class="form-group form-span-2">
                            <label>Student ID (Matric No.)</label>
                            <!-- Read-only: student_id is the PK and a FK in internships, it should not be changed -->
                            <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" disabled>
                            <small class="card-description d-block mt-4">The Student ID cannot be changed as it is used to link internship records.</small>
                        </div>
                        <div class="form-group">
                            <label for="student_name_edit">Full Name</label>
                            <input type="text" name="student_name" id="student_name_edit" class="form-control"
                                   value="<?= htmlspecialchars($edit_student_name_value) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="programme_id_edit">Programme</label>
                            <select name="programme_id" id="programme_id_edit" class="form-control" required>
                                <option value="">Select a Programme...</option>
                                <?php foreach ($programmes_list as $prog):
                                    $selected = ($edit_programme_id_value == $prog['programme_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $prog['programme_id'] ?>" <?= $selected ?>><?= htmlspecialchars($prog['programme_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-actions">
                            <a href="students.php" class="btn btn-secondary btn-auto">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-auto">Update Student</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">Student ID</th>
                                    <th class="table-header-cell">Full Name</th>
                                    <th class="table-header-cell">Programme</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="table-body-row">
                                            <td class="table-cell-muted"><?= htmlspecialchars($row['student_id']) ?></td>
                                            <td class="table-cell-medium"><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td class="table-cell"><?= htmlspecialchars($row['programme_name']) ?></td>
                                            <td class="table-actions-cell">
                                                <a href="?edit_id=<?= $row['student_id'] ?>" class="action-edit">Edit</a>
                                                <a href="?delete_id=<?= $row['student_id'] ?>" class="action-revoke"
                                                   onclick="return confirm('Delete this student profile? This cannot be undone if they have active internships.')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="table-body-row">
                                        <td colspan="4" class="table-cell text-center">No students found.</td>
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
<?php
    // 1. Guard and Config
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';
    include '../includes/functions.php';

    // 2. Initialize State
    $errors = [];
    $success_msg = "";
    $action = $_POST['action'] ?? '';

    // editing variables
    $edit_mode = false;
    $edit_student_id = $edit_lecturer_id = $edit_supervisor_id = $edit_company_name = $edit_semester = $edit_internship_year = "";


    // 3. Handle Success Messages from URL (PRG Pattern)
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'added')   $success_msg = "New internship added successfully!";
        if ($_GET['success'] === 'deleted') $success_msg = "Internship deleted successfully!";
        if ($_GET['success'] === 'edited')  $success_msg = "Internship updated successfully!";
    }

    // 4. Handle Edit Trigger (GET)
    if (isset($_GET['edit_id'])) {
        $edit_id = (int) $_GET['edit_id'];

        $internship = check_internships($conn, $edit_id);

        if (!$internship) {
            $errors[] = "Internship not found.";
        } else {
            $edit_mode = true;
            // If user submitted form but failed, refill previous submission details.
            // If empty, get from database.
            $edit_internship_id = $internship['internship_id'];
            $edit_student_id = $_POST['student_id'] ?? $internship['student_id'];
            $edit_lecturer_id = $_POST['lecturer_id'] ?? $internship['lecturer_id'];
            $edit_supervisor_id = $_POST['supervisor_id'] ?? $internship['industry_supervisor_id'];
            $edit_company_name = $_POST['company_name'] ?? $internship['company_name'];
            $edit_semester = $_POST['semester'] ?? $internship['semester'];
            $edit_internship_year = $_POST['internship_year'] ?? $internship['internship_year'];
        }
    }

    // 5. Handle Form Submissions (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {

        $student_id = trim($_POST['student_id'] ?? '');
        $lecturer_id = trim($_POST['lecturer_id'] ?? '');
        $supervisor_id = trim($_POST['supervisor_id'] ?? '');
        $company_name = trim($_POST['company_name'] ?? '');
        $semester = trim($_POST['semester'] ?? '');
        $internship_year = (int) ($_POST['internship_year'] ?? 0);

        if (!ctype_digit($student_id))      $errors[] = "Please select a valid student.";
        if (!ctype_digit($lecturer_id))     $errors[] = "Please select a valid lecturer.";
        if (!ctype_digit($supervisor_id))   $errors[] = "Please select a valid industry supervisor.";
        if (empty($company_name))           $errors[] = "Please enter a company name.";
        if (empty($semester))               $errors[] = "Please select a valid semester.";
        if ($internship_year < 2024)        $errors[] = "Please select a valid year.";

        if (!empty($errors)) {
            // Do Nothing.
        } else {

            if ($action === 'edit') {
                $edit_id = (int) $_POST['edit_id'];
                // Make sure user cannot bypass UI state to POST to a forbidden ID
                $internship = check_internships($conn, $edit_id);
                if (!$internship) {
                    $errors[] = "Internship not found.";
                } else {
                    // Check if user made any changes
                    if ($student_id == $internship['student_id'] && 
                        $lecturer_id == $internship['lecturer_id'] && 
                        $supervisor_id == $internship['industry_supervisor_id'] && 
                        $company_name === $internship['company_name'] && 
                        $semester === $internship['semester'] && 
                        $internship_year == $internship['internship_year']) {
                        
                        $errors[] = "No changes were made to the internship assignment.";
                    } else {
                        $sql = "UPDATE internships SET student_id = ?, lecturer_id = ?, industry_supervisor_id = ?, company_name = ?, semester = ?, internship_year = ? WHERE internship_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiissii", $student_id, $lecturer_id, $supervisor_id, $company_name, $semester, $internship_year, $edit_id);

                        $redirect_url = null;
                        try {
                            if ($stmt->execute()) {
                                $redirect_url = "internships.php?success=edited";
                            }
                        } catch (mysqli_sql_exception) {
                            if ($conn->errno === 1062) {// 1062 = Duplicate entry
                                $errors[] = "Error: This student is already assigned to an internship for $semester $internship_year. Please check the existing records.";
                            } elseif ($conn->errno === 1452) {
                                $errors[] = "Error: One of the selected options does not exist in our records.";
                            } else {
                                $errors[] = "System Error: Something went wrong, please try again later.";
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

            } elseif ($action === 'add') {
                $sql = "INSERT INTO internships (student_id, industry_supervisor_id, lecturer_id, company_name, semester, internship_year) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiissi", $student_id, $supervisor_id, $lecturer_id, $company_name, $semester, $internship_year);

                $redirect_url = null;
                try{
                    if ($stmt->execute()) {
                        $redirect_url = "internships.php?success=added";
                    }

                } catch (mysqli_sql_exception) {
                    if ($conn->errno === 1062) { // 1062 = Duplicate entry
                        $errors[] = "Error: This student is already assigned to an internship for $semester $internship_year. Please check the existing records.";
                    } elseif ($conn->errno === 1452) { // 1452 = Foreign Key Constraint Fail
                        $errors[] = "Error: One of the selected persons does not exist in our records.";
                    } else {
                        $errors[] = "System Error: Something went wrong, please try again later.";
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
    }

    // 6. Handle Deletions (GET)
    if (isset($_GET['delete_id'])) {
        $delete_id = (int) $_GET['delete_id'];
        $internship = check_internships($conn, $delete_id);

        if (!$internship) {
            $errors[] = "Internship not found.";
        } else {
            $del_stmt = $conn->prepare("DELETE FROM internships WHERE internship_id = ?");
            $del_stmt->bind_param("i", $delete_id);

            $redirect_url = null;
            try{
                if ($del_stmt->execute()) {
                    $redirect_url = "internships.php?success=deleted";
                } else {
                    $errors[] = "System Error: Something went wrong, please try again later.";
                }
            } catch (mysqli_sql_exception) {
                if ($conn->errno == 1451) { // has at least 1 submitted assessment
                    $errors[] = "Cannot delete: This internship is being actively marked";
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

    // 7. Fetch All Records for the Table
    $result = get_internships($conn);

    $lecturers = get_lecturers($conn);
    $supervisors = get_supervisors($conn);
    $students = get_students($conn);

    $semesters = ['Autumn', 'Spring', 'Summer'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Internships - IRMS</title>
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
                    <h1 class="page-title">Manage Internships (Assessor Assignments)</h1>
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Assign Internship</button>
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
                    <h3 class="mb-20">Assign Assessor to Student Internship</h3>
                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Please Select a Student</option>
                                <?php while ($row = $students->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['student_id']) ?>" <?= ($action === 'add' && ($_POST['student_id'] ?? '') == $row['student_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['student_name']) ?> - <?= htmlspecialchars($row['student_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lecturer</label>
                            <select name="lecturer_id" class="form-control" required>
                                <option value="">Please Select a Lecturer</option>
                                <?php while ($row = $lecturers->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($action === 'add' && ($_POST['lecturer_id'] ?? '') == $row['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Row 2: Industry Supervisor and Company Details -->
                        <div class="form-group">
                            <label>Industry Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                <option value="">Please Select an Industry Supervisor</option>
                                <?php while ($row = $supervisors->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($action === 'add' && ($_POST['supervisor_id'] ?? '') == $row['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= $action === 'add' ? htmlspecialchars($_POST['company_name'] ?? '') : '' ?>" placeholder="e.g. Acme Corporation" required>
                        </div>
                        
                        <!-- Row 3: Timeline -->
                        <div class="form-group">
                            <label>Semester</label>
                            <select name="semester" class="form-control" required>
                                <option value="">Select a Semester</option>
                                <?php foreach($semesters as $sem_option): ?>
                                    <option value="<?= htmlspecialchars($sem_option) ?>" <?= ($action === 'add' && ($_POST['semester'] ?? '') === $sem_option) ? 'selected' : '' ?>><?= htmlspecialchars($sem_option) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="internship_year" id="internship_year" class="form-control" value="<?= $action === 'add' && !empty($_POST['internship_year']) ? htmlspecialchars($_POST['internship_year']) : date('Y') ?>" placeholder="e.g. 2026" required>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-auto">Save Assignment</button>
                        </div>
                    </form>
                </div>

                <!-- Edit Form -->
                <?php
                    // Reset results so we can loop through them again for the Edit form
                    if ($students) $students->data_seek(0);
                    if ($lecturers) $lecturers->data_seek(0);
                    if ($supervisors) $supervisors->data_seek(0);
                ?>
                <div class="card collapse-form" id="editForm" style="display: <?= ($edit_mode || ($action === 'edit' && !empty($errors))) ? 'block' : 'none' ?>;">
                    <h3 class="mb-20">Edit Student Internship</h3>
                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_internship_id ?? '') ?>">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Please Select a Student</option>
                                <?php while ($row = $students->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['student_id']) ?>" <?= ($row['student_id'] == $edit_student_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['student_name']) ?> - <?= htmlspecialchars($row['student_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lecturer</label>
                            <select name="lecturer_id" class="form-control" required>
                                <option value="">Please Select a Lecturer</option>
                                <?php while ($row = $lecturers->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($row['user_id'] == $edit_lecturer_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Row 2: Industry Supervisor and Company Details -->
                        <div class="form-group">
                            <label>Industry Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                <option value="">Please Select an Industry Supervisor</option>
                                <?php while ($row = $supervisors->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($row['user_id'] == $edit_supervisor_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($edit_company_name) ?>" placeholder="e.g. Acme Corporation" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Semester</label>
                            <select name="semester" class="form-control" required>
                                <?php foreach($semesters as $semester): ?>
                                    <option value="<?= htmlspecialchars($semester) ?>" <?= ($semester === $edit_semester) ? 'selected' : '' ?>><?= htmlspecialchars($semester) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="internship_year" id="internship_year" class="form-control" value="<?= htmlspecialchars($edit_internship_year) ?>" placeholder="e.g. 2026" required>
                        </div>

                        <div class="form-actions">
                            <a href="internships.php" class="btn btn-secondary btn-auto">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-auto">Save Assignment</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="search-container justify-between">
                        <input type="text" class="form-control max-w-400" placeholder="Search Assignments by Student or Company...">
                        
                        <select class="form-control btn-auto">
                            <!-- Filter dropdown simulation -->
                            <option value="">Filter by Assessor: All</option>
                            <option value="2">Dr. Alan Smith</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">Student</th>
                                    <th class="table-header-cell">Lecturer</th>
                                    <th class="table-header-cell">Industry Supervisor</th>
                                    <th class="table-header-cell">Company</th>
                                    <th class="table-header-cell">Period</th>
                                    <th class="table-header-cell">Result Status</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php
                                            $internship_id = htmlspecialchars($row['internship_id']);
                                            $student_id = htmlspecialchars($row['student_id']);
                                            $student_name = htmlspecialchars($row['student_name']);
                                            $lecturer_name = htmlspecialchars($row['lecturer_name']);
                                            $supervisor_name = htmlspecialchars($row['supervisor_name']);
                                            $company_name = htmlspecialchars($row['company_name']);

                                            $semester = htmlspecialchars($row['semester']);
                                            $internship_year = htmlspecialchars($row['internship_year']);
                                            $period = $semester . ' / ' . $internship_year;

                                            $assessment_count = $row['assessment_count'];
                                            $status = null;
                                            $badge_color = null;
                                            if ($assessment_count == '2') {
                                                $badge_color = 'badge-success';
                                                $status = 'Completed';
                                            } elseif ($assessment_count == '1') {
                                                $badge_color = 'badge-warning';
                                                $status = 'Incomplete 1/2';
                                            } else {
                                                $badge_color = 'badge-warning';
                                                $status = 'Incomplete 0/2';
                                            }
                                        ?>
                                        <tr class="table-body-row">
                                            <td class="table-cell-medium"><?= $student_id ?> <br><span class="subtitle"><?= $student_name ?></span></td>
                                            <td class="table-cell"><?= $lecturer_name ?></td>
                                            <td class="table-cell"><?= $supervisor_name ?></td>
                                            <td class="table-cell-muted"><?= $company_name ?></td>
                                            <td class="table-cell font-14"><?= $period ?></td>
                                            <td class="table-cell">
                                                <span class="badge <?= $badge_color ?>"><?= $status ?></span>
                                            </td>
                                            <td class="table-actions-cell">
                                                <a href="?edit_id=<?= $internship_id ?>" class="action-edit">Edit</a>
                                                <a href="?delete_id=<?= $internship_id ?>" class="action-revoke" onclick="return confirm('Do you want to delete this internship? This cannot be undone.')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="table-body-row">
                                        <td colspan="7" class="table-cell text-center">No internship assignments found.</td>
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

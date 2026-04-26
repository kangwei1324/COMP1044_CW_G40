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
    if (isset($_GET['success']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($_GET['success'] === 'added')   $success_msg = "New internship added successfully!";
        if ($_GET['success'] === 'deleted') $success_msg = "Internship deleted successfully!";
        if ($_GET['success'] === 'edited')  $success_msg = "Internship updated successfully!";
    }

    // 4. Pagination & Search/Filter State
    $search = trim($_GET['search'] ?? '');
    $filters = [
        'lecturer_id'   => $_GET['lecturer_id'] ?? '',
        'supervisor_id' => $_GET['supervisor_id'] ?? '',
        'semester'      => $_GET['semester'] ?? '',
        'year'          => $_GET['year'] ?? ''
    ];
    
    $limit  = 10;
    $page   = (int) ($_GET['page'] ?? 1);
    if ($page < 1) $page = 1;

    $total_internships = count_internships($conn, $search, $filters);
    $total_pages       = ceil($total_internships / $limit);
    if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

    $offset = ($page - 1) * $limit;

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
                        // Option 4 Logic: Check if an assessor changed and the old one has marks
                        $needs_confirmation = false;
                        $old_assessors_to_clear = [];
                        $confirm_reassign = isset($_POST['confirm_reassign']) && $_POST['confirm_reassign'] == '1';

                        if ($lecturer_id != $internship['lecturer_id']) {
                            if (check_assessor_has_marks($conn, $edit_id, $internship['lecturer_id'])) {
                                $needs_confirmation = true;
                                $old_assessors_to_clear[] = $internship['lecturer_id'];
                            }
                        }
                        if ($supervisor_id != $internship['industry_supervisor_id']) {
                            if (check_assessor_has_marks($conn, $edit_id, $internship['industry_supervisor_id'])) {
                                $needs_confirmation = true;
                                $old_assessors_to_clear[] = $internship['industry_supervisor_id'];
                            }
                        }

                        if ($needs_confirmation && !$confirm_reassign) {
                            $errors[] = "We need your confirmation: The previous assessor(s) already submitted marks. Reassigning will permanently delete them. Check the confirmation box to proceed.";
                        } else {
                            // If confirmed, clear the old marks first
                            if ($needs_confirmation && $confirm_reassign && !empty($old_assessors_to_clear)) {
                                foreach ($old_assessors_to_clear as $assessor_to_clear) {
                                    delete_assessor_marks($conn, $edit_id, $assessor_to_clear);
                                }
                            }

                            // Proceed with UPDATE
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

    // 6. Handle Deletions (POST)
    if (isset($_POST['delete_id'])) {
        $delete_id = (int) $_POST['delete_id'];
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

    // 7. Fetch Paged Records for the Table
    $result = get_internships_paged($conn, $limit, $offset, $search, $filters);

    $lecturers_list = get_lecturers($conn);
    $supervisors_list = get_supervisors($conn);
    $students_list = get_students($conn);

    // Collect into arrays for reuse
    $lecturers = []; while($row = $lecturers_list->fetch_assoc()) $lecturers[] = $row;
    $supervisors = []; while($row = $supervisors_list->fetch_assoc()) $supervisors[] = $row;
    $students = []; while($row = $students_list->fetch_assoc()) $students[] = $row;

    $semesters = ['Autumn', 'Spring', 'Summer'];
    $years = $conn->query("SELECT DISTINCT internship_year FROM internships ORDER BY internship_year DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Internships - University of Nottingham</title>
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
                    <form action="" method="post" class="form-grid" novalidate>
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Please Select a Student</option>
                                <?php foreach ($students as $row): ?>
                                    <option value="<?= htmlspecialchars($row['student_id']) ?>" <?= ($action === 'add' && ($_POST['student_id'] ?? '') == $row['student_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['student_name']) ?> - <?= htmlspecialchars($row['student_id']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lecturer</label>
                            <select name="lecturer_id" class="form-control" required>
                                <option value="">Please Select a Lecturer</option>
                                <?php foreach ($lecturers as $row): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($action === 'add' && ($_POST['lecturer_id'] ?? '') == $row['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Row 2: Industry Supervisor and Company Details -->
                        <div class="form-group">
                            <label>Industry Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                <option value="">Please Select an Industry Supervisor</option>
                                <?php foreach ($supervisors as $row): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($action === 'add' && ($_POST['supervisor_id'] ?? '') == $row['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endforeach; ?>
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
                    // Results are in arrays, so we can loop through them multiple times
                ?>
                <div class="card collapse-form" id="editForm" style="display: <?= ($edit_mode || ($action === 'edit' && !empty($errors))) ? 'block' : 'none' ?>;">
                    <h3 class="mb-20">Edit Student Internship</h3>
                    <form action="" method="post" class="form-grid" novalidate>
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_internship_id ?? '') ?>">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Please Select a Student</option>
                                <?php foreach ($students as $row): ?>
                                    <option value="<?= htmlspecialchars($row['student_id']) ?>" <?= ($row['student_id'] == $edit_student_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['student_name']) ?> - <?= htmlspecialchars($row['student_id']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lecturer</label>
                            <select name="lecturer_id" class="form-control" required>
                                <option value="">Please Select a Lecturer</option>
                                <?php foreach ($lecturers as $row): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($row['user_id'] == $edit_lecturer_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Row 2: Industry Supervisor and Company Details -->
                        <div class="form-group">
                            <label>Industry Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                <option value="">Please Select an Industry Supervisor</option>
                                <?php foreach ($supervisors as $row): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>" <?= ($row['user_id'] == $edit_supervisor_id) ? 'selected' : '' ?>><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endforeach; ?>
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

                        <?php if (isset($needs_confirmation) && $needs_confirmation): ?>
                            <div class="form-span-2">
                                <div class="alert alert-warning mb-20" style="padding: 20px;">
                                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                                        <span style="font-size: 20px;">⚠️</span>
                                        <div>
                                            <strong style="display: block; margin-bottom: 4px; font-size: 15px;">Data Loss Warning</strong>
                                            <p style="margin-bottom: 12px; opacity: 0.9;">The previous assessor(s) have already submitted marks for this internship. Reassigning them will <strong>permanently delete</strong> all existing marks and comments.</p>
                                            
                                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; background: rgba(255,255,255,0.5); padding: 10px 14px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.05);">
                                                <input type="checkbox" name="confirm_reassign" value="1" required style="width: 18px; height: 18px; cursor: pointer;">
                                                <span style="font-weight: 600; font-size: 14px;">I understand, delete grades and proceed with reassignment.</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <a href="internships.php" class="btn btn-secondary btn-auto">Cancel</a>
                            <button type="submit" class="btn <?= (isset($needs_confirmation) && $needs_confirmation) ? 'btn-danger' : 'btn-primary' ?> btn-auto"><?= (isset($needs_confirmation) && $needs_confirmation) ? 'Confirm & Overwrite' : 'Save Assignment' ?></button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <form action="" method="get" class="search-container">
                        <input type="text" name="search" class="form-control max-w-400" 
                               placeholder="Search by Student, Company or ID..." 
                               value="<?= htmlspecialchars($search) ?>">
                        
                        <select name="lecturer_id" class="form-control btn-auto">
                            <option value="">Filter by Lecturer: All</option>
                            <?php foreach ($lecturers as $row): if ($row['role'] === 'lecturer'): ?>
                                <option value="<?= $row['user_id'] ?>" <?= $filters['lecturer_id'] == $row['user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['fullname']) ?>
                                </option>
                            <?php endif; endforeach; ?>
                        </select>

                        <select name="semester" class="form-control btn-auto">
                            <option value="">Semester: All</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem ?>" <?= $filters['semester'] === $sem ? 'selected' : '' ?>><?= $sem ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select name="year" class="form-control btn-auto">
                            <option value="">Year: All</option>
                            <?php while($y = $years->fetch_assoc()): ?>
                                <option value="<?= $y['internship_year'] ?>" <?= $filters['year'] == $y['internship_year'] ? 'selected' : '' ?>>
                                    <?= $y['internship_year'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-auto">Filter</button>
                        <?php if (!empty($search) || !empty(array_filter($filters))): ?>
                            <a href="internships.php" class="btn btn-secondary btn-auto">Reset</a>
                        <?php endif; ?>
                    </form>

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
                                                <?php if ($assessment_count > 0): ?>
                                                    <a href="view_result.php?internship_id=<?= $internship_id ?>" class="action-edit" style="color: #0f172a;">View Results</a>
                                                <?php endif; ?>
                                                <a href="?edit_id=<?= $internship_id ?>" class="action-edit">Edit</a>
                                                <form action="" method="post" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?= $internship_id ?>">
                                                    <button type="submit" class="btn-link-reset action-revoke" onclick="return confirm('Are you sure you want to delete this internship assignment? This action cannot be undone.')">Delete</button>
                                                </form>
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

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_internships) ?> of <?= $total_internships ?> assignments
                            </div>
                            
                            <!-- Prev -->
                            <?php 
                                $query_params = http_build_query(array_merge(['search' => $search], $filters));
                            ?>
                            <a href="?page=<?= $page - 1 ?>&<?= $query_params ?>" 
                               class="pagination-item <?= ($page <= 1) ? 'disabled' : '' ?>"
                               <?= ($page <= 1) ? 'onclick="return false;"' : '' ?>>
                                &laquo; Prev
                            </a>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?= $i ?>&<?= $query_params ?>" 
                                   class="pagination-item <?= ($i === $page) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <a href="?page=<?= $page + 1 ?>&<?= $query_params ?>" 
                               class="pagination-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>"
                               <?= ($page >= $total_pages) ? 'onclick="return false;"' : '' ?>>
                                Next &raquo;
                            </a>
                        </div>
                    <?php elseif ($total_internships > 0): ?>
                        <div class="pagination">
                            <div class="pagination-info">
                                Showing all <?= $total_internships ?> assignments
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/form_validation.js"></script>
</body>
</html>

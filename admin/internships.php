<?php
    $required_role = 'admin';
    include '../config/db.php';
    include '../includes/auth_check.php';

    $errors = [];
    $success_msg = "";
    $action = $_POST['action'] ?? '';

    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'added')   $success_msg = "New internship added successfully!";
        if ($_GET['success'] === 'deleted') $success_msg = "Internship deleted successfully!";
        if ($_GET['success'] === 'edited')  $success_msg = "Internship updated successfully!";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $student_id = (int) ($_POST['student_id'] ?? 0);
        $lecturer_id = (int) ($_POST['lecturer_id'] ?? 0);
        $supervisor_id = (int) ($_POST['supervisor_id'] ?? 0);
        $company_name = trim($_POST['company_name'] ?? '');
        $semester = $_POST['semester'] ?? '';
        $internship_year = (int) ($_POST['internship_year'] ?? 0);

        if (empty($student_id)) $errors[]      = "Please select a valid student.";
        if (empty($lecturer_id)) $errors[]     = "Please select a valid lecturer.";
        if (empty($supervisor_id)) $errors[]   = "Please select a valid industry supervisor.";
        if (empty($company_name)) $errors[]    = "Please enter a company name.";
        if (empty($semester)) $errors[]        = "Please select a valid semester.";
        if (empty($internship_year)) $errors[] = "Please select a valid year.";

        if (!empty($errors)) {
            // Do Nothing.
        } else {

            if ($action === 'add') {
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
                        $errors[] = "Error: Internship already exists.";
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

    // Delete An Internship
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

    function check_internships($conn, $delete_id) {
        $check_stmt = $conn->prepare("SELECT * FROM internships WHERE internship_id = ?");
        $check_stmt->bind_param("i", $delete_id);
        $check_stmt->execute();

        $result = $check_stmt->get_result();
        $internship = $result->fetch_assoc();
        $check_stmt->close();

        return $internship;
    }

    // get internship info
    function get_internships($conn) {
        $sql = "SELECT internships.internship_id, student.student_id, student.student_name, 
                         industry_supervisor.fullname AS supervisor_name, lecturer.fullname AS lecturer_name, 
                         internships.company_name, internships.semester, internships.internship_year,
                         (SELECT COUNT(*) FROM assessment WHERE assessment.internship_id = internships.internship_id) AS assessment_count
                  FROM internships
                  JOIN student ON internships.student_id = student.student_id
                  LEFT JOIN user AS industry_supervisor ON internships.industry_supervisor_id = industry_supervisor.user_id
                  LEFT JOIN user AS lecturer ON internships.lecturer_id = lecturer.user_id";
        
        $result = $conn->query($sql);
        return $result;
    }

    // get specific assessor list
    function get_lecturers($conn) {
        $sql = "SELECT * FROM user WHERE role = 'lecturer' ORDER BY fullname ASC";
        $result = $conn->query($sql);
        return $result;
    }
    function get_supervisors($conn) {
        $sql = "SELECT * FROM user WHERE role = 'industry_supervisor' ORDER BY fullname ASC";
        $result = $conn->query($sql);
        return $result;
    }

    // get students
    function get_students($conn) {
        $sql = "SELECT * FROM student ORDER BY student_name ASC";
        $result = $conn->query($sql);
        return $result;
    }


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

                <!-- Add Form (Hidden by default) -->
                <div class="card collapse-form" id="addForm">
                    <h3 class="mb-20">Assign Assessor to Student Internship</h3>
                    <form action="" method="post" class="form-grid">
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Please Select a Student</option>
                                <?php while ($row = $students->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['student_id']) ?>"><?= htmlspecialchars($row['student_name']) ?> - <?= htmlspecialchars($row['student_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lecturer</label>
                            <select name="lecturer_id" class="form-control" required>
                                <option value="">Please Select a Lecturer</option>
                                <?php while ($row = $lecturers->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>"><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Row 2: Industry Supervisor and Company Details -->
                        <div class="form-group">
                            <label>Industry Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                <option value="">Please Select an Industry Supervisor</option>
                                <?php while ($row = $supervisors->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['user_id']) ?>"><?= htmlspecialchars($row['fullname']) ?> - <?= htmlspecialchars($row['user_id']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" placeholder="e.g. Acme Corporation" required>
                        </div>
                        
                        <!-- Row 3: Timeline -->
                        <div class="form-group">
                            <label>Semester</label>
                            <select name="semester" class="form-control" required>
                                <?php foreach($semesters as $semester): ?>
                                    <option value="<?= htmlspecialchars($semester) ?>"><?= htmlspecialchars($semester) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" name="internship_year" id="internship_year" class="form-control" placeholder="e.g. 2026" required>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
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
                                            <a href="#" class="action-edit">Edit</a>
                                            <a href="?delete_id=<?= $internship_id ?>" class="action-revoke" onclick="return confirm ('Do you want to delete this internship? This cannot be undone.')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const currentYear = new Date().getFullYear();
        document.querySelector('#internship_year').value = currentYear;
    </script>
</body>
</html>

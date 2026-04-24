<?php

    // ============================================================
    // functions.php — Shared Helper Functions
    // ============================================================


    // ---- User / Assessor Functions ----

    function get_user($conn, $user_id) {
        $sql = "SELECT * FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ?: false;
    }


    // ---- Student Functions ----

    function get_student($conn, $student_id) {
        $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        return $student ?: false;
    }

    function get_students($conn) {
        $result = $conn->query("SELECT s.*, p.programme_name 
                                FROM student s 
                                JOIN programme p ON s.programme_id = p.programme_id 
                                ORDER BY s.student_name ASC");
        return $result;
    }

    function get_students_paged($conn, $limit, $offset, $search = "") {
        $sql = "SELECT s.*, p.programme_name 
                FROM student s 
                JOIN programme p ON s.programme_id = p.programme_id";
        
        if (!empty($search)) {
            $sql .= " WHERE s.student_name LIKE ? OR s.student_id LIKE ?";
        }
        
        $sql .= " ORDER BY s.student_name ASC LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function count_students($conn, $search = "") {
        $sql = "SELECT COUNT(*) as total FROM student";
        if (!empty($search)) {
            $sql .= " WHERE student_name LIKE ? OR student_id LIKE ?";
        }
        
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'];
    }


    // ---- Programme Functions ----

    function get_programme($conn, $programme_id) {
        $stmt = $conn->prepare("SELECT * FROM programme WHERE programme_id = ?");
        $stmt->bind_param("i", $programme_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $programme = $result->fetch_assoc();
        $stmt->close();

        return $programme ?: false;
    }

    function get_programmes_paged($conn, $limit, $offset, $search = "") {
        $sql = "SELECT * FROM programme";
        if (!empty($search)) {
            $sql .= " WHERE programme_name LIKE ?";
        }
        $sql .= " ORDER BY programme_name ASC LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function count_programmes($conn, $search = "") {
        $sql = "SELECT COUNT(*) as total FROM programme";
        if (!empty($search)) {
            $sql .= " WHERE programme_name LIKE ?";
        }
        
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("s", $searchTerm);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'];
    }


    // ---- Internship Functions ----

    function check_internships($conn, $internship_id) {
        $stmt = $conn->prepare("SELECT * FROM internships WHERE internship_id = ?");
        $stmt->bind_param("i", $internship_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $internship = $result->fetch_assoc();
        $stmt->close();

        return $internship ?: false;
    }

    function get_internships($conn) {
        return get_internships_paged($conn, 9999, 0); // Legacy support
    }

    function get_internships_paged($conn, $limit, $offset, $search = "", $filters = []) {
        $sql = "SELECT i.internship_id, s.student_id, s.student_name,
                       u_is.fullname AS supervisor_name, u_l.fullname AS lecturer_name,
                       i.company_name, i.semester, i.internship_year,
                       (SELECT COUNT(*) FROM assessment a WHERE a.internship_id = i.internship_id) AS assessment_count
                FROM internships i
                JOIN student s ON i.student_id = s.student_id
                LEFT JOIN user u_is ON i.industry_supervisor_id = u_is.user_id
                LEFT JOIN user u_l ON i.lecturer_id = u_l.user_id";

        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(s.student_name LIKE ? OR s.student_id LIKE ? OR i.company_name LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        if (!empty($filters['lecturer_id'])) {
            $where[] = "i.lecturer_id = ?";
            $params[] = $filters['lecturer_id'];
            $types .= "i";
        }
        if (!empty($filters['supervisor_id'])) {
            $where[] = "i.industry_supervisor_id = ?";
            $params[] = $filters['supervisor_id'];
            $types .= "i";
        }
        if (!empty($filters['semester'])) {
            $where[] = "i.semester = ?";
            $params[] = $filters['semester'];
            $types .= "s";
        }
        if (!empty($filters['year'])) {
            $where[] = "i.internship_year = ?";
            $params[] = $filters['year'];
            $types .= "i";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY i.internship_id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function count_internships($conn, $search = "", $filters = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM internships i
                JOIN student s ON i.student_id = s.student_id";

        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(s.student_name LIKE ? OR s.student_id LIKE ? OR i.company_name LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        if (!empty($filters['lecturer_id'])) {
            $where[] = "i.lecturer_id = ?";
            $params[] = $filters['lecturer_id'];
            $types .= "i";
        }
        if (!empty($filters['supervisor_id'])) {
            $where[] = "i.industry_supervisor_id = ?";
            $params[] = $filters['supervisor_id'];
            $types .= "i";
        }
        if (!empty($filters['semester'])) {
            $where[] = "i.semester = ?";
            $params[] = $filters['semester'];
            $types .= "s";
        }
        if (!empty($filters['year'])) {
            $where[] = "i.internship_year = ?";
            $params[] = $filters['year'];
            $types .= "i";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'];
    }

    function get_assessors_paged($conn, $limit, $offset, $search = "") {
        $sql = "SELECT user_id, username, fullname, role, email FROM user WHERE (role='industry_supervisor' OR role='lecturer')";
        
        if (!empty($search)) {
            $sql .= " AND (fullname LIKE ? OR username LIKE ? OR email LIKE ?)";
        }
        
        $sql .= " ORDER BY user_id DESC LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function count_assessors($conn, $search = "") {
        $sql = "SELECT COUNT(*) as total FROM user WHERE (role='industry_supervisor' OR role='lecturer')";
        if (!empty($search)) {
            $sql .= " AND (fullname LIKE ? OR username LIKE ? OR email LIKE ?)";
        }
        
        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'];
    }

    function get_lecturers($conn) {
        $result = $conn->query("SELECT * FROM user WHERE role = 'lecturer' ORDER BY fullname ASC");
        return $result;
    }

    function get_supervisors($conn) {
        $result = $conn->query("SELECT * FROM user WHERE role = 'industry_supervisor' ORDER BY fullname ASC");
        return $result;
    }

    function check_assessor_has_marks($conn, $internship_id, $assessor_id) {
        $sql = "SELECT assessment_id FROM assessment WHERE internship_id = ? AND assessor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $internship_id, $assessor_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows > 0;
        } else {
            $stmt->close();
            return false;
        }
    }

    function delete_assessor_marks($conn, $internship_id, $assessor_id) {
        $stmt = $conn->prepare("DELETE FROM assessment WHERE internship_id = ? AND assessor_id = ?");
        $stmt->bind_param("ii", $internship_id, $assessor_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }


    // ---- Assessor Dashboard Functions ----

    function get_student_assessor($conn, $user_id) {
        return get_student_assessor_paged($conn, $user_id, 9999, 0); // Legacy support
    }

    function get_student_assessor_paged($conn, $user_id, $limit, $offset) {
        $sql = "SELECT s.student_id, s.student_name, i.internship_id, i.lecturer_id, i.industry_supervisor_id,
        i.company_name, i.semester, i.internship_year, p.programme_name,
        IF(a.assessment_id IS NULL, 'Pending', 'Completed') AS status
        FROM internships i
        JOIN student s ON i.student_id = s.student_id
        JOIN programme p ON s.programme_id = p.programme_id
        LEFT JOIN assessment a ON a.internship_id = i.internship_id AND a.assessor_id = ?
        WHERE (i.lecturer_id = ? OR i.industry_supervisor_id = ?)
        ORDER BY i.internship_id DESC LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $limit, $offset);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return false;
        }
    }

    function count_student_assessor($conn, $user_id) {
        $sql = "SELECT COUNT(*) as total 
                FROM internships i
                WHERE (i.lecturer_id = ? OR i.industry_supervisor_id = ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result['total'];
        } else {
            $stmt->close();
            return 0;
        }
    }
    
    
    function submit_evaluation($conn, $user_id, $internship_id,
        $score_tasks, $comment_tasks, $score_health, $comment_health,
        $score_knowledge, $comment_knowledge, $score_report, $comment_report,
        $score_language, $comment_language, $score_lifelong, $comment_lifelong,
        $score_project, $comment_project, $score_time, $comment_time, $comments) {

        $sql = "INSERT INTO assessment (
            internship_id, assessor_id, task_projects, task_projects_comment,
            health_safety, health_safety_comment, theoretical_knowledge, theoretical_knowledge_comment,
            report_presentation, report_presentation_comment, clarity_of_language, clarity_of_language_comment,
            lifelong_learning, lifelong_learning_comment, project_management, project_management_comment,
            time_management, time_management_comment, overall_comments
            
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?

        )";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisisisisisisisiss",
            $internship_id, $user_id, $score_tasks, $comment_tasks,
            $score_health, $comment_health, $score_knowledge, $comment_knowledge,
            $score_report, $comment_report, $score_language, $comment_language,
            $score_lifelong, $comment_lifelong, $score_project, $comment_project,
            $score_time, $comment_time, $comments
        );

        try {
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            $stmt->close();
            return false;
        }

        $stmt->close();
        return false;
    }


    function get_student_result($conn, $user_id, $internship_id) {
        $sql = "SELECT s.student_name, s.student_id, i.company_name, p.programme_name, i.semester, i.internship_year, a.task_projects, a.task_projects_comment,
        a.health_safety, a.health_safety_comment, a.theoretical_knowledge, a.theoretical_knowledge_comment,
        a.report_presentation, a.report_presentation_comment, a.clarity_of_language, a.clarity_of_language_comment,
        a.lifelong_learning, a.lifelong_learning_comment, a.project_management, a.project_management_comment,
        a.time_management, a.time_management_comment, a.overall_comments, a.total_marks
        FROM assessment a
        JOIN internships i ON a.internship_id = i.internship_id
        JOIN student s ON i.student_id = s.student_id
        JOIN programme p ON s.programme_id = p.programme_id
        WHERE (a.internship_id = ? AND a.assessor_id = ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $internship_id, $user_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;

        } else {
            $stmt->close();
            return false;
        }

    }

    function get_all_student_results($conn, $internship_id) {
        $sql = "SELECT a.*, u.role as assessor_role, u.fullname as assessor_name,
                i.company_name, p.programme_name, i.semester, i.internship_year, s.student_name, s.student_id
                FROM assessment a
                JOIN user u ON a.assessor_id = u.user_id
                JOIN internships i ON a.internship_id = i.internship_id
                JOIN student s ON i.student_id = s.student_id
                JOIN programme p ON s.programme_id = p.programme_id
                WHERE a.internship_id = ?
                ORDER BY u.role DESC"; // Order by role to group them consistently

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $internship_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;

        } else {
            $stmt->close();
            return false;
        }
    }

?>
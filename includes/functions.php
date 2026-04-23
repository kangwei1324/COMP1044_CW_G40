<?php

    // ============================================================
    // functions.php — Shared Helper Functions
    // ============================================================


    // ---- User / Assessor Functions ----

    function get_user($conn, $user_id) {
        // Grab all info except password
        $sql = "SELECT user_id, username, fullname, email, role FROM user WHERE user_id = ?";
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
        $result = $conn->query("SELECT * FROM student ORDER BY student_name ASC");
        return $result;
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
        $sql = "SELECT s.student_id, s.student_name, i.internship_id, i.lecturer_id, i.industry_supervisor_id,
        i.company_name, i.semester, i.internship_year, p.programme_name,
        IF(a.assessment_id IS NULL, 'Pending', 'Completed') AS status
        FROM internships i
        JOIN student s ON i.student_id = s.student_id
        JOIN programme p ON s.programme_id = p.programme_id
        LEFT JOIN assessment a ON a.internship_id = i.internship_id AND a.assessor_id = ?
        WHERE (i.lecturer_id = ? OR i.industry_supervisor_id = ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $user_id, $user_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;

        } else {
            $stmt->close();
            return false;
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

        if ($stmt->execute()) {
            $stmt->close();
            return true;

        } else {
            $stmt->close();
            return false;
        }

    }


    function get_student_result($conn, $user_id, $internship_id) {
        $sql = "SELECT i.company_name, p.programme_name, i.semester, i.internship_year, a.task_projects, a.task_projects_comment,
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
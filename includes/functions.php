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
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        return $result; // Returns mysqli_result or false
    }

?>
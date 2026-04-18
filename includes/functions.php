<?php

    function get_user($conn, $user_id) {
        // grab all info except password
        $sql = "SELECT user_id, username, fullname, email, role FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            return $user;
        }

        return false;
    }

    
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
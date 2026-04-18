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

    
    function get_student_assesor($conn, $user_id) {
        // Get all necessary data for displaying assigned students to assessor
        // COALESCE: Takes null as 'Pending' since assessment row isn't created until assessor submit their marks
        $sql = "SELECT s.student_id, s.student_name, i.internship_id, i.lecturer_id, i.industry_supervisor_id,
        i.company_name, p.programme_name, COALESCE(a.status, 'Pending') AS status
        FROM internships i
        JOIN student s ON i.student_id = s.student_id
        JOIN programme p ON s.programme_id = p.programme_id
        LEFT JOIN assessment a ON a.internship_id = i.internship_id AND a.assessor_id = ?
        WHERE (i.lecturer_id = ? OR i.industry_supervisor_id = ?)";

        // Prepare the statement for execution
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $user_id, $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        //$assigned_students = $result->fetch_assoc();
        $stmt->close();

        // Return the result if it exist 
        if ($result) {
            return $result;
        }

        // Return false if no results
        return false;
    }
    

?>
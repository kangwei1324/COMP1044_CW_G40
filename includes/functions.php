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

?>
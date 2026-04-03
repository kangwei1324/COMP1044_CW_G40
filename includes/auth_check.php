<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        session_destroy();
        header("Location: ../auth/login.php");
        exit;
    }

    if (isset($required_role)) {

        if ($required_role === 'assessor') {
            if ($_SESSION['role'] !== 'industry_supervisor' && $_SESSION['role'] !== 'lecturer') {
                header("Location: ../403.html");
                exit;
            }
        } elseif ($_SESSION['role'] !== $required_role) {
            header("Location: ../403.html");
            exit;
        }
    }
?>
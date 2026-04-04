<?php

    // index.php is used to direct the user to the login page if they are not logged in
    // else, they will be redirected to their respective dashboards based on the role.
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth/login.php");
        exit;
    }

    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'industry_supervisor' || $_SESSION['role'] === 'lecturer') {
        header("Location: assessor/dashboard.php");
        exit;
    } else {
        // logs out user if they have logged in but have invalid or unknown role
        header("Location: auth/logout.php");
        exit;
    }
?>
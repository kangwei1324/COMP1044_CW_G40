<?php
    // Error display for debugging
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    $required_role = 'assessor';
    include '../includes/auth_check.php';
    include '../config/db.php';
    include '../includes/functions.php';

    $user_id = $_SESSION['user_id'];
    
    if (isset($_POST['submit_evaluation'])) {
        // Take all input into variable
        $internship_id = $_POST['internship_id'];
        $student_id = $_POST['student_id'];

        $score_tasks = $_POST['score_tasks'];
        $comment_tasks = $_POST['comment_tasks'];

        $score_health = $_POST['score_health'];
        $comment_health = $_POST['comment_health'];

        $score_knowledge = $_POST['score_knowledge'];
        $comment_knowledge = $_POST['comment_knowledge'];

        $score_report = $_POST['score_report'];
        $comment_report = $_POST['comment_report'];

        $score_language = $_POST['score_language'];
        $comment_language = $_POST['comment_language'];

        $score_lifelong = $_POST['score_lifelong'];
        $comment_lifelong = $_POST['comment_lifelong'];

        $score_project = $_POST['score_project'];
        $comment_project = $_POST['comment_project'];

        $score_time = $_POST['score_time'];
        $comment_time = $_POST['comment_time'];

        $comments = $_POST['comments'];

        // Send all input and update Database
        if (submit_evaluation(
            $conn, $user_id, $internship_id,
            $score_tasks, $comment_tasks, $score_health, $comment_health,
            $score_knowledge, $comment_knowledge, $score_report, $comment_report,
            $score_language, $comment_language, $score_lifelong, $comment_lifelong,
            $score_project, $comment_project, $score_time, $comment_time, $comments)
            ) {

            header("Location: dashboard.php");
            exit();

        } else {
            echo "Error: Submission Failed! Please Try Again!";
        }

    }

?>
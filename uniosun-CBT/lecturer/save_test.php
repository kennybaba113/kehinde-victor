<?php
session_start();
include '../includes/connect.php';



if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lecturer_id = $_SESSION['lecturer_id'];
    $course_code = $_POST['course_code'];
    $course_title = $_POST['course_title'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $semester = $_POST['semester'];
    $max_students = $_POST['max_students'];
    $duration = $_POST['duration'];
    
    // Use posted value directly
    $allow_results = isset($_POST['allow_results']) ? $_POST['allow_results'] : 0;

    $sql = "INSERT INTO tests (lecturer_id, course_code, course_title, department, level, semester, max_students, allow_results, duration, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssiis", $lecturer_id, $course_code, $course_title, $department, $level, $semester, $max_students, $allow_results, $duration);

    if ($stmt->execute()) {
        $test_id = $conn->insert_id;
        header("Location: add_questions.php?test_id=" . $test_id);
        exit();
    } else {
        echo "Error creating test: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

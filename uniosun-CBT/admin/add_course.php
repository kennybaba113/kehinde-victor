<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch departments from DB
$departments = $conn->query("SELECT * FROM department ORDER BY department_name ASC");

// Handle form submission
if (isset($_POST['add_course'])) {
    $department = $_POST['department'];
    $course_code = strtoupper(trim($_POST['course_code']));
    $course_title = trim($_POST['course_title']);
    $level = $_POST['level'];
    $semester = $_POST['semester'];

    // Validation
    if (empty($department) || empty($course_code) || empty($course_title) || empty($level) || empty($semester)) {
        $message = "<div class='alert alert-danger'>All fields are required!</div>";
    } else {
        // Check for duplicate course (same department + code + level + semester)
        $check = $conn->prepare("SELECT * FROM courses WHERE department = ? AND course_code = ? AND level = ? AND semester = ?");
        $check->bind_param("ssss", $department, $course_code, $level, $semester);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Course already exists for this department, level, and semester!</div>";
        } else {
            // Insert course
            $insert = $conn->prepare("INSERT INTO courses (department, course_code, course_title, level, semester) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssss", $department, $course_code, $course_title, $level, $semester);

            if ($insert->execute()) {
                $_SESSION['success_msg'] = "Course added successfully!";
                header("Location: view_course.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error adding course!</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <a href="view_course.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Add New Course</h2>

    <?= $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <select name="department" id="department" class="form-select" required>
                <option value="">-- Select Department --</option>
                <?php while ($d = $departments->fetch_assoc()) { ?>
                    <option value="<?= $d['department_name']; ?>"><?= $d['department_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" name="course_code" id="course_code" class="form-control" placeholder="E.g. CSC101" required>
        </div>

        <div class="mb-3">
            <label for="course_title" class="form-label">Course Title</label>
            <input type="text" name="course_title" id="course_title" class="form-control" placeholder="E.g. Introduction to Computer Science" required>
        </div>

        <div class="mb-3">
            <label for="level" class="form-label">Level</label>
            <select name="level" id="level" class="form-select" required>
                <option value="">-- Select Level --</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <select name="semester" id="semester" class="form-select" required>
                <option value="">-- Select Semester --</option>
                <option value="First">First</option>
                <option value="Second">Second</option>
            </select>
        </div>

        <button type="submit" name="add_course" class="btn btn-success">Add Course</button>
    </form>

</div>

</body>
</html>

<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get course ID from URL
if (!isset($_GET['id'])) {
    header("Location: view_course.php");
    exit();
}

$course_id = $_GET['id'];
$message = "";

// Fetch current course
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_msg'] = "Course not found!";
    header("Location: view_course.php");
    exit();
}

$course = $result->fetch_assoc();

// Fetch departments for dropdown
$departments = $conn->query("SELECT * FROM department ORDER BY department_name ASC");

// Handle form submission
if (isset($_POST['update_course'])) {
    $department = $_POST['department'];
    $course_code = strtoupper(trim($_POST['course_code']));
    $course_title = trim($_POST['course_title']);
    $level = $_POST['level'];
    $semester = $_POST['semester'];

    if (empty($department) || empty($course_code) || empty($course_title) || empty($level) || empty($semester)) {
        $message = "<div class='alert alert-danger'>All fields are required!</div>";
    } else {
        // Check for duplicate (excluding current course)
        $check = $conn->prepare("SELECT * FROM courses WHERE department = ? AND course_code = ? AND level = ? AND semester = ? AND id != ?");
        $check->bind_param("ssssi", $department, $course_code, $level, $semester, $course_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Course already exists for this department, level, and semester!</div>";
        } else {
            // Update course
            $update = $conn->prepare("UPDATE courses SET department = ?, course_code = ?, course_title = ?, level = ?, semester = ? WHERE id = ?");
            $update->bind_param("sssssi", $department, $course_code, $course_title, $level, $semester, $course_id);

            if ($update->execute()) {
                $_SESSION['success_msg'] = "Course updated successfully!";
                header("Location: view_course.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error updating course!</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <a href="view_courses.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Edit Course</h2>

    <?= $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <select name="department" id="department" class="form-select" required>
                <option value="">-- Select Department --</option>
                <?php while ($d = $departments->fetch_assoc()) { ?>
                    <option value="<?= $d['department_name']; ?>" <?= ($course['department'] == $d['department_name']) ? "selected" : ""; ?>>
                        <?= $d['department_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" name="course_code" id="course_code" class="form-control" 
                   value="<?= htmlspecialchars($course['course_code']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="course_title" class="form-label">Course Title</label>
            <input type="text" name="course_title" id="course_title" class="form-control" 
                   value="<?= htmlspecialchars($course['course_title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="level" class="form-label">Level</label>
            <select name="level" id="level" class="form-select" required>
                <?php foreach (['100','200','300','400','500','600'] as $lvl) { ?>
                    <option value="<?= $lvl; ?>" <?= ($course['level'] == $lvl) ? "selected" : ""; ?>><?= $lvl; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <select name="semester" id="semester" class="form-select" required>
                <option value="First" <?= ($course['semester'] == 'First') ? "selected" : ""; ?>>First</option>
                <option value="Second" <?= ($course['semester'] == 'Second') ? "selected" : ""; ?>>Second</option>
            </select>
        </div>

        <button type="submit" name="update_course" class="btn btn-success">Update Course</button>
    </form>

</div>

</body>
</html>

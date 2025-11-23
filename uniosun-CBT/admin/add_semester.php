<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle form submission
if (isset($_POST['add_semester'])) {
    $semester_name = $_POST['semester_name'];

    // Check if semester already exists
    $check = $conn->prepare("SELECT * FROM semester WHERE semester_name = ?");
    $check->bind_param("s", $semester_name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Semester already exists!</div>";
    } else {
        $insert = $conn->prepare("INSERT INTO semester (semester_name) VALUES (?)");
        $insert->bind_param("s", $semester_name);
        if ($insert->execute()) {
            $_SESSION['success_msg'] = "Semester added successfully!";
            header("Location: view_semester.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Error adding semester!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Semester</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <a href="view_semesters.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Add Semester</h2>

    <?= $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="semester_name" class="form-label">Semester Name</label>
            <select name="semester_name" id="semester_name" class="form-select" required>
                <option value="">-- Select Semester --</option>
                <option value="First">First</option>
                <option value="Second">Second</option>
            </select>
        </div>

        <button type="submit" name="add_semester" class="btn btn-success">Add Semester</button>
    </form>

</div>
</body>
</html>

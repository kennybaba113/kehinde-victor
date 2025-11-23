<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location:login.php");
    exit();
}

$students_count = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$lecturers_count = $conn->query("SELECT COUNT(*) as total FROM lecturers")->fetch_assoc()['total'];
$departments_count = $conn->query("SELECT COUNT(*) as total FROM department")->fetch_assoc()['total'];
$courses_count = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];
$semesters_count = $conn->query("SELECT COUNT(*) as total FROM semester")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<!-- Bootstrap -->
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<style>
.card-box {
    border-radius: 14px;
    padding: 25px;
    color: white;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
    transition: .3s;
}
.card-box:hover { transform: translateY(-6px); }
</style>
</head>

<body class="bg-light">

<!-- Top Navbar -->
<nav class="navbar navbar-dark bg-primary p-3">
    <div class="container-fluid">
        <button class="btn btn-light me-2" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            ☰ Menu
        </button>
        <span class="navbar-brand h4 mb-0">Admin Dashboard</span>
        <a href="login.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<!-- BOOTSTRAP OFFCANVAS SIDEBAR -->
<div class="offcanvas offcanvas-start bg-primary text-white" id="sidebar">
    <div class="offcanvas-header">
        <h4 class="offcanvas-title">Admin Panel</h4>
        <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <a href="dashboard.php" class="btn btn-light w-100 mb-2">Dashboard</a>
        <a href="lecturer.php" class="btn btn-light w-100 mb-2">Lecturers</a>
        <a href="view_department.php" class="btn btn-light w-100 mb-2">Departments</a>
        <a href="view_course.php" class="btn btn-light w-100 mb-2">Courses</a>
        <a href="view_semester.php" class="btn btn-light w-100 mb-2">Semesters</a>
        <a href="view_students.php" class="btn btn-light w-100 mb-2">Students</a>
        <a href="department_requests.php" class="btn btn-light w-100 mb-2">Department Change </a>
        <a href="login.php" class="btn btn-danger w-100 mt-3">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container mt-4">

    <h3 class="mb-3">Welcome, <?php echo $_SESSION['admin_username']; ?></h3>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card-box" style="background:#3b82f6;">
                <h4>Students</h4>
                <h2><?php echo $students_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box" style="background:#10b981;">
                <h4>Lecturers</h4>
                <h2><?php echo $lecturers_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box" style="background:#f59e0b;">
                <h4>Departments</h4>
                <h2><?php echo $departments_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box" style="background:#8b5cf6;">
                <h4>Courses</h4>
                <h2><?php echo $courses_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box" style="background:#ef4444;">
                <h4>Semesters</h4>
                <h2><?php echo $semesters_count; ?></h2>
            </div>
        </div>

    </div>
</div>

</body>
</html>

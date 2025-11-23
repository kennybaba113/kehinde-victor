<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get search & filter inputs
$search = $_GET['search'] ?? "";
$filter_department = $_GET['department'] ?? "";
$filter_level = $_GET['level'] ?? "";

// Fetch departments for filter dropdown
$departments = $conn->query("SELECT * FROM department ORDER BY department_name ASC");

// Base query
$query = "SELECT * FROM courses WHERE 1";

// Apply search
if (!empty($search)) {
    $query .= " AND (course_code LIKE '%$search%' OR course_title LIKE '%$search%')";
}

// Apply department filter
if (!empty($filter_department)) {
    $query .= " AND department = '$filter_department'";
}

// Apply level filter
if (!empty($filter_level)) {
    $query .= " AND level = '$filter_level'";
}

$query .= " ORDER BY course_code ASC";
$courses = $conn->query($query);

// DELETE course
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $del = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    $_SESSION['success_msg'] = "Course deleted successfully!";
    header("Location: view_courses.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7f9; }
        .table thead { background:#1e3a8a; color:white; }
    </style>
</head>
<body>

<div class="container mt-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Courses List</h2>

    <!-- SUCCESS MESSAGE -->
    <?php 
    if (isset($_SESSION['success_msg'])) {
        echo "<div class='alert alert-success'>".$_SESSION['success_msg']."</div>";
        unset($_SESSION['success_msg']);
    }
    ?>

    <!-- SEARCH + FILTER FORM -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" 
                   class="form-control" placeholder="Search course...">
        </div>

        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">-- Filter by Department --</option>
                <?php while ($d = $departments->fetch_assoc()) { ?>
                    <option value="<?= $d['department_name']; ?>" <?= ($filter_department == $d['department_name']) ? "selected" : ""; ?>>
                        <?= $d['department_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="level" class="form-select">
                <option value="">-- Filter by Level --</option>
                <?php foreach (['100','200','300','400','500','600'] as $lvl) { ?>
                    <option value="<?= $lvl; ?>" <?= ($filter_level == $lvl) ? "selected" : ""; ?>><?= $lvl; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>

    </form>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Department</th>
            <th>Level</th>
            <th>Semester</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php 
        $sn = 1;
        if ($courses->num_rows > 0) {
            while ($row = $courses->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sn++; ?></td>
                    <td><?= $row['course_code']; ?></td>
                    <td><?= $row['course_title']; ?></td>
                    <td><?= $row['department']; ?></td>
                    <td><?= $row['level']; ?></td>
                    <td><?= $row['semester']; ?></td>
                    <td>
                        <a href="edit_course.php?id=<?= $row['id']; ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        <a href="view_course.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this course?')">
                           Delete
                        </a>
                    </td>
                </tr>
        <?php } 
        } else { ?>
            <tr>
                <td colspan="7" class="text-center text-danger">No courses found!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="add_course.php" class="btn btn-success mt-3">+ Add New Course</a>

</div>

</body>
</html>

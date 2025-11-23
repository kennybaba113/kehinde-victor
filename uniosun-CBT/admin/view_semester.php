<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// DELETE semester
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = $conn->prepare("DELETE FROM semester WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    $_SESSION['success_msg'] = "Semester deleted successfully!";
    header("Location: view_semester.php");
    exit();
}

// Fetch all semesters
$semesters = $conn->query("SELECT * FROM semester ORDER BY semester_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Semesters</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7f9; }
        .table thead { background:#1e3a8a; color:white; }
    </style>
</head>
<body>

<div class="container mt-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Semesters List</h2>

    <?php 
    if (isset($_SESSION['success_msg'])) {
        echo "<div class='alert alert-success'>".$_SESSION['success_msg']."</div>";
        unset($_SESSION['success_msg']);
    }
    ?>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Semester Name</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        $sn = 1;
        if ($semesters->num_rows > 0) {
            while ($row = $semesters->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sn++; ?></td>
                    <td><?= $row['semester_name']; ?></td>
                    <td>
                        <a href="view_semester.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this semester?')">Delete</a>
                    </td>
                </tr>
        <?php } } else { ?>
            <tr>
                <td colspan="3" class="text-center text-danger">No semester found!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="add_semester.php" class="btn btn-success mt-3">+ Add Semester</a>

</div>
</body>
</html>

<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// GET SEARCH INPUT
$search = $_GET['search'] ?? "";

// BASE QUERY
$query = "SELECT * FROM department WHERE 1";

// APPLY SEARCH
if (!empty($search)) {
    $query .= " AND department_name LIKE '%$search%'";
}

$query .= " ORDER BY department_name ASC";
$departments = $conn->query($query);

// DELETE department
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $del = $conn->prepare("DELETE FROM department WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    $_SESSION['success_msg'] = "Department deleted successfully!";
    header("Location: view_department.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Departments</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f7f9; }
        .table thead { background:#1e3a8a; color:white; }
    </style>
</head>
<body>

<div class="container mt-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Departments List</h2>

    <!-- SUCCESS MESSAGE -->
    <?php 
    if (isset($_SESSION['success_msg'])) {
        echo "<div class='alert alert-success'>".$_SESSION['success_msg']."</div>";
        unset($_SESSION['success_msg']);
    }
    ?>

    <!-- SEARCH FORM -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" value="<?= $search; ?>" 
                   class="form-control" placeholder="Search department...">
        </div>
        <div class="col-md-6">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Department Name</th>
            <th>Max Level</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php 
        $sn = 1;
        if ($departments->num_rows > 0) {
            while ($row = $departments->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sn++; ?></td>
                    <td><?= $row['department_name']; ?></td>
                    <td><?= $row['max_level'] ?>L</td>
                    <td>
                        <a href="edit_department.php?id=<?= $row['id']; ?>" 
                           class="btn btn-warning btn-sm">Edit</a>

                        <a href="view_department.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this department?')">
                           Delete
                        </a>
                    </td>
                </tr>
        <?php } 
        } else { ?>
            <tr>
                <td colspan="4" class="text-center text-danger">No department found!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="add_department.php" class="btn btn-success mt-3">+ Add New Department</a>

</div>

</body>
</html>

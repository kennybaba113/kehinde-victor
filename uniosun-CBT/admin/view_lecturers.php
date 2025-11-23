<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// GET ALL DEPARTMENTS
$dept_query = $conn->query("SELECT * FROM department ORDER BY department_name ASC");

// GET SEARCH AND FILTER INPUTS
$search = $_GET['search'] ?? "";
$filter_department = $_GET['department'] ?? "";

// BASE QUERY
$query = "SELECT * FROM lecturers WHERE 1";

// APPLY SEARCH 
if (!empty($search)) {
    $query .= " AND (staff_id LIKE '%$search%' 
                OR full_name LIKE '%$search%' 
                OR email LIKE '%$search%' 
                OR department LIKE '%$search%')";
}

// APPLY DEPARTMENT FILTER
if (!empty($filter_department)) {
    $query .= " AND department = '$filter_department'";
}

$query .= " ORDER BY full_name ASC";
$lecturers = $conn->query($query);

// DELETE lecturer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $del = $conn->prepare("DELETE FROM lecturers WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    $_SESSION['success_msg'] = "Lecturer deleted successfully!";
    header("Location: view_lecturers.php");
    exit();
}

// ACTIVATE / DEACTIVATE lecturer
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];

    // Fetch current status
    $get = $conn->prepare("SELECT status FROM lecturers WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $res = $get->get_result();
    $lect = $res->fetch_assoc();

    $new_status = ($lect['status'] == 1) ? 0 : 1;

    $update = $conn->prepare("UPDATE lecturers SET status = ? WHERE id = ?");
    $update->bind_param("ii", $new_status, $id);
    $update->execute();

    $_SESSION['success_msg'] = "Lecturer status updated!";
    header("Location: view_lecturers.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Lecturers</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f7f9; }
        .table thead { background:#1e3a8a; color:white; }
    </style>
</head>
<body>

<div class="container mt-4">

    <a href="lecturer.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Lecturers List</h2>

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
            <input type="text" name="search" value="<?= $search; ?>" 
                   class="form-control" placeholder="Search lecturer...">
        </div>

        <div class="col-md-4">
            <select name="department" class="form-select">
                <option value="">-- Filter by Department --</option>
                <?php while ($d = $dept_query->fetch_assoc()) { ?>
                    <option value="<?= $d['department_name']; ?>"
                        <?= ($filter_department == $d['department_name']) ? "selected" : ""; ?>>
                        <?= $d['department_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>

    </form>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Staff ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Gender</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php 
        $sn = 1;
        if ($lecturers->num_rows > 0) {
            while ($row = $lecturers->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sn++; ?></td>
                    <td><?= $row['staff_id']; ?></td>
                    <td><?= $row['full_name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['department']; ?></td>
                    <td><?= ucfirst($row['gender']); ?></td>

                    <td>
                        <?php if ($row['status'] == 1) { ?>
                            <span class="badge bg-success">Active</span>
                        <?php } else { ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php } ?>
                    </td>

                    <td>
                        <!-- Toggle -->
                        <a href="view_lecturers.php?toggle=<?= $row['id']; ?>" 
                           class="btn btn-warning btn-sm">
                           <?= $row['status'] == 1 ? "Deactivate" : "Activate"; ?>
                        </a>

                        <!-- Delete -->
                        <a href="view_lecturers.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this lecturer?')">
                           Delete
                        </a>
                    </td>
                </tr>
        <?php } 
        } else { ?>
            <tr>
                <td colspan="8" class="text-center text-danger">No lecturer found!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>

<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get department ID from URL
if (!isset($_GET['id'])) {
    header("Location: view_department.php");
    exit();
}

$department_id = $_GET['id'];
$message = "";

// Fetch current department
$stmt = $conn->prepare("SELECT * FROM department WHERE id = ?");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_msg'] = "Department not found!";
    header("Location: view_department.php");
    exit();
}

$department = $result->fetch_assoc();

// Handle form submission
if (isset($_POST['update_department'])) {
    $department_name = trim($_POST['department_name']);

    if (empty($department_name)) {
        $message = "<div class='alert alert-danger'>Department name cannot be empty!</div>";
    } else {
        // Check for duplicate (excluding current department)
        $check = $conn->prepare("SELECT * FROM department WHERE department_name = ? AND id != ?");
        $check->bind_param("si", $department_name, $department_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Department name already exists!</div>";
        } else {
            // Update department
            $update = $conn->prepare("UPDATE department SET department_name = ? WHERE id = ?");
            $update->bind_param("si", $department_name, $department_id);

            if ($update->execute()) {
                $_SESSION['success_msg'] = "Department updated successfully!";
                header("Location: view_department.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error updating department!</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Department</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <a href="view_departments.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Edit Department</h2>

    <?= $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="department_name" class="form-label">Department Name</label>
            <input type="text" name="department_name" id="department_name" 
                   class="form-control" value="<?= htmlspecialchars($department['department_name']); ?>" required>
        </div>

        <button type="submit" name="update_department" class="btn btn-success">Update Department</button>
    </form>

</div>

</body>
</html>

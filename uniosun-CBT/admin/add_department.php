<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch levels from levels table
$levels = [];
$level_sql = $conn->query("SELECT level_name FROM levels ORDER BY level_name ASC");
while ($row = $level_sql->fetch_assoc()) {
    $levels[] = $row['level_name'];
}

// Handle form submission
if (isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);
    $max_level = trim($_POST['max_level']);

    if (empty($department_name) || empty($max_level)) {
        $message = "<div class='alert alert-danger'>All fields are required!</div>";
    } else {
        // Check for duplicate
        $check = $conn->prepare("SELECT * FROM department WHERE department_name = ?");
        $check->bind_param("s", $department_name);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Department already exists!</div>";
        } else {
            // Insert department with max_level
            $insert = $conn->prepare("INSERT INTO department (department_name, max_level) VALUES (?, ?)");
            $insert->bind_param("ss", $department_name, $max_level);

            if ($insert->execute()) {
                $_SESSION['success_msg'] = "Department added successfully!";
                header("Location: view_department.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error adding department!</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Department</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <a href="view_department.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h2 class="mb-3">Add New Department</h2>

    <?= $message; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Department Name</label>
            <input type="text" name="department_name" class="form-control" placeholder="Enter department name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Maximum Level</label>
            <select name="max_level" class="form-control" required>
                <option value="">-- Select Max Level --</option>
                <?php foreach ($levels as $level): ?>
                    <option value="<?= htmlspecialchars($level); ?>">
                        <?= htmlspecialchars($level); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="add_department" class="btn btn-success">Add Department</button>
    </form>

</div>

</body>
</html>

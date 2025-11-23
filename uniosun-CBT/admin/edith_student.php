<?php
session_start();
include '../includes/connect.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$id = (int)$id;

// Fetch student
$student = $conn->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();

// Fetch departments
$departments = $conn->query("SELECT * FROM department ORDER BY department_name ASC");

if(isset($_POST['update_student'])){
    $new_department = $_POST['department'];

    $stmt = $conn->prepare("UPDATE students SET department = ? WHERE id = ?");
    $stmt->bind_param("si", $new_department, $id);
    if($stmt->execute()){
        $_SESSION['success_msg'] = "Student department updated successfully!";
        header("Location: view_students.php");
        exit();
    } else {
        $error_msg = "Error updating student.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Student Department</h2>
    <a href="view_students.php" class="btn btn-secondary mb-3">← Back</a>

    <?php if(isset($error_msg)) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($student['full_name']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label>Department</label>
            <select name="department" class="form-select" required>
                <?php while($dept = $departments->fetch_assoc()){ ?>
                    <option value="<?= $dept['department_name'] ?>" <?= ($dept['department_name']==$student['department'])?'selected':'' ?>>
                        <?= $dept['department_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" name="update_student" class="btn btn-success">Update Department</button>
    </form>
</div>
</body>
</html>

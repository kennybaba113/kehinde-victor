<?php
include '../includes/connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// AUTO GENERATE PASSWORD
function generatePassword($length = 8) {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    return substr(str_shuffle($chars), 0, $length);
}

$generated_password = generatePassword();
$message = "";

// Fetch departments from DB
$departments = $conn->query("SELECT id, department_name FROM department");

if (isset($_POST['add_lecturer'])) {

    $staff_id = $_POST['staff_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $gender = $_POST['gender'];
    $plain_password = $_POST['generated_password'];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // 1. CHECK IF STAFF ID ALREADY EXISTS
    $check_staff = $conn->prepare("SELECT id FROM lecturers WHERE staff_id = ?");
    $check_staff->bind_param("s", $staff_id);
    $check_staff->execute();
    $check_staff->store_result();

    if ($check_staff->num_rows > 0) {
        $message = "<div class='alert alert-danger'>This Staff ID already exists.</div>";

    } else {

        // 2. CHECK IF EMAIL ALREADY EXISTS
        $check_email = $conn->prepare("SELECT id FROM lecturers WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Email already exists. Choose another one.</div>";

        } else {

            // 3. INSERT LECTURER (SAFE)
            $stmt = $conn->prepare("INSERT INTO lecturers (staff_id, full_name, email, department, gender, password) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $staff_id, $full_name, $email, $department, $gender, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Lecturer added successfully!";
                header("Location:lecturer.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error saving lecturer.</div>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Lecturer</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #eef2f7; }
.card { border-radius: 15px; padding: 25px; }
</style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-primary">Add Lecturer</h2>

    <?= $message ?>

    <div class="card shadow p-4">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Staff ID</label>
                <input type="text" name="staff_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php while($dept = $departments->fetch_assoc()): ?>
                        <option value="<?= $dept['department_name'] ?>"><?= $dept['department_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">-- Select Gender --</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Generated Password</label>
                <input type="text" name="generated_password" class="form-control" value="<?= $generated_password ?>" readonly>
                <small class="text-muted">Give this password to the lecturer.</small>
            </div>

            <button type="submit" name="add_lecturer" class="btn btn-primary w-100">
                Add Lecturer
            </button>

        </form>
    </div>
</div>

</body>
</html>

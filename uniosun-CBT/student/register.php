<?php
session_start();
include('../includes/connect.php');

// Fetch departments from database
$department_list = [];
$dept_query = "SELECT department_name FROM department ORDER BY department_name ASC";
$dept_result = mysqli_query($conn, $dept_query);
while ($row = mysqli_fetch_assoc($dept_result)) {
    $department_list[] = $row['department_name'];
}

// Fetch levels from database or define manually if not in DB
$level_list = ['100','200','300','400','500','600'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_number = trim($_POST['matric_number']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $level = trim($_POST['level']);
    $department = trim($_POST['department']);
    $semester = trim($_POST['semester']);
    $password = trim($_POST['password']);
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if matric number or email already exists
    $check = $conn->prepare("SELECT * FROM students WHERE matric_number = ? OR email = ?");
    $check->bind_param("ss", $matric_number, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "This matric number or email is already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (matric_number, full_name, email, gender, level, department, semester, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $matric_number, $full_name, $email, $gender, $level, $department, $semester, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Student registered successfully!";
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Error: Unable to register student.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration | Admin Portal</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/img/logo.png">

<style>
body, html { height: 100%; margin: 0; font-family: "Poppins", sans-serif; }
.background {
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    min-height: 100vh; display: flex; justify-content: center; align-items: center; position: relative; overflow: hidden;
}
.background::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.6); z-index:0; }
.container { position: relative; z-index:1; max-width: 420px; width: 90%; }
.card { background: #fff; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.2); animation: fadeIn 0.8s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
.card-header { background-color:#007bff; color:#fff; text-align:center; border-radius:15px 15px 0 0; padding:15px; }
.btn-primary { background-color:#007bff; border:none; width:100%; padding:12px; font-weight:500; }
.btn-primary:hover { background-color:#0056b3; }
.alert { font-size: 14px; }
</style>
</head>
<body>

<section class="background">
<div class="container">
<div class="card p-3">
    <div class="card-header">
        <h4>Student Registration</h4>
    </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if(isset($_SESSION['success_msg'])) { echo "<div class='alert alert-success'>".$_SESSION['success_msg']."</div>"; unset($_SESSION['success_msg']); } ?>
        <form method="POST">
            <div class="mb-3">
                <label>Matric Number</label>
                <input type="text" name="matric_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Level</label>
                <select name="level" class="form-select" required>
                    <option value="">-- Select Level --</option>
                    <?php foreach($level_list as $lvl): ?>
                        <option value="<?= htmlspecialchars($lvl) ?>"><?= htmlspecialchars($lvl) ?>L</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Department</label>
                <select name="department" class="form-select" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($department_list as $dept): ?>
                        <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="">Select Semester</option>
                    <option>First</option>
                    <option>Second</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register Student</button>

            <p class="text-center mt-3 mb-0">
                Already registered? <a href="login.php" class="text-primary">Login here</a>
            </p>

        </form>
    </div>
</div>
</div>
</section>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

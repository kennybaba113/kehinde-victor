<?php
session_start();
/*include('../includes/connect.php');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id   = trim($_POST['staff_id']);
    $full_name  = trim($_POST['full_name']);
    $email      = trim($_POST['email']);
    $department = trim($_POST['department']);
    $gender     = trim($_POST['gender']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM lecturers WHERE staff_id = ?");
    $check->bind_param("s", $staff_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "❌ Staff ID already exists. Please use another one.";
    } else {
        $sql = $conn->prepare("INSERT INTO lecturers (staff_id, full_name, email, department, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssss", $staff_id, $full_name, $email, $department, $gender, $password);

        if ($sql->execute()) {
            echo "<script>
                alert('✅ Registration successful! Redirecting to login...');
                setTimeout(function(){ window.location.href = 'login.php'; }, 2000);
            </script>";
        } else {
            $message = "⚠️ Error saving data: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lecturer Registration | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">  

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: url('../assets/img/register_bg.jpg') no-repeat center center/cover;
    background-attachment: fixed;
    color: #fff;
}

body::before {
    content: "";
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    z-index: 0;
}

.register-container {
    position: relative;
    z-index: 1;
    max-width: 450px;
    margin: 50px auto;
    padding: 30px 25px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
}

h1 {
    text-align: center;
    color: #00e676;
    font-weight: 700;
    margin-bottom: 25px;
}

.form-label {
    color: #e0e0e0;
}

.form-control, .form-select {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    transition: 0.3s;
}

.form-control:focus, .form-select:focus {
    background: rgba(255,255,255,0.2);
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    color: #fff;
}

.btn-register {
    background: linear-gradient(135deg, #00c853, #007e33);
    color: #fff;
    font-weight: bold;
    border-radius: 10px;
    width: 100%;
    padding: 12px;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-register:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #007e33, #00c853);
}

.alert {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 15px;
}

.text-info { color: #00b0ff !important; }

 Mobile adjustments 
@media (max-width: 768px) {
    .register-container {
        margin: 30px 15px;
        padding: 25px 15px;
    }
    h1 { font-size: 24px; }
}

@media (max-width: 480px) {
    .register-container {
        margin: 20px 10px;
        padding: 20px 10px;
    }
    h1 { font-size: 20px; }
}
</style>
</head>

<body>
<div class="register-container shadow">
    <h1>Lecturer Registration</h1>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Staff ID</label>
            <input type="text" name="staff_id" class="form-control" placeholder="Enter staff ID" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="text" name="department" class="form-control" placeholder="Enter department" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn btn-register">Register</button>
        <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php" class="text-info">Login here</a></small>
        </div>
    </form>
</div>
</body>
</html>

<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['matric_number'])) { 
    header("Location: login.php"); 
    exit(); 
}
$matric = $_SESSION['matric_number'];

$sql = "SELECT matric_number, full_name, email, department, level, semester FROM students WHERE matric_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matric);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Profile | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    background-attachment: fixed;
    color: #fff;
}
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    z-index: 0;
}
.container {
    position: relative;
    z-index: 2;
    max-width: 700px;
    margin: 80px auto;
    padding: 20px;
}
.card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    backdrop-filter: blur(12px);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}
h4 { font-weight: 700; margin-bottom: 10px; }
p { color: #ddd; margin-bottom: 18px; }

.row-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.row-item:last-child { border-bottom: none; }
.label { color:#ccc; font-weight:600; }
.value { color:#fff; font-weight:500; }

.btn { font-weight:600; }
.btn-primary { background: #28a745; border:none; }
.btn-outline-secondary { border-color:#28a745; color:#28a745; }
.btn-outline-secondary:hover { background:#28a745; color:#fff; }
.btn-light { color:#222; }

@media(max-width:768px){
    .container { margin: 100px 12px; padding:15px; }
}
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <h4>My Profile</h4>
    <p>Your basic information</p>

    <div class="row-item">
      <div class="label">Full Name</div>
      <div class="value"><?php echo htmlspecialchars($student['full_name']); ?></div>
    </div>
    <div class="row-item">
      <div class="label">Matric No.</div>
      <div class="value"><?php echo htmlspecialchars($student['matric_number']); ?></div>
    </div>
    <div class="row-item">
      <div class="label">Email</div>
      <div class="value"><?php echo htmlspecialchars($student['email']); ?></div>
    </div>
    <div class="row-item">
      <div class="label">Department</div>
      <div class="value"><?php echo htmlspecialchars($student['department']); ?></div>
    </div>
    <div class="row-item">
      <div class="label">Level</div>
      <div class="value"><?php echo htmlspecialchars($student['level']); ?></div>
    </div>
    <div class="row-item">
      <div class="label">Semester</div>
      <div class="value"><?php echo htmlspecialchars($student['semester']); ?></div>
    </div>

    <div style="margin-top:18px;display:flex;gap:10px; flex-wrap:wrap;">
      <a href="change_password.php" class="btn btn-sm btn-primary">Change Password</a>
      <a href="department_change.php" class="btn btn-sm btn-outline-secondary">Request Dept Change</a>
      <a href="dashboard.php" class="btn btn-sm btn-light">Back to Dashboard</a>
    </div>
  </div>
</div>

</body>
</html>

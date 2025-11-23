<?php
session_start();
include '../includes/connect.php';

// Prevent caching
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login if session does not exist
if (!isset($_SESSION['matric_number'])) {
    header("Location: login.php");
    exit();
}
$matric_number = $_SESSION['matric_number'];

// Fetch student info
$student_sql = "SELECT * FROM students WHERE matric_number = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("s", $matric_number);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$department = $student['department'];
$level = $student['level'];
$semester = $student['semester'];

// Fetch active tests
$test_sql = "SELECT * FROM tests 
             WHERE department = ? 
             AND level = ? 
             AND semester = ? 
             AND is_active = 1
             ORDER BY id DESC";
$stmt = $conn->prepare($test_sql);
$stmt->bind_param("sss", $department, $level, $semester);
$stmt->execute();
$tests = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/img/logo.png">
<style>
  body, html {
    margin:0;
    padding:0;
    font-family: Arial, sans-serif;
    min-height:100vh;
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    background-attachment: fixed;
    color: #fff;
    overflow-x: hidden;
  }
  body::before {
    content:"";
    position: fixed;
    inset:0;
    background: rgba(0,0,0,0.65);
    z-index:0;
  }
  .navbar-custom {
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(10px);
    padding: 10px 20px;
    position: fixed;
    top:0;
    width:100%;
    z-index: 9999;
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .navbar-custom h5 { margin:0; font-weight:600; color:white; }
  .dashboard-container {
    position:relative;
    z-index:2;
    width:95%;
    max-width:1100px;
    margin:auto;
    padding-top:100px;
  }
  .header-card { background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); backdrop-filter: blur(12px); border-radius:15px; padding:25px; margin-bottom:30px; text-align:center; box-shadow:0 0 20px rgba(0,0,0,0.3);}
  .header-card h3 { font-weight:700; margin-bottom:10px; }
  .header-card p { margin:0; font-size:15px; color:#e6e6e6; }
  .test-card { background: rgba(255,255,255,0.06); border-radius:15px; padding:20px; color:white; transition: all .3s ease; height:100%; }
  .test-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.10); }
  .test-title { font-weight:600; font-size:18px; }
  .test-info { font-size:14px; color:#dcdcdc; }
  .btn-start { background: linear-gradient(90deg,#28a745,#218838); border:none; color:white; border-radius:8px; padding:8px 20px; transition:.3s; }
  .btn-start:hover { transform: scale(1.05); }
  .no-tests { text-align:center; margin-top:50px; }
  .no-tests img { width:120px; opacity:0.8; }
  .no-tests p { color:#ccc; font-size:17px; margin-top:15px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark navbar-custom fixed-top">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <h5 class="ms-3">📘 UNIOSUN CBT Portal</h5>
    
  </div>
</nav>

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Student Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="profile.php">👤 Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="change_password.php">🔑 Change Password</a></li>
      <li class="nav-item"><a class="nav-link" href="department_change.php">📤 Department Change</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>
</div>

<section class="dashboard-container">
  <div class="header-card">
    <h3>Welcome, <?php echo htmlspecialchars($student['full_name']); ?> 👋</h3>
    <p>
      <b>Department:</b> <?php echo htmlspecialchars($department); ?> &nbsp;|&nbsp;
      <b>Level:</b> <?php echo htmlspecialchars($level); ?> &nbsp;|&nbsp;
      <b>Semester:</b> <?php echo htmlspecialchars($semester); ?>
    </p>
  </div>

  <?php if ($tests->num_rows > 0): ?>
    <div class="row g-4">
      <?php while($test = $tests->fetch_assoc()): ?>
      <div class="col-md-4 col-sm-6">
        <div class="test-card">
          <h5 class="test-title"><?php echo htmlspecialchars($test['course_title']); ?></h5>
          <p class="test-info"><b>Code:</b> <?php echo htmlspecialchars($test['course_code']); ?></p>
          <p class="test-info"><b>Duration:</b> <?php echo htmlspecialchars($test['duration']); ?> mins</p>
          <p class="test-info"><b>Department:</b> <?php echo htmlspecialchars($test['department']); ?></p>
          <div class="d-grid mt-3">
            <a href="start_test.php?test_id=<?php echo $test['id']; ?>" class="btn btn-start">🎯 Start Test</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="no-tests">
      <img src="../assets/img/logo.png" alt="No tests">
      <p>No active tests are available for your department and level right now.</p>
    </div>
  <?php endif; ?>
</section>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

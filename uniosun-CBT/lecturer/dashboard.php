<?php

session_start();
include('../includes/connect.php');
include '../includes/page_history.php';

if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']);
}

// Restrict access if not logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch lecturer info
$lecturer_id = $_SESSION['lecturer_id'];
$sql = $conn->prepare("SELECT * FROM lecturers WHERE id = ?");
$sql->bind_param("i", $lecturer_id);
$sql->execute();
$result = $sql->get_result();
$lecturer = $result->fetch_assoc();

$lecturer_name = $lecturer['full_name'];
$department = $lecturer['department'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lecturer Dashboard | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/img/logo.png">

<style>
/* Body and background */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: url('../assets/img/logo.png') no-repeat center center/cover;
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

/* Navbar */
.navbar {
    background-color: #0b5139 !important;
}

.navbar-brand {
    font-weight: 600;
    letter-spacing: 1px;
    font-size: 1rem;
    color: #fff !important;
}

.offcanvas {
    background-color: rgba(11,81,57,0.95);
}

.offcanvas a {
    color: #fff !important;
    font-size: 1rem;
    padding: 10px;
    display: block;
    border-radius: 8px;
    transition: 0.3s;
}

.offcanvas a:hover {
    background-color: #11694f;
}

/* Welcome section */
.welcome {
    text-align: center;
    margin-top: 120px;
    animation: fadeInDown 1s ease;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Action cards */
.action-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 50px;
    z-index: 1;
    position: relative;
}

.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    color: #fff;
    border: none;
    width: 250px;
    text-align: center;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    transition: all 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
    background: rgba(255,255,255,0.25);
}

.card i {
    font-size: 45px;
    color: #00e676;
    margin-top: 25px;
}

.card-body h5 {
    margin-top: 10px;
    font-weight: 600;
}

/* Buttons inside cards */
.card-body .btn {
    margin-top: 10px;
    border-radius: 10px;
    font-weight: bold;
    background: linear-gradient(135deg, #00c853, #007e33);
    color: #fff;
    transition: all 0.3s;
}

.card-body .btn:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #007e33, #00c853);
}

/* Section visibility */
.section { display: none; animation: fadeIn 0.4s ease-in-out; }
.active-section { display: block; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Fade in container */
.container1 {
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.8s ease-in-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .welcome { margin-top: 80px; }
    .action-cards { gap: 15px; margin-top: 40px; }
    .card { width: 90%; }
}

@media (max-width: 480px) {
    .welcome { margin-top: 60px; font-size: 14px; }
    .card-body p { font-size: 14px; }
}
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler order-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand ms-auto" href="#">
            <img src="../assets/img/logo.png" width="40" class="me-2" alt=" logo">
            UNIOSUN CBT
        </a>

        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Lecturer Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="dashboard.php" class="nav-link">🏠 Dashboard</a></li>
                    <li class="nav-item"><a href="create_test.php" class="nav-link">📝 Create Test</a></li>
                    <li class="nav-item"><a href="manage_tests.php" class="nav-link">📝 Manage Test</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link">👤 Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Welcome -->
<div class="container1 text-center welcome">
    <h1>Welcome, <?php echo htmlspecialchars($lecturer_name); ?> 👋</h1>
    <p class="lead mt-2">Manage your courses, set questions, and view student results seamlessly.</p>
</div>

<!-- Action Cards -->
<div class="container action-cards">
    <div class="card">
        <i class="fa-solid fa-pen-to-square"></i>
        <div class="card-body">
            <h5>Set Questions</h5>
            <p>Create CBT questions for your students.</p>
            <a href="create_test.php" class="btn">Start</a>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-chart-bar"></i>
        <div class="card-body">
            <h5>View Results</h5>
            <p>Check and export student results easily.</p>
            <a href="manage_tests.php" class="btn">View</a>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
  // Prevent going back to cached page after logout
  window.addEventListener("pageshow", function(event) {
      if (event.persisted || window.performance && window.performance.navigation.type === 2) {
          // If page is loaded from bfcache or back navigation
          window.location.href = "login.php";
      }
  });
</script>

</script>

</body>
</html>

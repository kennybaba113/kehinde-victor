<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lecturer Dashboard</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<script src="../assets/js/bootstrap.bundle.min.js"></script>


<style>
body {
    background: #f5f5f5;
}
.card-menu {
    transition: .3s;
    cursor: pointer;
    border-radius: 15px;
}
.card-menu:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <button class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
            ☰ Menu
        </button>
        <span class="navbar-brand">Lecturer Dashboard</span>
    </div>
</nav>

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start" id="offcanvasMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">

        <a href="lecturer.php" class="btn btn-primary w-100 my-2">🏠 Dashboard</a>
        <a href="add_lecturer.php" class="btn btn-success w-100 my-2">➕ Add Lecturer</a>
        <a href="view_lecturers.php" class="btn btn-info w-100 my-2">📄 View Lecturers</a>
        <a href="toggle_lecturer.php" class="btn btn-warning w-100 my-2">🔄 Toggle Lecturer</a>

        <hr>

        <a href="dashboard.php" class="btn btn-secondary w-100 my-2">⬅ Back to Admin</a>
        <a href="logout.php" class="btn btn-danger w-100 my-2">🚪 Logout</a>

    </div>
</div>

<!-- Dashboard Content -->
<div class="container mt-4">
    <div class="row">

        <div class="col-md-4">
            <a href="add_lecturer.php" style="text-decoration:none;">
                <div class="card p-4 text-center card-menu bg-success text-white">
                    <h3>➕ Add Lecturer</h3>
                    <p>Create new lecturer account</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="view_lecturers.php" style="text-decoration:none;">
                <div class="card p-4 text-center card-menu bg-info text-white">
                    <h3>📄 View Lecturers</h3>
                    <p>See all lecturers</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="toggle_lecturer.php" style="text-decoration:none;">
                <div class="card p-4 text-center card-menu bg-warning text-white">
                    <h3>🔄 Toggle Lecturer</h3>
                    <p>Activate / Deactivate accounts</p>
                </div>
            </a>
        </div>

    </div>
</div>


<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

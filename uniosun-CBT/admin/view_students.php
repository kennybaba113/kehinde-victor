<?php
session_start();
include '../includes/connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Departments
$dept_query = $conn->query("SELECT department_name FROM department ORDER BY department_name ASC");
$departments = [];
while($d = $dept_query->fetch_assoc()) {
    $departments[] = $d['department_name'];
}

// Levels and semesters
$levels = ['100','200','300','400','500','600'];
$semesters = ['First','Second'];

// Get filter inputs
$filter_department = $_GET['department'] ?? "";
$filter_level = $_GET['level'] ?? "";

// MASS PROMOTE
if(isset($_POST['mass_promote'])){
    $dept = trim($_POST['department_filter']);
    $level = trim($_POST['level_filter']);

    $where = [];
    if($dept !== "") $where[] = "department = '".$conn->real_escape_string($dept)."'";
    if($level !== "") $where[] = "level = '".$conn->real_escape_string($level)."'";

    $where_sql = count($where) ? implode(" AND ", $where) : "1";

    // Get department's max level
    if($dept !== "") {
        $dept_res = $conn->prepare("SELECT max_level FROM department WHERE department_name=?");
        $dept_res->bind_param("s", $dept);
        $dept_res->execute();
        $dept_result = $dept_res->get_result();
        if($dept_result->num_rows > 0){
            $dept_data = $dept_result->fetch_assoc();
            $max_level = $dept_data['max_level']; // e.g., '400'
        } else {
            $max_level = '600'; // fallback
        }
    } else {
        $max_level = '600';
    }

    // Promote students only if they are below max_level
    $conn->query("
        UPDATE students 
        SET level = CASE 
            WHEN level = '100' AND '200' <= '$max_level' THEN '200'
            WHEN level = '200' AND '300' <= '$max_level' THEN '300'
            WHEN level = '300' AND '400' <= '$max_level' THEN '400'
            WHEN level = '400' AND '500' <= '$max_level' THEN '500'
            WHEN level = '500' AND '600' <= '$max_level' THEN '600'
            ELSE level
        END
        WHERE $where_sql
    ");

    // Graduate students who reached max_level
    $conn->query("
        UPDATE students
        SET status='graduated'
        WHERE level = '$max_level' AND $where_sql
    ");

    $_SESSION['success_msg'] = "Mass promotion completed!";
    header("Location: view_students.php");
    exit();
}

// MASS SWITCH SEMESTER
if(isset($_POST['mass_semester'])){
    $dept = trim($_POST['department_filter']);
    $level = trim($_POST['level_filter']);

    $where = [];
    if($dept !== "") $where[] = "department = '".$conn->real_escape_string($dept)."'";
    if($level !== "") $where[] = "level = '".$conn->real_escape_string($level)."'";

    $where_sql = count($where) ? implode(" AND ", $where) : "1";

    // Switch semester
    $conn->query("
        UPDATE students
        SET semester = CASE 
            WHEN semester = 'First' THEN 'Second'
            ELSE 'First' 
        END
        WHERE $where_sql
    ");

    $_SESSION['success_msg'] = "Semester switched successfully!";
    header("Location: view_students.php");
    exit();
}

// Fetch students
$query = "SELECT * FROM students WHERE 1";
if($filter_department != "") $query .= " AND department='".$conn->real_escape_string($filter_department)."'";
if($filter_level != "") $query .= " AND level='".$conn->real_escape_string($filter_level)."'";
$query .= " ORDER BY full_name ASC";
$students = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background:#f4f7f9; }
        .table thead { background:#1e3a8a; color:white; }
        .action-btn { margin: 2px; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-3">Students List</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- Success Message -->
    <?php if(isset($_SESSION['success_msg'])) {
        echo "<div class='alert alert-success'>".$_SESSION['success_msg']."</div>";
        unset($_SESSION['success_msg']);
    } ?>

    <!-- FILTER FORM -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">-- Department --</option>
                <?php foreach($departments as $dept){ ?>
                    <option value="<?= $dept ?>" <?= ($filter_department==$dept)?'selected':'' ?>><?= $dept ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="level" class="form-select">
                <option value="">-- Level --</option>
                <?php foreach($levels as $lvl){ ?>
                    <option value="<?= $lvl ?>" <?= ($filter_level==$lvl)?'selected':'' ?>><?= $lvl ?>L</option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>
    </form>

    <!-- MASS ACTIONS -->
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="department_filter" value="<?= htmlspecialchars($filter_department) ?>">
        <input type="hidden" name="level_filter" value="<?= htmlspecialchars($filter_level) ?>">
        <div class="col-md-3">
            <button type="submit" name="mass_promote" class="btn btn-success w-100">Promote Selected</button>
        </div>
        <div class="col-md-3">
            <button type="submit" name="mass_semester" class="btn btn-warning w-100">Switch Semester Selected</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Matric</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Level</th>
                <th>Semester</th>
                <th>Department</th>
                <th>Status</th>
                <th>Edith</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sn = 1;
            if($students->num_rows > 0){
                while($row = $students->fetch_assoc()){ ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= htmlspecialchars($row['matric_number']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= ucfirst($row['gender']) ?></td>
                        <td><?= $row['level'] ?>L</td>
                        <td><?= $row['semester'] ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td>
                        <a href="edith_student.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                    </td>

                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="8" class="text-center text-danger">No student found!</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>

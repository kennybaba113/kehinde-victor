<?php
include '../includes/connect.php';
session_start();
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit();
}

// Get lecturer department
$lecturer_id = $_SESSION['lecturer_id'];
$stmt = $conn->prepare("SELECT department FROM lecturers WHERE id = ?");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();
$lecturer_department = $lecturer['department'];

// Fetch courses only for lecturer's department
$courses = [];
$course_query = $conn->prepare("SELECT course_code, course_title FROM courses WHERE department = ? ORDER BY course_code ASC");
$course_query->bind_param("s", $lecturer_department);
$course_query->execute();
$course_result = $course_query->get_result();
while ($row = $course_result->fetch_assoc()) {
    $courses[] = $row;
}

// Fetch levels
$levels = [];
$level_query = $conn->query("SELECT level_name FROM levels ORDER BY level_name ASC");
while ($row = $level_query->fetch_assoc()) {
    $levels[] = $row['level_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Test | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="/img/logo.png" href="../assets/img/logo.png">

<style>
body { margin:0; font-family:'Poppins',sans-serif; min-height:100vh; background:url('../assets/img/logo.png') no-repeat center center/cover; background-attachment:fixed; color:#fff; }
body::before { content:""; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); backdrop-filter:blur(10px); z-index:0; }
.container { position:relative; z-index:1; padding:20px; max-width:900px; margin:80px auto 50px auto; }
.card { background:rgba(255,255,255,0.15); backdrop-filter:blur(12px); border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.2); padding:30px; color:#fff; animation:fadeIn 0.8s ease-in-out; }
.card-header { background:linear-gradient(90deg,#00e676,#007e33); border-radius:15px 15px 0 0; padding:15px 20px; font-weight:bold; font-size:1.3rem; text-align:center; }
.form-control, .form-select { border-radius:10px; border:none; padding:10px; background:rgba(255,255,255,0.1); color:#fff; }
.form-control::placeholder { color:#ddd; }
.form-select option { color:#000; background-color:#fff; }
.btn-success { background:linear-gradient(135deg,#00c853,#007e33); font-weight:bold; transition:all 0.3s ease; }
.btn-success:hover { transform:scale(1.05); background:linear-gradient(135deg,#007e33,#00c853); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity:1; transform:translateY(0);} }
@media (max-width:768px){ .container{ margin:50px 15px; padding:15px; } .card{ padding:20px; } .row>.col-md-4, .row>.col-md-6{ flex:0 0 100%; max-width:100%; margin-bottom:15px; } .card-header{ font-size:1.1rem; } }
</style>
</head>

<body>
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header">Create New Test</div>
        <div class="card-body">

            <div class="mb-3 text-end">
                <a href="dashboard.php" class="btn btn-primary">🏠 Dashboard</a>
            </div>

            <form action="save_test.php" method="POST">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Code</label>
                        <select name="course_code" id="course_code" class="form-select" required>
                            <option value="">-- Select Course Code --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course['course_code']) ?>" 
                                        data-title="<?= htmlspecialchars($course['course_title']) ?>">
                                    <?= htmlspecialchars($course['course_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course Title</label>
                        <input type="text" name="course_title" id="course_title" class="form-control" placeholder="Course Title" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-select" required>
                            <option value="">-- Select Level --</option>
                            <?php foreach($levels as $lvl): ?>
                                <option value="<?= htmlspecialchars($lvl) ?>"><?= htmlspecialchars($lvl) ?>L</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" required>
                            <option value="">-- Select Semester --</option>
                            <option value="First">First Semester</option>
                            <option value="Second">Second Semester</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Max Students</label>
                        <input type="number" name="max_students" class="form-control" placeholder="Enter Maximum Students" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control" placeholder="Enter Duration" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Allow Results After Test?</label>
                        <select name="allow_results" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Create Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#course_code').on('change', function() {
        let title = $(this).find(':selected').data('title') || '';
        $('#course_title').val(title);
    });
});
</script>
</body>
</html>

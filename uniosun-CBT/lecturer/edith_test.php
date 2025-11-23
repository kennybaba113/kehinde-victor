<?php
include '../includes/connect.php';

if (!isset($_GET['id'])) {
    die("No test selected.");
}

$test_id = intval($_GET['id']);

// Fetch current test details
$stmt = $conn->prepare("SELECT * FROM tests WHERE id = ?");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();
$test = $result->fetch_assoc();

if (!$test) {
    die("Test not found.");
}

// Handle update
if (isset($_POST['update_test'])) {
    $course_code = $_POST['course_code'];
    $course_title = $_POST['course_title'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $semester = $_POST['semester'];
    $duration = $_POST['duration'];
    $max_students = $_POST['max_students'];
    $allow_results = isset($_POST['allow_results']) ? 1 : 0;

    $update = $conn->prepare("UPDATE tests 
        SET course_code = ?, course_title = ?, department = ?, level = ?, semester = ?, duration = ?, max_students = ?, allow_results = ? 
        WHERE id = ?");
    $update->bind_param("ssssssiii", $course_code, $course_title, $department, $level, $semester, $duration, $max_students, $allow_results, $test_id);

    if ($update->execute()) {
        $msg = "Test updated successfully!";
    } else {
        $msg = "Error updating test: " . $update->error;
    }

    $update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Test</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Test - <?php echo htmlspecialchars($test['course_title']); ?></h4>
            <a href="manage_tests.php" class="btn btn-light btn-sm">← Back to Tests</a>
        </div>

        <div class="card-body">
            <?php if (isset($msg)): ?>
                <div class="alert alert-info"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Course Code</label>
                        <input type="text" name="course_code" value="<?php echo htmlspecialchars($test['course_code']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Course Title</label>
                        <input type="text" name="course_title" value="<?php echo htmlspecialchars($test['course_title']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($test['department']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Level</label>
                        <input type="text" name="level" value="<?php echo htmlspecialchars($test['level']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Semester</label>
                        <input type="text" name="semester" value="<?php echo htmlspecialchars($test['semester']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Duration (in minutes)</label>
                        <input type="number" name="duration" value="<?php echo htmlspecialchars($test['duration']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Max Students</label>
                        <input type="number" name="max_students" value="<?php echo htmlspecialchars($test['max_students']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center mt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allow_results" id="allow_results" value="1"
                                <?php if ($test['allow_results']) echo 'checked'; ?>>
                            <label class="form-check-label" for="allow_results">
                                Allow Students to View Results
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" name="update_test" class="btn btn-success w-100 mt-3">Update Test</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

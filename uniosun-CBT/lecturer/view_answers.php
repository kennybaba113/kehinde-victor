<?php
session_start();
include '../includes/connect.php';
include '../includes/page_history.php';

 if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']); // remove last page from history
}



if (!isset($_GET['test_id'])) {
    die("No test selected.");
}

$test_id = intval($_GET['test_id']);

// Fetch test info
$test = $conn->query("SELECT * FROM tests WHERE id=$test_id")->fetch_assoc();
if (!$test) {
    die("Invalid test.");
}

// Fetch students who submitted
$submissions = $conn->query("
    SELECT DISTINCT matric_number, date_taken, duration_used, total_score 
    FROM test_submissions 
    WHERE test_id=$test_id 
    ORDER BY date_taken DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Submissions - <?php echo htmlspecialchars($test['course_title']); ?></title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5>Test: <?php echo htmlspecialchars($test['course_title']); ?></h5>
            <span>Total Submissions: <?php echo $submissions->num_rows; ?></span>
        </div>
        <div class="card-body">
            <?php if ($submissions->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Matric Number</th>
                        <th>Date Taken</th>
                        <th>Duration (min)</th>
                        <th>Total Score</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while ($row = $submissions->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['matric_number']); ?></td>
                        <td><?php echo $row['date_taken']; ?></td>
                        <td><?php echo $row['duration_used']; ?></td>
                        <td><?php echo $row['total_score']; ?></td>
                        <td>
                            <a href="mark_answers.php?test_id=<?php echo $test_id; ?>&matric_number=<?php echo urlencode($row['matric_number']); ?>" class="btn btn-warning btn-sm">View Answers</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-info">No submissions found for this test.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
history.pushState(null, null, location.href); // prevent default back
window.onpopstate = function () {
    window.location.href = "<?php echo $prev_page; ?>";
};
<script>
</body>
</html>

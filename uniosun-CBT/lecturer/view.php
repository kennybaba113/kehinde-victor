<?php
session_start();
include '../includes/connect.php'; // adjust if your db file path differs
include '../includes/page_history.php';

 if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']); // remove last page from history
}

if (!isset($_GET['test_id'])) {
    die("No test selected.");
}

$test_id = intval($_GET['test_id']);

// Fetch test info
$test = $conn->query("SELECT * FROM tests WHERE id = $test_id")->fetch_assoc();
if (!$test) {
    die("Invalid test selected.");
}

$total_submissions_query = $conn->query("SELECT COUNT(*) AS total FROM test_submissions WHERE test_id = '$test_id'");
$total_submissions = $total_submissions_query->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Test Results - <?php echo htmlspecialchars($test['course_title']); ?></title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Results for: <?php echo htmlspecialchars($test['course_title']); ?></h5>
      <h5>Total Students Submitted: <?php echo $total_submissions; ?></h5>

      <button onclick="window.print()" class="btn btn-light btn-sm">🖨 Print</button>
    </div>

    <div class="card-body">
      <?php
      $sql = "SELECT * FROM results WHERE test_id = '$test_id' ORDER BY date_taken DESC";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          echo "<div class='table-responsive'>
                <table class='table table-bordered table-striped'>
                <thead class='table-dark'>
                  <tr>
                    <th>#</th>
                    <th>Matric Number</th>
                    <th>Score</th>
                    <th>Correct Answers</th>
                    <th>Total Questions</th>
                    <th>Date Taken</th>
                    <th>Duration Used</th>
                    <th>Status</th>
                    <th>Submitted Via</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>";
          
          $i = 1;
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$i}</td>
                      <td>{$row['matric_number']}</td>
                      <td>{$row['score']}</td>
                      <td>" . (!empty($row['correct_answers']) ? $row['correct_answers'] : '-') . "</td>
                      <td>{$row['total_questions']}</td>
                      <td>{$row['date_taken']}</td>
                      <td>{$row['duration_used']}</td>
                      <td>{$row['status']}</td>
                      <td>{$row['submitted_via']}</td>
                      <td>
                        <a href='view_answers.php?test_id={$row['test_id']}&matric_number={$row['matric_number']}' class='btn btn-sm btn-warning text-white'>
                          View Answers
                        </a>
                      </td>
                    </tr>";
              $i++;
          }

          echo "</tbody></table></div>";
      } else {
          echo "<div class='alert alert-warning'>No results found for this test.</div>";
      }
      ?>
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

<?php
include '../includes/connect.php';
session_start();

$test_id = $_GET['test_id'] ?? null;
$matric_number = $_GET['matric_number'] ?? null;

if (!$test_id || !$matric_number) {
    die("Invalid access.");
}

$test = $conn->query("SELECT * FROM tests WHERE id=$test_id")->fetch_assoc();

// Fetch student's answers
$answers = $conn->query("
    SELECT qa.*, q.question_text, q.question_type, q.marks, q.correct_answers 
    FROM test_answers qa
    JOIN questions q ON qa.question_id = q.id
    WHERE qa.test_id=$test_id AND qa.matric_number='$matric_number'
");

if (!$answers || $answers->num_rows === 0) {
    die("No answers found for this student.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['scores'] as $answer_id => $score) {
        $score = floatval($score);

        // Update the test_answers table
        $conn->query("UPDATE test_answers SET score=$score WHERE id=$answer_id");
    }

    // Recalculate total score and update result/submission
    $totalScoreQuery = $conn->query("SELECT SUM(score) AS total_score FROM test_answers WHERE test_id=$test_id AND matric_number='$matric_number'");
    $total_score = $totalScoreQuery->fetch_assoc()['total_score'] ?? 0;

    $conn->query("UPDATE test_submissions SET total_score=$total_score WHERE test_id=$test_id AND matric_number='$matric_number'");
    $conn->query("UPDATE results SET score=$total_score WHERE test_id=$test_id AND matric_number='$matric_number'");

    echo "<script>alert('Marks updated successfully!'); window.location.href='view_answers.php?test_id=$test_id';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Answers - <?php echo htmlspecialchars($test['course_title']); ?></title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>Mark Answers for: <?php echo htmlspecialchars($matric_number); ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php while ($row = $answers->fetch_assoc()): ?>
                    <div class="mb-4 border rounded p-3">
                        <h6><b>Q:</b> <?php echo htmlspecialchars($row['question_text']); ?></h6>
                        <p><b>Your Answer:</b> <?php echo nl2br(htmlspecialchars($row['student_answer'])); ?></p>

                        <?php if ($row['question_type'] === 'objective'): ?>
                            <p><b>Correct Answer:</b> <?php echo htmlspecialchars($row['correct_answers']); ?></p>
                            <p><b>Score:</b> <?php echo $row['score']; ?> / <?php echo $row['marks']; ?></p>
                        <?php else: ?>
                            <label><b>Assign Marks (out of <?php echo $row['marks']; ?>):</b></label>
                            <input type="number" name="scores[<?php echo $row['id']; ?>]" class="form-control" min="0" max="<?php echo $row['marks']; ?>" step="0.5" required>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="btn btn-primary w-100">Save Marks</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

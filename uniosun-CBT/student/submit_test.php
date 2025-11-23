<?php
session_start();
include '../includes/connect.php';
include '../includes/page_history.php';


if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']); // remove last page from history
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$test_id = $_POST['test_id'] ?? null;
$matric_number = $_POST['matric_number'] ?? null;
$answers = $_POST['answers'] ?? [];
$time_remaining = $_POST['time_remaining'] ?? null;
$test_duration = $_POST['test_duration'] ?? null;
$start_time = $_POST['start_time'] ?? null;

if (!$test_id || !$matric_number) {
    die("Missing test ID or matric number.");
}

// Fetch test details
$test = $conn->query("SELECT * FROM tests WHERE id=$test_id AND is_active=1")->fetch_assoc();
if (!$test) die("Invalid or inactive test selected.");

// Calculate duration used
if ($test_duration && $time_remaining !== null) {
    $duration_used = round($test_duration - ($time_remaining / 60), 2); // minutes
} elseif ($start_time) {
    $duration_used = round((time() - strtotime($start_time)) / 60, 2);
} else {
    $duration_used = 0;
}

// Fetch all questions into an array
$questions_result = $conn->query("SELECT * FROM questions WHERE test_id=$test_id AND is_active=1");
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}

if (empty($questions)) {
    die("No questions found for this test.");
}

$total_questions = count($questions);

// Insert a new submission record
$stmt = $conn->prepare("INSERT INTO test_submissions (test_id, matric_number, date_taken, duration_used, total_score, total_marks, status, submitted_via)
                        VALUES (?, ?, NOW(), ?, 0, 0, 'completed', 'web')");
$stmt->bind_param("isd", $test_id, $matric_number, $duration_used);
$stmt->execute();
$submission_id = $stmt->insert_id;

// Initialize counters
$total_score = 0;
$total_marks = 0;
$correct_count = 0;

// Loop through questions
foreach ($questions as $q) {
    $qid = $q['id'];
    $marks = (float)$q['marks'];
    $total_marks += $marks;
    $score = 0;
    $status = 'unmarked';

    if (isset($answers[$qid])) {
        $student_answer = $answers[$qid];
        if (is_array($student_answer)) $student_answer = implode(',', $student_answer);
        $student_answer = strtoupper(trim($student_answer));

        if (strtolower($q['question_type']) === 'objective') {
            $correct = strtoupper(trim($q['correct_answers']));
            $corrects = explode(',', $correct);
            $students = explode(',', $student_answer);
            sort($corrects);
            sort($students);

            if ($corrects == $students) {
                $score = $marks;
                $correct_count++;
            }
            $status = 'marked';
            $total_score += $score;
        } else {
            // Theory questions pending lecturer grading
            $status = 'pending';
        }

        // Insert student answer
        $stmt2 = $conn->prepare("INSERT INTO test_answers 
            (submission_id, test_id, question_id, matric_number, student_answer, marks, score, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiissdds", $submission_id, $test_id, $qid, $matric_number, $student_answer, $marks, $score, $status);
        $stmt2->execute();
    }
}

// Insert results
$stmt3 = $conn->prepare("INSERT INTO results (test_id, matric_number, score, total_questions, correct_answers, date_taken, duration_used, status, submitted_via) 
    VALUES (?, ?, ?, ?, ?, NOW(), ?, 'completed', 'web')");
$stmt3->bind_param("isdiid", $test_id, $matric_number, $total_score, $total_questions, $correct_count, $duration_used);
$stmt3->execute();

// Update test_submissions with final score and total marks
$stmt4 = $conn->prepare("UPDATE test_submissions SET total_score=?, total_marks=? WHERE id=?");
$stmt4->bind_param("ddi", $total_score, $total_marks, $submission_id);
$stmt4->execute();

// Redirect to student view page
echo "<script>
alert('Test submitted successfully! Your score will appear if allowed by the lecturer.');
window.location.href='../student/view.php?test_id={$test_id}&submitted=1';
</script>";
?>
<html>
  <body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script>
history.pushState(null, null, location.href); // prevent default back
window.onpopstate = function () {
    window.location.href = "<?php echo $prev_page; ?>";
};
</script>


  </body>    
</html>
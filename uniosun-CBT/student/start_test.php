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
$matric_number = $_SESSION['matric_number'] ?? null;

if (!$matric_number) {
    header("Location: ../login.php");
    exit();
}

/* ✅ Check if student has already taken or started the test */
$check_sql = "SELECT id, date_taken, duration_used, submitted_via, status 
              FROM test_submissions 
              WHERE test_id = ? AND matric_number = ?
              ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("is", $test_id, $matric_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // If test is completed, block retake
    if ($row['status'] === 'completed') {
        echo "
        <div style='max-width:600px; margin:100px auto; text-align:center; background:#fff; padding:40px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
            <h3>You have already taken this test.</h3>
            <p><strong>Date Taken:</strong> {$row['date_taken']}</p>
            <p><strong>Duration Used:</strong> {$row['duration_used']} minutes</p>
            <p><strong>Submitted Via:</strong> {$row['submitted_via']}</p>
            <a href='../student/dashboard.php' class='btn btn-primary mt-3'>Return to Dashboard</a>
        </div>";
        exit();
    }
    // If test is in progress, allow resume
    elseif ($row['status'] === 'in_progress') {
        $_SESSION['resume_submission_id'] = $row['id'];
        $_SESSION['resume_test_id'] = $test_id;
    }
} else {
    // Create a new submission record for this attempt
    $new_sql = "INSERT INTO test_submissions (test_id, matric_number, date_taken, duration_used, total_score, total_marks, status, submitted_via)
                VALUES (?, ?, NOW(), 0, 0, 0, 'in_progress', 'web')";
    $stmt2 = $conn->prepare($new_sql);
    $stmt2->bind_param("is", $test_id, $matric_number);
    $stmt2->execute();
    $_SESSION['resume_submission_id'] = $stmt2->insert_id;
    $_SESSION['resume_test_id'] = $test_id;
}

/* ✅ Fetch test details */
$test = $conn->query("SELECT * FROM tests WHERE id=$test_id AND is_active=1")->fetch_assoc();
if (!$test) {
    die("Invalid or inactive test selected.");
}
// ✅ Check if test has reached its max allowed students
$max_students = (int)$test['max_students']; // assuming you have a 'max_students' column in `tests`
if ($max_students > 0) {
    $taken_count = $conn->query("SELECT COUNT(*) AS total FROM test_submissions WHERE test_id=$test_id AND status='completed'")->fetch_assoc()['total'];

    if ($taken_count >= $max_students) {
        echo "
        <div style='max-width:600px; margin:100px auto; text-align:center; background:#fff; padding:40px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
            <h3>This test has reached the maximum number of students allowed.</h3>
            <p><strong>Limit:</strong> {$max_students} students</p>
            <p><strong>Currently Taken:</strong> {$taken_count}</p>
            <a href='../student/dashboard.php' class='btn btn-primary mt-3'>Return to Dashboard</a>
        </div>";
        exit();
    }
}


/* ✅ Fetch all questions */
$questions_result = $conn->query("SELECT * FROM questions WHERE test_id=$test_id AND is_active=1 ORDER BY id ASC");
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}

if (empty($questions)) {
    die("No questions found for this test.");
}

$total_questions = count($questions);
$duration = (int)$test['duration'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($test['course_title']); ?> - Start Test</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    overflow-x: hidden;
}

.container {
    width: 95%;
    max-width: 900px;
    z-index: 1;
}

.card {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.08);
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,0,0,0.6);
    color: #fff;
    padding: 30px;
    border: 1px solid rgba(255,255,255,0.2);
}

.card-header {
    background: rgba(0,0,0,0.3);
    border-radius: 15px 15px 0 0;
    padding: 15px 20px;
    font-weight: bold;
    font-size: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.timer {
    font-size: 1.2rem;
    font-weight: bold;
    color: #ff5252;
}

.question-card {
    display: none;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.question-card.active {
    display: block;
}

.form-check-label {
    color: #eee;
}

textarea.form-control {
    background: rgba(0,0,0,0.3);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px;
    resize: vertical;
}

.btn {
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
}
.btn-primary:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #0056b3, #520dc2);
}

.btn-secondary {
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.btn-secondary:hover {
    background: rgba(255,255,255,0.25);
}

.btn-success {
    background: linear-gradient(135deg, #00c851, #007e33);
}
.btn-success:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #007e33, #00c851);
}

@media (max-width: 768px) {
    .card {
        padding: 20px;
    }
    .btn {
        width: 100%;
        margin-top: 10px;
    }
    .card-header {
        flex-direction: column;
        align-items: start !important;
        text-align: left;
        gap: 10px;
    }
}
</style>
</head>

<body>
<div class="container">
  <div class="card">
    <div class="card-header">
      <span>Test: <?php echo htmlspecialchars($test['course_title']); ?></span>
      <span class="timer" id="timer"></span>
    </div>

    <div class="card-body">
      <form id="testForm" method="POST" action="submit_test.php">
        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
        <input type="hidden" name="matric_number" value="<?php echo htmlspecialchars($matric_number); ?>">

        <?php 
        $qno = 1;
        foreach ($questions as $q): 
        ?>
          <div class="question-card" id="question-<?php echo $qno; ?>">
            <h6><b>Q<?php echo $qno; ?>.</b> <?php echo htmlspecialchars($q['question_text']); ?></h6>

            <?php if (strtolower($q['question_type']) === 'objective'): ?>
              <div class="ms-3 mt-2">
                <?php
                  $is_multiple = (strpos($q['correct_answers'], ',') !== false);
                  $options = [
                    'A' => $q['option_a'], 
                    'B' => $q['option_b'], 
                    'C' => $q['option_c'], 
                    'D' => $q['option_d']
                  ];

                  foreach ($options as $key => $val):
                    if (trim($val) == '') continue;
                ?>
                  <div class="form-check">
                    <input class="form-check-input" 
                           type="<?php echo $is_multiple ? 'checkbox' : 'radio'; ?>" 
                           name="answers[<?php echo $q['id']; ?>]<?php echo $is_multiple ? '[]' : ''; ?>" 
                           value="<?php echo $key; ?>" 
                           id="q<?php echo $q['id'] . $key; ?>">
                    <label class="form-check-label" for="q<?php echo $q['id'] . $key; ?>">
                      <?php echo "$key. " . htmlspecialchars($val); ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>

            <?php else: ?>
              <div class="mt-3">
                <textarea class="form-control" 
                          name="answers[<?php echo $q['id']; ?>]" 
                          rows="3" 
                          placeholder="Type your answer here..."></textarea>
              </div>
            <?php endif; ?>
          </div>
        <?php 
        $qno++;
        endforeach; 
        ?>

        <div class="d-flex justify-content-between mt-4 flex-wrap">
          <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-3" id="submitBtn" style="display:none;">Submit Test</button>
        <input type="hidden" name="start_time" value="<?php echo date('Y-m-d H:i:s'); ?>">
        <input type="hidden" name="test_duration" value="<?php echo $duration; ?>">
        <input type="hidden" name="time_remaining" id="time_remaining" value="0">
      </form>
    </div>
  </div>
</div>

<script>
let currentPage = 1;
const totalQuestions = <?php echo $total_questions; ?>;
const questionsPerPage = 5;
const totalPages = Math.ceil(totalQuestions / questionsPerPage);

function showPage(page) {
    $('.question-card').hide();
    const start = (page - 1) * questionsPerPage + 1;
    const end = start + questionsPerPage - 1;

    for (let i = start; i <= end; i++) {
        $('#question-' + i).show();
    }

    $('#prevBtn').toggle(page > 1);
    $('#nextBtn').toggle(page < totalPages);
    $('#submitBtn').toggle(page === totalPages);
}
$('#prevBtn').click(() => { if (currentPage > 1) { currentPage--; showPage(currentPage); } });
$('#nextBtn').click(() => { if (currentPage < totalPages) { currentPage++; showPage(currentPage); } });
showPage(currentPage);

let timeLeft = <?php echo $duration * 60; ?>;
const timerElement = document.getElementById('timer');
const testForm = document.getElementById('testForm');
function updateTimer() {
    if (timeLeft <= 0) { clearInterval(timerInterval); $("#time_remaining").val(0); testForm.submit(); return; }
    let m = Math.floor(timeLeft / 60), s = timeLeft % 60;
    timerElement.textContent = `${m}:${s < 10 ? '0' : ''}${s}`;
    $("#time_remaining").val(timeLeft); timeLeft--;
}
const timerInterval = setInterval(updateTimer, 1000);

function autoSubmitOnCheat() {
    clearInterval(timerInterval);
    $("#time_remaining").val(timeLeft);
    testForm.submit();
}
document.addEventListener("visibilitychange", () => { if (document.hidden) autoSubmitOnCheat(); });
window.addEventListener("blur", autoSubmitOnCheat);
history.pushState(null, null, location.href);
window.onpopstate = function () { window.location.href = "<?php echo $prev_page; ?>"; };
</script>
</body>
</html>

<?php
session_start();
include '../includes/connect.php';
include '../includes/page_history.php';

if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']);
}

if (!isset($_GET['test_id'])) {
    die("No test selected.");
}

$test_id = intval($_GET['test_id']);

// Handle new question submission
if (isset($_POST['add_question'])) {
    $question_text = trim($_POST['question_text']);
    $question_type = $_POST['question_type'];
    $option_a = $_POST['option_a'] ?? '';
    $option_b = $_POST['option_b'] ?? '';
    $option_c = $_POST['option_c'] ?? '';
    $option_d = $_POST['option_d'] ?? '';
    $marks = $_POST['marks'] ?? 0;

    if ($question_type === 'objective') {
        $correct_answers = isset($_POST['correct']) ? implode(',', $_POST['correct']) : '';
    } else {
        $correct_answers = $_POST['correct_answers'] ?? '';
    }

    $result = $conn->query("SELECT MAX(question_number) AS max_no FROM questions WHERE test_id = $test_id");
    $row = $result->fetch_assoc();
    $next_question_no = ($row['max_no'] ?? 0) + 1;

    $stmt = $conn->prepare("INSERT INTO questions (test_id, question_number, question_text, question_type, option_a, option_b, option_c, option_d, correct_answers, marks) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssssssi", $test_id, $next_question_no, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answers, $marks);

    if ($stmt->execute()) {
        $msg = "✅ Question $next_question_no added successfully!";
    } else {
        $msg = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch test info
$test = $conn->query("SELECT * FROM tests WHERE id=$test_id")->fetch_assoc();

// Fetch questions
$questions = $conn->query("SELECT * FROM questions WHERE test_id=$test_id ORDER BY question_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Questions - <?php echo htmlspecialchars($test['course_title']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    margin:0;
    font-family: 'Poppins', sans-serif;
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    min-height: 100vh;
    color:#fff;
}

body::before {
    content:"";
    position: fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    z-index:0;
}

.container {
    position: relative;
    z-index:1;
    max-width:900px;
    margin:80px auto 50px;
    padding:0 10px;
}

.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.4);
    border:1px solid rgba(255,255,255,0.2);
    margin-bottom:30px;
    color:#fff;
    animation: fadeIn 0.8s ease-in-out;
}

.card-header {
    font-weight:bold;
    font-size:1.2rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 20px;
    background: linear-gradient(90deg,#00e676,#007e33);
    border-radius:15px 15px 0 0;
}

.form-control, .form-select {
    border-radius:10px;
    border:none;
    background: rgba(255,255,255,0.1);
    color:#fff;
    padding:10px;
}

.form-control::placeholder {
    color:#ddd;
}

.btn-success {
    background: linear-gradient(135deg,#00c853,#007e33);
    font-weight:bold;
    transition:all 0.3s ease;
}

.btn-success:hover {
    transform:scale(1.05);
    background: linear-gradient(135deg,#007e33,#00c853);
}

/* ✅ TABLE FIX */
.table {
    background: #000; /* full black background */
    color: #fff;      /* white text */
    border-radius: 15px;
    overflow: hidden;
}

.table th {
    background: #111; /* slightly lighter black for header */
    color: #fff;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(255,255,255,0.05);
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: rgba(255,255,255,0.1);
}

.table td, .table th {
    vertical-align: middle;
    border-color: rgba(255,255,255,0.2);
}

/* end table fix */

@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}

/* Responsive */
@media (max-width:768px) {
    .row > .col-md-6, .row > .col-md-12, .row > .col-md-4 {
        flex:0 0 100%;
        max-width:100%;
        margin-bottom:15px;
    }

    .card-header h4 {
        font-size:1rem;
    }
}
</style>


<script>
$(document).ready(function(){
    $("#question_type").on("change", function(){
        if ($(this).val() === "objective") {
            $(".objective-options").show();
        } else {
            $(".objective-options").hide();
        }
    });
});
</script>
</head>
<body>
<div class="container">
  <div class="card shadow-lg">
    <div class="card-header">
      <h4 class="mb-0">Add Questions for <?php echo htmlspecialchars($test['course_code']); ?></h4>
      <a href="manage_tests.php" class="btn btn-light btn-sm">← Back to Tests</a>
    </div>
    <div class="card-body">
      <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Question Type</label>
          <select name="question_type" id="question_type" class="form-select" required>
            <option value="">-- Select Type --</option>
            <option value="objective">Objective</option>
            <option value="theory">Theory</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Question Text</label>
          <textarea name="question_text" class="form-control" rows="3" placeholder="Enter your question here..." required></textarea>
        </div>

        <div class="objective-options" style="display:none;">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Option A</label>
              <input type="text" name="option_a" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option B</label>
              <input type="text" name="option_b" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option C</label>
              <input type="text" name="option_c" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option D</label>
              <input type="text" name="option_d" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label>Correct Answer(s)</label>
              <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="correct[]" value="A" id="ca">
                  <label class="form-check-label" for="ca">A</label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="correct[]" value="B" id="cb">
                  <label class="form-check-label" for="cb">B</label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="correct[]" value="C" id="cc">
                  <label class="form-check-label" for="cc">C</label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="correct[]" value="D" id="cd">
                  <label class="form-check-label" for="cd">D</label>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label>Mark</label>
          <input type="number" name="marks" class="form-control" min="1" placeholder="Enter marks" required>
        </div>

        <button type="submit" name="add_question" class="btn btn-success w-100">+ Add Question</button>
      </form>
    </div>
  </div>

  <div class="card shadow-lg">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">Questions for this Test</h5>
    </div>
    <div class="card-body">
      <?php if ($questions->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead class="table-dark text-dark">
              <tr>
                <th>#</th>
                <th>Question</th>
                <th>Type</th>
                <th>Mark</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($q = $questions->fetch_assoc()): ?>
              <tr>
                <td><?php echo $q['question_number']; ?></td>
                <td><?php echo htmlspecialchars(substr($q['question_text'],0,80)); ?>...</td>
                <td><?php echo ucfirst($q['question_type']); ?></td>
                <td><?php echo $q['marks']; ?></td>
                <td>
                  <a href="edith_question.php?id=<?php echo $q['id']; ?>&test_id=<?php echo $test_id; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_question.php?id=<?php echo $q['id']; ?>&test_id=<?php echo $test_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this question?');">Delete</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center">No questions added yet.</p>
      <?php endif; ?>
    </div>
    <div class="text-center mt-4">
      <form action="manage_tests.php" method="get">
        <input type="hidden" name="redirected" value="1">
        <button type="submit" class="btn btn-success btn-lg">✅ Submit Test & Go to Manage Tests</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
history.pushState(null, null, location.href);
window.onpopstate = function() {
    window.location.href = "<?php echo $prev_page; ?>";
};
</script>
</body>
</html>

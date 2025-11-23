<?php
include '../includes/connect.php';

if (!isset($_GET['id']) || !isset($_GET['test_id'])) {
  die("Invalid access.");
}

$question_id = intval($_GET['id']);
$test_id = intval($_GET['test_id']);

// Fetch question info
$q = $conn->query("SELECT * FROM questions WHERE id=$question_id");
if ($q->num_rows == 0) {
  die("Question not found.");
}
$question = $q->fetch_assoc();

// Handle form update
if (isset($_POST['update_question'])) {
  $question_text = $_POST['question_text'];
  $question_type = $_POST['question_type'];
  $option_a = $_POST['option_a'] ?? '';
  $option_b = $_POST['option_b'] ?? '';
  $option_c = $_POST['option_c'] ?? '';
  $option_d = $_POST['option_d'] ?? '';
  $marks = $_POST['marks'] ?? 0;

  // Handle multiple correct answers
  if ($question_type === 'objective') {
    $correct_answers = isset($_POST['correct']) ? implode(',', $_POST['correct']) : '';
  } else {
    $correct_answers = $_POST['correct_answers'] ?? '';
  }

  $stmt = $conn->prepare("UPDATE questions SET question_text=?, question_type=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answers=?, marks=? WHERE id=?");
  $stmt->bind_param("sssssssii", $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answers, $marks, $question_id);

  if ($stmt->execute()) {
    header("Location: add_questions.php?test_id=" . $test_id . "&msg=Question updated successfully");
    exit;
  } else {
    $msg = "Error: " . $stmt->error;
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Question</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(document).ready(function() {
    // Toggle fields
    $("#question_type").on("change", function() {
      if ($(this).val() === "objective") {
        $(".objective-options").show();
      } else {
        $(".objective-options").hide();
      }
    }).trigger("change"); // Trigger on load
  });
  </script>
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Edit Question</h4>
      <a href="add_questions.php?test_id=<?php echo $test_id; ?>" class="btn btn-light btn-sm">← Back</a>
    </div>

    <div class="card-body">
      <?php if (isset($msg)): ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Question Type</label>
          <select name="question_type" id="question_type" class="form-select" required>
            <option value="objective" <?php if($question['question_type'] == 'objective') echo 'selected'; ?>>Objective</option>
            <option value="theory" <?php if($question['question_type'] == 'theory') echo 'selected'; ?>>Theory</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Question Text</label>
          <textarea name="question_text" class="form-control" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
        </div>

        <div class="objective-options" style="display:none;">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Option A</label>
              <input type="text" name="option_a" class="form-control" value="<?php echo htmlspecialchars($question['option_a']); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option B</label>
              <input type="text" name="option_b" class="form-control" value="<?php echo htmlspecialchars($question['option_b']); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option C</label>
              <input type="text" name="option_c" class="form-control" value="<?php echo htmlspecialchars($question['option_c']); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label>Option D</label>
              <input type="text" name="option_d" class="form-control" value="<?php echo htmlspecialchars($question['option_d']); ?>">
            </div>

            <?php
              $corrects = explode(',', $question['correct_answers']);
            ?>
            <div class="col-md-6 mb-3">
              <label class="form-label">Correct Answer(s)</label><br>
              <?php foreach (['A','B','C','D'] as $opt): ?>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="correct[]" value="<?php echo $opt; ?>" 
                    <?php if(in_array($opt, $corrects)) echo 'checked'; ?>>
                  <label class="form-check-label"><?php echo $opt; ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label>Mark</label>
          <input type="number" name="marks" class="form-control" min="1" value="<?php echo $question['marks']; ?>" required>
        </div>

        <button type="submit" name="update_question" class="btn btn-success w-100">💾 Update Question</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>

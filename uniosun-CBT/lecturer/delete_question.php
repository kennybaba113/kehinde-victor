<?php
include '../includes/connect.php';

if (!isset($_GET['id']) || !isset($_GET['test_id'])) {
    die("Invalid request.");
}

$question_id = intval($_GET['id']);
$test_id = intval($_GET['test_id']);

// Check if question exists
$q = $conn->query("SELECT * FROM questions WHERE id=$question_id LIMIT 1");
if ($q->num_rows === 0) {
    die("Question not found.");
}

// Delete the question
$delete = $conn->query("DELETE FROM questions WHERE id=$question_id");

if ($delete) {
    // Redirect back to add_questions.php after success
    header("Location: add_questions.php?test_id=" . $test_id . "&msg=Question+deleted+successfully");
    exit;
} else {
    echo "Error deleting question: " . $conn->error;
}
?>

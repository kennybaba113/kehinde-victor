<?php
include '../includes/connect.php';
session_start();

if (!isset($_SESSION['lecturer_id'])) {
    die("Unauthorized access. Please log in as a lecturer.");
}

$lecturer_id = $_SESSION['lecturer_id'];

if (!isset($_GET['test_id'])) {
    die("No test selected.");
}

$test_id = intval($_GET['test_id']);

// Verify the lecturer owns this test
$stmt = $conn->prepare("SELECT * FROM tests WHERE id = ? AND lecturer_id = ?");
$stmt->bind_param("ii", $test_id, $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You are not authorized to delete this test or it doesn't exist.");
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete results first
    $stmt1 = $conn->prepare("DELETE FROM results WHERE test_id = ?");
    $stmt1->bind_param("i", $test_id);
    $stmt1->execute();
    $stmt1->close();

    // Delete test answers
    $stmt2 = $conn->prepare("DELETE FROM test_answers WHERE test_id = ?");
    $stmt2->bind_param("i", $test_id);
    $stmt2->execute();
    $stmt2->close();

    // Delete test submissions
    $stmt3 = $conn->prepare("DELETE FROM test_submissions WHERE test_id = ?");
    $stmt3->bind_param("i", $test_id);
    $stmt3->execute();
    $stmt3->close();

    // Delete questions
    $stmt4 = $conn->prepare("DELETE FROM questions WHERE test_id = ?");
    $stmt4->bind_param("i", $test_id);
    $stmt4->execute();
    $stmt4->close();

    // Finally, delete the test
    $stmt5 = $conn->prepare("DELETE FROM tests WHERE id = ?");
    $stmt5->bind_param("i", $test_id);
    $stmt5->execute();
    $stmt5->close();

    $conn->commit();

    echo "<script>alert('Test and all related records deleted successfully!'); window.location.href='manage_tests.php';</script>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<script>alert('Error deleting test: {$e->getMessage()}'); window.location.href='manage_tests.php';</script>";
}

$conn->close();
?>

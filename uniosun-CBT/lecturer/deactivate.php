<?php
include '../includes/connect.php';

if (isset($_GET['test_id'])) {
    $test_id = intval($_GET['test_id']);
    $sql = "UPDATE tests SET is_active = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $test_id);

    if ($stmt->execute()) {
        header("Location: manage_tests.php?msg=Test deactivated successfully!");
        exit;
    } else {
        echo "Error deactivating test: " . $stmt->error;
    }
} else {
    header("Location: manage_tests.php?msg=Invalid request!");
    exit;
}

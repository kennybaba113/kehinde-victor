<?php
include '../includes/connect.php';

// Admin info
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123';

// Hash password correctly
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert admin safely
$sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>

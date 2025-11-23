<?php
include '../includes/connect.php';
session_start();

// PHPMailer
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM students WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Generate unique reset token
        $token = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        // Save token and expiry
        mysqli_query($conn, "UPDATE students SET reset_token='$token', token_expiry='$expiry' WHERE email='$email'");

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_email@gmail.com';          // your Gmail
            $mail->Password   = 'your_app_password';            // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('your_email@gmail.com', 'UNIOSUN CBT');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Your password reset code is: <b>$token</b>. It expires in 10 minutes.";

            $mail->send();
            $message = "Check your email for the reset code.";
        } catch (Exception $e) {
            // For local testing (XAMPP)
            $message = "Mailer error: {$mail->ErrorInfo}. For testing, your code is: <b>$token</b>";
        }
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<style>
body { font-family: Arial; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); width:100%; max-width:400px; }
h2{text-align:center; margin-bottom:20px; color:#333;}
input{width:100%; padding:12px; margin:10px 0 20px; border:1px solid #ccc; border-radius:8px;}
button{width:100%; padding:12px; border:none; border-radius:8px; background:#007bff; color:#fff; font-size:16px; cursor:pointer;}
button:hover{background:#0056b3;}
.message{text-align:center; margin-bottom:15px; color:green;}
@media(max-width:480px){.container{padding:20px;}}
</style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" name="submit">Send Code</button>
    </form>
</div>
</body>
</html>

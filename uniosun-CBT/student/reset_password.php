<?php
include '../includes/connect.php';
session_start();
$message = '';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Verify token and expiry
        $query = "SELECT * FROM students WHERE email='$email' AND reset_token='$token' AND token_expiry >= NOW()";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            mysqli_query($conn, "UPDATE students SET password='$password_hashed', reset_token=NULL, token_expiry=NULL WHERE email='$email'");
            $message = "Password reset successful. <a href='login.php'>Login here</a>";
        } else {
            $message = "Invalid token or expired.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<style>
body { font-family: Arial; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); width:100%; max-width:400px; }
h2{text-align:center; margin-bottom:20px; color:#333;}
.input-container{position:relative; margin-bottom:20px;}
input{width:100%; padding:12px; border:1px solid #ccc; border-radius:8px;}
button{width:100%; padding:12px; border:none; border-radius:8px; background:#28a745; color:#fff; font-size:16px; cursor:pointer;}
button:hover{background:#1e7e34;}
.message{text-align:center; margin-bottom:15px; color:red;}
.toggle-password{position:absolute; right:15px; top:12px; cursor:pointer; user-select:none;}
a{color:#007bff; text-decoration:none;}
a:hover{text-decoration:underline;}
.countdown{font-size:14px; color:#555; text-align:right; margin-top:-15px; margin-bottom:15px;}
@media(max-width:480px){.container{padding:20px;}}
</style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="text" name="token" placeholder="Enter reset code" required>
        
        <div class="input-container">
            <input type="password" name="password" placeholder="New password" id="password" required>
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
        </div>

        <div class="input-container">
            <input type="password" name="confirm_password" placeholder="Confirm password" id="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
        </div>

        <div class="countdown" id="countdown">Code expires in 10:00</div>

        <button type="submit" name="submit">Reset Password</button>
    </form>
</div>

<script>
// Toggle password visibility
function togglePassword(id){
    var input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

// Countdown timer
var countdown = 600; // 10 minutes
var countdownEl = document.getElementById('countdown');
var timer = setInterval(function(){
    var minutes = Math.floor(countdown/60);
    var seconds = countdown % 60;
    countdownEl.innerHTML = "Code expires in "+minutes+":"+(seconds<10?"0"+seconds:seconds);
    countdown--;
    if(countdown < 0){
        clearInterval(timer);
        countdownEl.innerHTML = "Code expired.";
    }
},1000);
</script>
</body>
</html>

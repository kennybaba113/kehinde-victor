<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['matric_number'])) { 
    header("Location: login.php"); 
    exit(); 
}
$matric = $_SESSION['matric_number'];
$message = '';

if (isset($_POST['submit'])) {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        $sql = "SELECT password FROM students WHERE matric_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $matric);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res || !password_verify($old, $res['password'])) {
            $message = "Old password is incorrect.";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $u = "UPDATE students SET password = ? WHERE matric_number = ?";
            $s2 = $conn->prepare($u);
            $s2->bind_param("ss", $hash, $matric);
            if ($s2->execute()) {
                $message = "Password changed successfully.";
            } else {
                $message = "Failed to update password. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Change Password | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{
    --glass-bg: rgba(255,255,255,0.08);
    --glass-border: rgba(255,255,255,0.12);
    --accent: #28a745;
}
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    background-attachment: fixed;
    color: #fff;
}
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    z-index: 0;
}
.container {
    position: relative;
    z-index: 2;
    max-width: 500px;
    margin: 100px auto;
    padding: 20px;
}
.card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(12px);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}
h4 { font-weight: 700; margin-bottom: 10px; }
p { color: #ddd; margin-bottom: 18px; }

.input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.05);
    color: #fff;
    margin-bottom: 12px;
}
.input::placeholder { color: #ccc; }

.btn {
    font-weight: 600;
    padding: 8px 18px;
    border-radius: 8px;
}
.btn-primary { 
    background: var(--accent);
    border: none;
    color: #fff;
}
.btn-primary:hover { 
    background: #218838;
}
.btn-outline-secondary { 
    border: 1px solid var(--accent);
    color: var(--accent);
    background: transparent;
}
.btn-outline-secondary:hover { 
    background: var(--accent);
    color: #fff;
}

.message {
    margin-bottom: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
}
.message.success { color: #28a745; }
.message.error { color: #ff4c4c; }

@media(max-width:768px){
    .container { margin: 80px 12px; padding:15px; }
}
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <h4>Change Password</h4>
    <p>Enter your current password and choose a new one.</p>

    <?php if ($message): ?>
      <div class="message <?php echo strpos($message,'success')!==false?'success':'error'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <input type="password" name="old_password" class="input" placeholder="Current password" required>
      <input type="password" name="new_password" class="input" placeholder="New password" required>
      <input type="password" name="confirm_password" class="input" placeholder="Confirm new password" required>
      
      <div style="display:flex;gap:10px; flex-wrap:wrap; margin-top:10px;">
        <button class="btn btn-primary" name="submit" type="submit">Change Password</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back To Dashboard</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>

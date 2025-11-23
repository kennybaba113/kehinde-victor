<?php
session_start();
include('../includes/connect.php');
include '../includes/page_history.php';

 if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']); // remove last page from history
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = trim($_POST['staff_id']);
    $password = trim($_POST['password']);

    $sql = $conn->prepare("SELECT * FROM lecturers WHERE staff_id = ?");
    $sql->bind_param("s", $staff_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $lecturer = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $lecturer['password'])) {
            $_SESSION['lecturer_id'] = $lecturer['id'];
            $_SESSION['lecturer_name'] = $lecturer['full_name'];

            // ✅ Redirect directly to dashboard
            header("Location:dashboard.php");
            exit();
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "Lecturer not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lecturer Login | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: url('../assets/img/login_bg.jpg') no-repeat center center/cover;
    background-attachment: fixed;
    color: #fff;
}

body::before {
    content: "";
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(10px);
    z-index: 0;
}

.login-container {
    position: relative;
    z-index: 1;
    max-width: 400px;
    margin: 50px auto;
    padding: 30px 25px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
}

h1, h3 {
    text-align: center;
    color: #00e676;
    font-weight: 700;
}

h3 { margin-bottom: 25px; }

.form-label { color: #e0e0e0; }

.form-control {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    transition: 0.3s;
}

.form-control:focus {
    background: rgba(255,255,255,0.2);
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    color: #fff;
}

.btn-login {
    background: linear-gradient(135deg, #00c853, #007e33);
    color: #fff;
    font-weight: bold;
    border-radius: 10px;
    width: 100%;
    padding: 12px;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-login:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg, #007e33, #00c853);
}

.alert {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 15px;
}

/* Header styling */
.header {
    text-align: center;
    margin-top: 30px;
    color: #00e676;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .login-container {
        margin: 30px 15px;
        padding: 25px 15px;
    }
    h1 { font-size: 24px; }
    h3 { font-size: 20px; }
}

@media (max-width: 480px) {
    .login-container {
        margin: 20px 10px;
        padding: 20px 10px;
    }
    h1 { font-size: 20px; }
    h3 { font-size: 18px; }
}
</style>
</head>

<body>

<div class="header">
    <h1>UNIOSUN CBT Portal</h1>
    <p>Lecturer Login Access</p>
</div>

<div class="login-container shadow">
    <h3>Lecturer Login</h3>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="staff_id" class="form-label">Staff ID</label>
            <input type="text" name="staff_id" id="staff_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login">Login</button>
        
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
history.pushState(null, null, location.href); // prevent back
window.onpopstate = function () {
    window.location.href = "<?php echo $prev_page ?? '../'; ?>";
};
</script>

</body>
</html>

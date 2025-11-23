<?php
session_start();
include '../includes/connect.php';
include '../includes/page_history.php';

if (!empty($_SESSION['page_history'])) {
    array_pop($_SESSION['page_history']); // remove last page from history
}

// Prevent caching so back button cannot show dashboard
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If student already logged in, redirect to dashboard
if (isset($_SESSION['matric_number'])) {
    header("Location: dashboard.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matric = trim($_POST['matric_number']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM students WHERE matric_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['matric_number'] = $student['matric_number'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "❌ Incorrect password!";
        }
    } else {
        $message = "❌ Matric number not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login | UNIOSUN CBT</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/img/logo.png">
<style>
  body, html {
    margin: 0;
    padding: 0;
    height: 100%;
  }
  .background {
    background: url('../assets/img/logo.png') no-repeat center center/cover;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
  }
  .background::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 0;
  }
  .card {
    position: relative;
    z-index: 1;
    backdrop-filter: blur(15px);
    background: rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 30px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
    color: white;
    animation: fadeIn 1s ease-in-out;
  }
  @keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
  }
  .card h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #fff;
    font-weight: 600;
  }
  .form-label {
    font-weight: 500;
  }
  .btn-login {
    background-color: #0f9d58;
    color: white;
    border: none;
    transition: 0.3s;
    font-weight: 500;
  }
  .btn-login:hover {
    background-color: #0c7b46;
    transform: scale(1.03);
  }
  .alert {
    text-align: center;
    background-color: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
  }
  a {
    text-decoration: none;
  }
  @media (max-width: 576px) {
    .card {
      padding: 25px;
      margin: 0 10px;
      width: 95%;
    }
  }
</style>
</head>
<body>
  <section class="background">
    <div class="card">
      <h3>Student Login</h3>
      <?php if ($message): ?>
        <div class="alert alert-warning"><?php echo $message; ?></div>
      <?php endif; ?>
      <form method="POST" action="">
        <div class="mb-3">
          <label for="matric" class="form-label">Matric Number</label>
          <input type="text" name="matric_number" id="matric" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-login w-100">Login</button>
      </form>
      <p class="mt-3 text-center">
        Don’t have an account? <a href="register.php" class="text-light fw-bold">Register</a>
      </p>
      <p><a href="forgot_password.php">Forgot Password</a></p>

    </div>
  </section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Push a new state
  history.pushState(null, null, window.location.href);
  window.addEventListener('popstate', function () {
      // Go to open/home page when back button is clicked
      window.location.href = "../page.php"; // <-- Replace with your open page URL
  });
</script>
</body>
</html>

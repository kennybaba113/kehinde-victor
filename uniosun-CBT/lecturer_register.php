<?php
    error_reporting(E_ALL);
    ini_set('display errors' , 1);
    session_start();
    include('includes/connect.php'); 

            if (!$conn) {
                die("Database connection failed: " . mysqli_connect_error());
            }else{
                echo "database connected sucessfully";
            }

/**if (isset($_POST['login'])) {
    $matric_no = mysqli_real_escape_string($conn, $_POST['matric_no']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);

    
    $query = "SELECT * FROM students 
              WHERE matric_number='$matric_number' 
              AND full_name='$full_name'
              AND level='$level'
              AND department='$department'
              AND semester='$semester'
              LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['student'] = mysqli_fetch_assoc($result);
        header("Location: student_dashboard.php");
        exit();
    } else {
        $error = "Invalid login details. Please check and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Login | UNIOSUN CBT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet"> <!-- optional custom CSS -->
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-card {
      max-width: 450px;
      margin: 80px auto;
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .btn-primary {
      background-color: #e96e00;
      border: none;
    }
    .btn-primary:hover {
      background-color: #cf5f00;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h4 class="text-center text-primary mb-4">UNIOSUN CBT Student Login</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Matric Number</label>
        <input type="text" name="matric_number" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Level</label>
        <select name="level" class="form-select" required>
          <option value="">Select Level</option>
          <option value="100">100</option>
          <option value="200">200</option>
          <option value="300">300</option>
          <option value="400">400</option>
          <option value="500">500</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Department</label>
        <select name="department" class="form-select" required>
          <option value="">Select Department</option>
          <option value="Accounting">Accounting</option>
          <option value="Computer Science">Computer Science</option>
          <option value="Economics">Economics</option>
          <option value="Law">Law</option>
          <option value="Nursing Science">Nursing Science</option>
          <!-- Add more departments from your ENUM list -->
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select" required>
          <option value="">Select Semester</option>
          <option value="First">First</option>
          <option value="Second">Second</option>
        </select>
      </div>

      <div class="d-grid">
        <button type="submit" name="login" class="btn btn-primary">Login</button>
      </div>
    </form>
  </div>

</body>
</html>*//

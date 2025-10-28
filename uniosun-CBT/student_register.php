<?php
include 'includes/connect.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_number = mysqli_real_escape_string($conn, $_POST['matric_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if matric number already exists
    $check_query = "SELECT * FROM student_accounts WHERE matric_number='$matric_number'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "<div class='alert alert-warning text-center fade-in'> This Matric Number is already registered.</div>";
    } else {
        // Hash password and insert
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO student_accounts (matric_number, password) VALUES ('$matric_number', '$hashed_password')";
        if (mysqli_query($conn, $insert_query)) {
            $message = "<div class='alert alert-success text-center fade-in'> Registration successful!</div>";
            header("refresh:2;url=student_login.php");
        } else {
            $message = "<div class='alert alert-danger text-center fade-in'> Error: could not register. Try again later.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration | UNIOSUN CBT</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #43cea2, #185a9d);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .register-card {
            width: 400px;
            padding: 40px 35px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            color: #fff;
            animation: fadeSlide 1s ease;
        }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h3 {
            text-align: center;
            font-weight: 700;
            color: #f8f9fa;
            margin-bottom: 20px;
        }

        .form-control {
            background-color: rgba(255,255,255,0.2);
            border: none;
            border-radius: 10px;
            color: #fff;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255,255,255,0.4);
            box-shadow: 0 0 5px #0d6efd;
            color: #000;
        }

        ::placeholder {
            color: #e9ecef;
        }

        .btn-register {
            width: 100%;
            background: linear-gradient(90deg, #198754, #28a745);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-register:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .fade-in {
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        a {
            color: #0d6efd;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="register-card">
        <h3>📝 Student Registration</h3>
        <?= $message ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Matric Number</label>
                <input type="text" name="matric_number" class="form-control" placeholder="e.g. 2023/54122" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-register mt-2">Register</button>
        </form>
        <p class="text-center mt-3 text-dark">
            Already have an account? <a href="student_login.php">Login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include('includes/connect.php'); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_number = trim($_POST['matric_number']);
    $full_name = trim($_POST['full_name']);
    $gender = trim($_POST['gender']);
    $level = trim($_POST['level']);
    $department = trim($_POST['department']);
    $semester = trim($_POST['semester']);
    $password = trim($_POST['password']);

    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $check = $conn->prepare("SELECT * FROM students WHERE matric_number = ?");
    $check->bind_param("s", $matric_number);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "This matric number is already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (matric_number, full_name, gender, level, department, semester, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $matric_number, $full_name, $gender, $level, $department, $semester, $hashed_password);

        if ($stmt->execute()) {
            header("Location: student_login.php");
            exit;
        } else {
            $error = "Error: Unable to register student.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | UNIOSUN CBT</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <style>
        
        .background {
            background: url('assets/img/logo.png') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow:hidden;
            position:relative;  
        }
        .background::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); 
            z-index: 0;                     
            }

            
            .container {
            position: relative;
            z-index: 1;                     
            border-radius: 15px;
            padding: 40px;
            width: 90%;
            max-width: 420px;
            animation: fadeInUp 0.8s ease-in-out;
            
            }
        .card {
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            border-radius: 15px;
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .btn-primary {
            width: 100%;
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <section class="background">

      <div class="contanier">

      <div class="card p-3">
    <div class="card-header">
        <h4>Student Registration</h4>
    </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Matric Number</label>
                <input type="text" name="matric_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Level</label>
                <select name="level" class="form-select" required>
                    <option value="">Select Level</option>
                    <option>100L</option>
                    <option>200L</option>
                    <option>300L</option>
                    <option>400L</option>
                    <option>500L</option>
                    <option>600L</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Department</label>
                <input type="text" name="department" class="form-control" placeholder="e.g. Accounting" required>
            </div>
            <div class="mb-3">
                <label>Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="">Select Semester</option>
                    <option>First Semester</option>
                    <option>Second Semester</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>

            <p class="text-center mt-3">
                Already registered? <a href="student_login.php" class="text-primary">Login here</a>
            </p>
        </form>
    </div>
</div>


    </div>





    </section>

    

</body>
</html>

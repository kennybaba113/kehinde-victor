
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNIOSUN Online Test Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/logo.png">

    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url("assets/img/logo.png") no-repeat center center/cover;
            position: relative;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            width: 90%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h1 {
            color: #000;
            font-size: 24px;
            margin-bottom: 30px;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .student-btn {
            background-color: #007bff;
            color: white;
        }

        .student-btn:hover {
            background-color: #0056b3;
        }

        .lecturer-btn {
            background-color: #1e7e34;
            color: white;
        }

        .lecturer-btn:hover {
            background-color: #28a745;
        }

        .footer {
            margin-top: 25px;
            color: #555;
            font-size: 13px;
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            h1 {
                font-size: 20px;
                margin-bottom: 20px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }

            .container {
                padding: 30px 20px;
            }
        }

    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h1>UNIOSUN Online Test Portal</h1>

        <button class="student-btn" onclick="window.location.href='student/register.php'">
            <i class="bi bi-person-fill"></i> Student Portal
        </button>

        <button class="lecturer-btn" onclick="window.location.href='lecturer/login.php'">
            <i class="bi bi-mortarboard-fill"></i> Lecturer Portal
        </button>

        <div class="footer">
            <?php echo date("Y"); ?> Osun State University - Department Test System
        </div>
    </div>
</body>
</html>

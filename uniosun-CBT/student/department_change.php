<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['matric_number'])) { 
    header("Location: login.php"); 
    exit(); 
}

$matric = $_SESSION['matric_number'];
$message = "";

// Fetch current department of student
$stmt = $conn->prepare("SELECT department FROM students WHERE matric_number = ?");
$stmt->bind_param("s", $matric);
$stmt->execute();
$current_data = $stmt->get_result()->fetch_assoc();
$current_dept = $current_data['department'] ?? '';

// Check the most recent department change request
$req_stmt = $conn->prepare("SELECT * FROM department_change_requests WHERE matric_number=? ORDER BY date_requested DESC LIMIT 1");
$req_stmt->bind_param("s", $matric);
$req_stmt->execute();
$req_data = $req_stmt->get_result()->fetch_assoc();

$request_exists = $req_data ? true : false;
$request_status = $req_data['status'] ?? '';
$admin_comment = $req_data['admin_comment'] ?? '';

// Only allow new request if last request is rejected or no request exists
$can_submit = !$request_exists || $request_status === 'rejected';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_submit) {
    $new_dept = trim($_POST['new_department'] ?? '');

    if (empty($new_dept)) {
        $message = "Please choose a new department.";
    } elseif (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
        $message = "Please upload your proof document.";
    } else {
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        $file_type = $_FILES['proof']['type'];
        $file_tmp  = $_FILES['proof']['tmp_name'];
        $file_size = $_FILES['proof']['size'];
        $file_name = $_FILES['proof']['name'];

        if (!in_array($file_type, $allowed_types)) {
            $message = "Allowed file formats: PDF, JPG, PNG.";
        } elseif ($file_size > 5 * 1024 * 1024) {
            $message = "File too large. Max size is 5MB.";
        } else {
            // Upload folder directly under project root
            $uploadDir = __DIR__ . "/../uploadoo/";

            // Create folder if it doesn't exist
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                $message = "Failed to create upload directory. Check server permissions.";
            }

            // Check writable
            if (!isset($message) && !is_writable($uploadDir)) {
                $message = "Upload directory is not writable.";
            }

            if (!isset($message)) {
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $filename = $matric . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $relative_path = 'uploadoo/' . $filename; // relative path stored in DB

                    // Insert request into DB
                    $sql = "INSERT INTO department_change_requests 
                            (matric_number, old_department, new_department, document_path, status, date_reviewed, admin_comment, date_requested)
                            VALUES (?, ?, ?, ?, 'pending', NULL, NULL, NOW())";
                    $insert = $conn->prepare($sql);
                    $insert->bind_param("ssss", $matric, $current_dept, $new_dept, $relative_path);

                    if ($insert->execute()) {
                        $message = "Your department change request has been submitted successfully.";
                        $request_exists = true;
                        $request_status = 'pending';
                    } else {
                        $message = "Error saving request: " . $insert->error;
                        @unlink($destination); // delete file if DB insert fails
                    }
                } else {
                    $message = "Failed to move uploaded file. Check folder permissions.";
                }
            }
        }
    }
}

// Fetch departments excluding current
$departments = [];
$dept_query = $conn->query("SELECT department_name FROM department ORDER BY department_name ASC");
while ($row = $dept_query->fetch_assoc()) {
    if ($row['department_name'] !== $current_dept) {
        $departments[] = $row['department_name'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Department Change Request | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Your CSS remains unchanged */
</style>
</head>
<body>

<div class="container">
  <div class="card">

    <h4>Department Change Request</h4>
    <p class="note">Upload proof (transfer letter / admin form). Admin will review and approve.</p>

    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message,'submitted')!==false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($request_exists): ?>
        <div class="status <?php echo $request_status; ?>">
            Your department change request is <strong><?php echo ucfirst($request_status); ?></strong>.
            <?php if($admin_comment): ?>
                <br>Admin Comment: <?php echo htmlspecialchars($admin_comment); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($can_submit): ?>
        <form method="post" enctype="multipart/form-data">
          <label>Select New Department</label>
          <select name="new_department" required>
            <option value="">-- Choose Department --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?php echo htmlspecialchars($d); ?>">
                    <?php echo htmlspecialchars($d); ?>
                </option>
            <?php endforeach; ?>
          </select>

          <label>Upload Proof (PDF, JPG, PNG) — Max 5MB</label>
          <input type="file" name="proof" accept=".pdf,image/*" required>

          <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
            <button type="submit" class="btn btn-primary">Submit Request</button>
            <a href="dashboard.php" class="btn btn-outline-secondary">Back To Dashboard</a>
          </div>
        </form>
    <?php elseif ($request_status === 'pending'): ?>
        <p>You cannot submit a new request while your last request is pending.</p>
    <?php endif; ?>

  </div>
</div>

</body>
</html>

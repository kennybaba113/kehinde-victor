<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/connect.php');

// Ensure lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit();
}

$lecturer_id = (int) $_SESSION['lecturer_id'];

// fetch lecturer
$stmt = $conn->prepare("SELECT * FROM lecturers WHERE id = ?");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

if (!$lecturer) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$message = '';

// -----------------------------
// Handle password verification
// -----------------------------
if (isset($_POST['verify_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');
    if ($current_password === '') {
        $message = "⚠️ Please enter your current password to verify.";
    } else {
        if (password_verify($current_password, $lecturer['password'])) {
            $_SESSION['verified'] = true;
            $message = "✅ Current password verified. You can now set a new password.";
        } else {
            unset($_SESSION['verified']);
            $message = "❌ Incorrect current password.";
        }
    }
}

// -----------------------------
// Handle change password
// -----------------------------
if (isset($_POST['change_password'])) {
    if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
        $message = "❌ Please verify your current password first.";
    } else {
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if ($new_password === '' || $confirm_password === '') {
            $message = "⚠️ Fill both new password fields.";
        } elseif ($new_password !== $confirm_password) {
            $message = "❌ New passwords do not match!";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $u = $conn->prepare("UPDATE lecturers SET password = ? WHERE id = ?");
            $u->bind_param("si", $hashed, $lecturer_id);
            if ($u->execute()) {
                unset($_SESSION['verified']);
                $message = "✅ Password changed successfully.";
                $stmt->execute();
                $lecturer = $stmt->get_result()->fetch_assoc();
            } else {
                $message = "❌ Failed to change password.";
            }
            $u->close();
        }
    }
}

// -----------------------------
// Handle save profile (phone/rank/faculty/picture)
// -----------------------------
if (isset($_POST['save_profile'])) {
    $phone_number = trim($_POST['phone_number'] ?? '');
    $rank = trim($_POST['rank'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $profile_picture = $lecturer['profile_picture'] ?? '';

    // handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($ext, $allowed)) {
            $upload_dir = __DIR__ . "/../uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $staff_id_for_name = $lecturer['staff_id'];
            $newFilename = "uploads/profile_" . preg_replace('/[^a-zA-Z0-9_-]/','', $staff_id_for_name) . "." . $ext;
            $dest = dirname(__FILE__) . "/../" . $newFilename;
            if (move_uploaded_file($fileTmp, $dest)) $profile_picture = $newFilename;
        } else {
            $message = "❌ Invalid file type.";
        }
    }

    if ($message === '') {
        $u = $conn->prepare("UPDATE lecturers SET phone_number = ?, rank = ?, faculty = ?, profile_picture = ? WHERE id = ?");
        $u->bind_param("ssssi", $phone_number, $rank, $faculty, $profile_picture, $lecturer_id);
        if ($u->execute()) {
            $message = "✅ Profile saved successfully.";
            $stmt->execute();
            $lecturer = $stmt->get_result()->fetch_assoc();
        } else {
            $message = "❌ Failed to save profile.";
        }
        $u->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Lecturer Profile | UNIOSUN CBT</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:Poppins, sans-serif; background:#111; color:#fff; margin:0;}
body::before {content:"";position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(10px);z-index:0;}
.container {position:relative; z-index:1; max-width:760px; margin:80px auto; padding:28px; background:rgba(255,255,255,0.06); border-radius:14px;}
h2 {text-align:center;color:#00e676; margin-bottom:18px;}
.form-control {background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:8px;}
.profile-pic {width:120px;height:120px;border-radius:50%;object-fit:cover;border:2px solid #00e676;}
.section-header {cursor:pointer;background:rgba(0,0,0,0.2);padding:10px 15px;border-radius:8px;margin-bottom:8px;font-weight:600;display:flex;justify-content:space-between;align-items:center;}
.section-content {display:none;padding:12px;background:rgba(0,0,0,0.18);border-radius:10px;margin-bottom:12px;}
.section-header.active {background:rgba(0,0,0,0.4);}
.btn-save {background:linear-gradient(135deg,#00c853,#007e33); color:#fff; border:none; padding:10px 14px; border-radius:8px;}
.alert {color:#111;}
</style>
</head>
<body>
<nav style="position:fixed;top:0;left:0;right:0;background:rgba(0,0,0,0.6);padding:10px 20px;z-index:2;">
  <span style="color:#fff;font-weight:600">👨‍🏫 Lecturer Profile</span>
  <span style="float:right"><a href="dashboard.php" style="color:#fff;margin-right:12px">Dashboard</a><a href="logout.php" style="color:#fff">Logout</a></span>
</nav>

<div class="container">
  <h2><?= htmlspecialchars($lecturer['full_name']); ?>'s Profile</h2>

  <?php if ($message !== ''): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">

    <!-- PERSONAL INFO -->
    <div class="section">
      <div class="section-header" data-target="#personalInfo">Personal Information <span>+</span></div>
      <div id="personalInfo" class="section-content">
        <div class="mb-2"><label class="form-label">Staff ID</label><input class="form-control" value="<?= htmlspecialchars($lecturer['staff_id']); ?>" readonly></div>
        <div class="mb-2"><label class="form-label">Full Name</label><input class="form-control" value="<?= htmlspecialchars($lecturer['full_name']); ?>" readonly></div>
        <div class="mb-2"><label class="form-label">Department</label><input class="form-control" value="<?= htmlspecialchars($lecturer['department']); ?>" readonly></div>
        <div class="mb-2"><label class="form-label">Gender</label><input class="form-control" value="<?= htmlspecialchars($lecturer['gender']); ?>" readonly></div>
      </div>
    </div>

    <!-- PROFESSIONAL INFO -->
    <div class="section">
      <div class="section-header" data-target="#professionalInfo">Professional Information <span>+</span></div>
      <div id="professionalInfo" class="section-content">
        <div class="text-center mb-2">
          <?php if (!empty($lecturer['profile_picture'])): ?>
            <img src="../<?= htmlspecialchars($lecturer['profile_picture']); ?>" class="profile-pic" alt="profile">
          <?php else: ?>
            <img src="../assets/img/default.png" class="profile-pic" alt="default">
          <?php endif; ?>
        </div>
        <div class="mb-2"><label class="form-label">Upload profile picture</label><input type="file" name="profile_picture" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Phone number</label><input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($lecturer['phone_number'] ?? ''); ?>"></div>
        <div class="mb-2"><label class="form-label">Rank</label><input type="text" name="rank" class="form-control" value="<?= htmlspecialchars($lecturer['rank'] ?? ''); ?>"></div>
        <div class="mb-2"><label class="form-label">Faculty</label><input type="text" name="faculty" class="form-control" value="<?= htmlspecialchars($lecturer['faculty'] ?? ''); ?>"></div>
      </div>
    </div>

    <!-- SECURITY -->
    <div class="section">
      <div class="section-header" data-target="#securitySection">Security (Password) <span>+</span></div>
      <div id="securitySection" class="section-content">
        <div class="mb-2"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control"></div>
        <div class="mb-2"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_password" class="form-control"></div>
        <div style="display:flex;gap:10px;">
          <button type="submit" name="verify_password" class="btn btn-secondary flex-fill">Verify Password</button>
          <button type="submit" name="change_password" class="btn btn-warning flex-fill">Change Password</button>
        </div>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" name="save_profile" class="btn-save mt-3">Save Profile</button>
    </div>

  </form>
</div>

<script>
document.querySelectorAll('.section-header').forEach(header => {
  header.addEventListener('click', () => {
    const target = document.querySelector(header.dataset.target);
    const expanded = target.style.display === 'block';
    document.querySelectorAll('.section-content').forEach(sec => sec.style.display = 'none');
    document.querySelectorAll('.section-header span').forEach(s => s.textContent = '+');
    if (!expanded) {
      target.style.display = 'block';
      header.querySelector('span').textContent = '−';
    }
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

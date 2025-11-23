<?php
session_start();
include '../includes/connect.php';
include '../includes/page_history.php';

if (!empty($_SESSION['page_history'])) {
  array_pop($_SESSION['page_history']);
}

// Fetch all tests
$sql = "SELECT * FROM tests ORDER BY id DESC";
$result = $conn->query($sql);

if (isset($_GET['redirected'])) {
  echo "<script>alert('Test and questions saved successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Tests</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/img/logo.png">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: url('../assets/img/logo.png') no-repeat center center/cover;
      min-height: 100vh;
      color: #fff;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.8);
      backdrop-filter: blur(10px);
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
      max-width: 1100px;
      margin: 80px auto;
      padding: 15px;
    }

    .card {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
      overflow: hidden;
      animation: fadeIn 0.8s ease-in-out;
    }

    .card-header {
      background: linear-gradient(90deg, #00b4d8, #0077b6);
      border-bottom: none;
      border-radius: 20px 20px 0 0 !important;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 25px;
    }

    .card-header h4 {
      margin: 0;
      font-weight: 600;
    }

    .btn-light {
      background: rgba(255,255,255,0.15);
      border: none;
      color: #fff;
      font-weight: 500;
      transition: 0.3s;
    }

    .btn-light:hover {
      background: #00b4d8;
      transform: scale(1.05);
    }

    .table {
      color: #fff;
      border-radius: 15px;
      overflow: hidden;
      background: rgba(0, 0, 0, 0.5);
    }

    .table thead th {
      background: rgba(0, 180, 216, 0.2);
      color: #00e5ff;
      text-align: center;
      font-weight: 600;
      font-size: 14px;
    }

    .table tbody td {
      text-align: center;
      vertical-align: middle;
      font-size: 13px;
    }

    .table-hover tbody tr:hover {
      background: rgba(0, 180, 216, 0.1);
      transition: 0.3s;
    }

    .btn {
      border-radius: 8px;
      font-size: 13px;
      padding: 5px 10px;
      transition: 0.3s ease;
    }

    .btn-success:hover { box-shadow: 0 0 8px #00ff99; transform: scale(1.05); }
    .btn-warning:hover { box-shadow: 0 0 8px #ffea00; transform: scale(1.05); }
    .btn-danger:hover { box-shadow: 0 0 8px #ff3b3b; transform: scale(1.05); }

    .card-body {
      padding: 20px;
    }

    .badge {
      font-size: 12px;
      padding: 5px 8px;
      border-radius: 8px;
    }

    .table-container {
      border-radius: 12px;
      overflow-x: auto;
      scrollbar-width: thin;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ✅ Mobile Optimization */
    @media (max-width: 768px) {
      .card-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
      }

      .table-container {
        font-size: 12px;
      }

      .btn {
        font-size: 11px;
        padding: 4px 8px;
      }

      .table thead {
        display: none;
      }

      .table tbody td {
        display: block;
        text-align: right;
        padding: 8px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
      }

      .table tbody td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        color: #00e5ff;
      }

      .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        padding: 10px;
      }
    }
  </style>
</head>

<body>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h4>📘 Manage Tests</h4>
      <a href="create_test.php" class="btn btn-light btn-sm shadow-sm">+ Create New Test</a>
    </div>

    <div class="card-body">
      <div class="mb-3 text-end">
                <a href="dashboard.php" class="btn btn-primary">🏠 Dashboard</a>
            </div>
      <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive table-container">
          <table class="table table-hover table-bordered align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Department</th>
                <th>Level</th>
                <th>Semester</th>
                <th>Max Students</th>
                <th>Duration</th>
                <th>Allow Results</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td data-label="ID"><?php echo $row['id']; ?></td>
                  <td data-label="Course Code"><strong><?php echo htmlspecialchars($row['course_code']); ?></strong></td>
                  <td data-label="Course Title"><?php echo htmlspecialchars($row['course_title']); ?></td>
                  <td data-label="Department"><?php echo htmlspecialchars($row['department']); ?></td>
                  <td data-label="Level"><?php echo htmlspecialchars($row['level']); ?></td>
                  <td data-label="Semester"><?php echo htmlspecialchars($row['semester']); ?></td>
                  <td data-label="Max Students"><?php echo htmlspecialchars($row['max_students']); ?></td>
                  <td data-label="Duration"><?php echo htmlspecialchars($row['duration']); ?> mins</td>
                  <td data-label="Allow Results">
                    <?php if ($row['allow_results']): ?>
                      <span class="badge bg-success">Yes</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">No</span>
                    <?php endif; ?>
                  </td>
                  <td data-label="Actions">
                    <div class="d-flex flex-wrap justify-content-center gap-1">
                      <a href="add_questions.php?test_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Add Q</a>
                      <a href="edith_test.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm text-white">Edit</a>
                      <a href="delete_tests.php?test_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this test?');">Del</a>
                      <a href="view.php?test_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm text-white">View</a>
                      <?php if ($row['is_active'] == 1): ?>
                        <a href="deactivate.php?test_id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm">Deactivate</a>
                      <?php else: ?>
                        <a href="activate_test.php?test_id=<?php echo $row['id']; ?>" class="btn btn-outline-success btn-sm">Activate</a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center text-muted mt-4">No tests have been created yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  history.pushState(null, null, location.href);
  window.onpopstate = function () {
      window.location.href = "<?php echo $prev_page; ?>";
  };
</script>
</body>
</html>

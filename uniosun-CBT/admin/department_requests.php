<?php
session_start();
include '../includes/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Approve/Reject
if (isset($_POST['action'])) {
    $id = intval($_POST['id']);
    $status = $_POST['action']; // approved or rejected
    $comment = trim($_POST['comment']);

    // Fetch request details
    $req = $conn->prepare("SELECT * FROM department_change_requests WHERE id=?");
    $req->bind_param("i", $id);
    $req->execute();
    $data = $req->get_result()->fetch_assoc();

    if ($data) {
        if ($status == "approved") {
            // Update student department
            $update = $conn->prepare("UPDATE students SET department=? WHERE matric_number=?");
            $update->bind_param("ss", $data['new_department'], $data['matric_number']);
            $update->execute();
        }

        // Update request status
        $upd = $conn->prepare("
            UPDATE department_change_requests 
            SET status=?, admin_comment=?, date_reviewed=NOW() 
            WHERE id=?
        ");
        $upd->bind_param("ssi", $status, $comment, $id);
        $upd->execute();
    }

    $_SESSION['msg'] = "Request has been $status.";
    header("Location: department_requests.php");
    exit();
}

// Fetch all requests
$requests = $conn->query("SELECT * FROM department_change_requests ORDER BY date_requested DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Change Requests</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f6f9; }
        .table thead { background:#1e3a8a; color:white; }
    </style>
</head>
<body>

<div class="container mt-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <h3>Department Change Requests</h3>

    <?php if(isset($_SESSION['msg'])){ ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; ?></div>
        <?php unset($_SESSION['msg']); ?>
    <?php } ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Matric</th>
                <th>Old Dept</th>
                <th>New Dept</th>
                <th>Document</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sn = 1;
            while($row = $requests->fetch_assoc()){ ?>
                <tr>
                    <td><?= $sn++; ?></td>
                    <td><?= $row['matric_number']; ?></td>
                    <td><?= $row['old_department']; ?></td>
                    <td><?= $row['new_department']; ?></td>
                    <td>
                        <a href="../<?= $row['document_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                            View Document
                        </a>
                    </td>
                    <td><?= ucfirst($row['status']); ?></td>
                    <td><?= $row['date_requested']; ?></td>
                    <td>
                        <?php if($row['status'] == "pending"){ ?>
                            <!-- Approve Form -->
                            <form method="POST" style="margin-bottom:5px">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <textarea name="comment" class="form-control mb-1" placeholder="Admin comment..."></textarea>
                                <button name="action" value="approved" class="btn btn-success btn-sm w-100">
                                    Approve
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <textarea name="comment" class="form-control mb-1" placeholder="Reason for rejection..."></textarea>
                                <button name="action" value="rejected" class="btn btn-danger btn-sm w-100">
                                    Reject
                                </button>
                            </form>

                        <?php } else { ?>
                            <b>Reviewed</b>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>

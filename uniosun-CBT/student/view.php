<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Tests</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: start;
    padding: 40px 0;
}

.container {
    width: 95%;
    max-width: 1000px;
}

.card {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.08);
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,0,0,0.6);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 20px;
    transition: transform 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 35px rgba(0,0,0,0.8);
}

.card-title {
    font-weight: bold;
    font-size: 20px;
}

.btn-primary, .btn-secondary {
    border-radius: 10px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #520dc2);
    transform: scale(1.05);
}

.btn-secondary {
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.btn-secondary:hover {
    background: rgba(255,255,255,0.25);
}

.alert-success {
    backdrop-filter: blur(10px);
    background: rgba(0, 255, 0, 0.1);
    border: 1px solid rgba(0,255,0,0.3);
    color: #00ff00;
}

@media (max-width: 768px) {
    .card {
        padding: 15px;
    }
    .btn {
        width: 100%;
        margin-top: 10px;
    }
}
</style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-center">My Tests</h3>

    <?php if($submitted): ?>
    <div class="alert alert-success text-center">
        Test submitted successfully! Your score will appear if the lecturer allows results.
    </div>
    <?php endif; ?>

    <?php if(empty($tests)): ?>
        <p class="text-center">No tests found.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($tests as $test): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($test['course_title']); ?></h5>
                            <p><strong>Course Code:</strong> <?= htmlspecialchars($test['course_code']); ?></p>
                            <p><strong>Department:</strong> <?= htmlspecialchars($test['department']); ?></p>
                            <p><strong>Level:</strong> <?= htmlspecialchars($test['level']); ?></p>
                            <p><strong>Semester:</strong> <?= htmlspecialchars($test['semester']); ?></p>

                            <?php if(isset($test['submission_status'])): ?>
                                <?php if($test['allow_results'] == 1 || $submitted): ?>
                                    <p><strong>Status:</strong> <?= ucfirst($test['submission_status']); ?></p>
                                    <p><strong>Score:</strong> <?= $test['total_score'] ?? 0 ?></p>
                                <?php else: ?>
                                    <p><strong>Status:</strong> <?= ucfirst($test['submission_status']); ?></p>
                                    <p>Results are not yet available.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p><strong>Status:</strong> Not Attempted</p>
                                <a href="start_test.php?test_id=<?= $test['id']; ?>" class="btn btn-primary mt-2">Start Test</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="../student/dashboard.php" class="btn btn-secondary">Return to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
history.pushState(null, null, location.href); // prevent default back
window.onpopstate = function () {
    window.location.href = "<?php echo $prev_page; ?>";
};
</script>
</body>
</html>

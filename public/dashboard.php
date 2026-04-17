<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$total = $conn->query('SELECT COUNT(*) as count FROM tasks')->fetch_assoc()['count'];
$completed = $conn->query('SELECT COUNT(*) as count FROM tasks WHERE status = "Done"')->fetch_assoc()['count'];
$pending = $conn->query('SELECT COUNT(*) as count FROM tasks WHERE status = "Pending"')->fetch_assoc()['count'];
$overdue = $conn->query('SELECT COUNT(*) as count FROM tasks WHERE deadline < CURDATE() AND status != "Done"')->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WorkFlowTrack - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <span class="brand">WorkFlowTrack</span>
    <div>
        <?php if (in_array($role, ['admin', 'manager'])): ?>
        <a href="tasks.php">Tasks</a>
        <?php endif; ?>
        <a href="workflow.php">Workflow</a>
        <a href="history.php">History</a>
        <?php if (in_array($role, ['admin', 'manager', 'department'])): ?>
        <a href="reports.php">Reports</a>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
        <a href="users.php">Users</a>
        <?php endif; ?>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
        <span style="margin-left:20px; font-size:13px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>
</nav>
<div class="container">
    <div class="kpi-grid">
        <div class="kpi-tile">
            <div class="kpi-number"><?php echo $total; ?></div>
            <div class="kpi-label">Total Tasks</div>
        </div>
        <div class="kpi-tile done">
            <div class="kpi-number"><?php echo $completed; ?></div>
            <div class="kpi-label">Completed</div>
        </div>
        <div class="kpi-tile">
            <div class="kpi-number"><?php echo $pending; ?></div>
            <div class="kpi-label">Pending</div>
        </div>
        <div class="kpi-tile overdue">
            <div class="kpi-number"><?php echo $overdue; ?></div>
            <div class="kpi-label">Overdue</div>
        </div>
    </div>
    <div class="card">
        <h2>Welcome to WorkFlowTrack</h2>
        <p style="color:#666; font-size:14px;">Use the navigation above to manage tasks, track workflow progress, and generate reports.</p>
    </div>
</div>
</body>
</html>
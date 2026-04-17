<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();
requireRole(['admin', 'manager']);

$conn = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = $_POST['priority'];
        $deadline = $_POST['deadline'];
        $assigned_to = $_POST['assigned_to'];

        if (empty($title) || empty($description) || empty($deadline) || empty($assigned_to)) {
            $error = 'All fields are required.';
        } elseif ($deadline <= date('Y-m-d')) {
            $error = 'Deadline must be a future date.';
        } else {
            $check = $conn->prepare('SELECT task_id FROM tasks WHERE title = ?');
            $check->bind_param('s', $title);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = 'A task with this title already exists.';
            } else {
                $stmt = $conn->prepare('INSERT INTO tasks (title, description, priority, deadline, assigned_to, created_by) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssis', $title, $description, $priority, $deadline, $assigned_to, $_SESSION['user_id']);
                if ($stmt->execute()) {
                    $task_id = $conn->insert_id;
                    $log = $conn->prepare('INSERT INTO audit_log (task_id, user_id, action, new_value, ip_address) VALUES (?, ?, ?, ?, ?)');
                    $action_log = 'CREATE';
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $log->bind_param('iisss', $task_id, $_SESSION['user_id'], $action_log, $title, $ip);
                    $log->execute();
                    $notif = $conn->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
                    $msg = 'You have been assigned a new task: ' . $title;
                    $notif->bind_param('is', $assigned_to, $msg);
                    $notif->execute();
                    $message = 'Task has been successfully created.';
                }
            }
        }
    } elseif ($action === 'delete') {
        $task_id = (int)$_POST['task_id'];
        $log = $conn->prepare('INSERT INTO audit_log (task_id, user_id, action, ip_address) VALUES (?, ?, ?, ?)');
        $action_log = 'DELETE';
        $ip = $_SERVER['REMOTE_ADDR'];
        $log->bind_param('iiss', $task_id, $_SESSION['user_id'], $action_log, $ip);
        $log->execute();
        $stmt = $conn->prepare('DELETE FROM tasks WHERE task_id = ?');
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        $message = 'Task has been successfully deleted.';
    } elseif ($action === 'edit') {
        $task_id = (int)$_POST['task_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = $_POST['priority'];
        $deadline = $_POST['deadline'];
        $assigned_to = $_POST['assigned_to'];
        $stmt = $conn->prepare('UPDATE tasks SET title=?, description=?, priority=?, deadline=?, assigned_to=? WHERE task_id=?');
        $stmt->bind_param('sssiii', $title, $description, $priority, $deadline, $assigned_to, $task_id);
        $stmt->execute();
        $log = $conn->prepare('INSERT INTO audit_log (task_id, user_id, action, new_value, ip_address) VALUES (?, ?, ?, ?, ?)');
        $action_log = 'UPDATE';
        $ip = $_SERVER['REMOTE_ADDR'];
        $log->bind_param('iisss', $task_id, $_SESSION['user_id'], $action_log, $title, $ip);
        $log->execute();
        $message = 'Task has been successfully updated.';
    }
}

$search = $_GET['search'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_priority = $_GET['priority'] ?? '';

$sql = 'SELECT t.*, u.username as assigned_name FROM tasks t LEFT JOIN users u ON t.assigned_to = u.user_id WHERE 1=1';
if ($search) $sql .= ' AND t.title LIKE "%' . $conn->real_escape_string($search) . '%"';
if ($filter_status) $sql .= ' AND t.status = "' . $conn->real_escape_string($filter_status) . '"';
if ($filter_priority) $sql .= ' AND t.priority = "' . $conn->real_escape_string($filter_priority) . '"';
$tasks = $conn->query($sql);

$users = $conn->query('SELECT user_id, username FROM users WHERE status = "active" AND role = "member"');
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WorkFlowTrack - Task Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <span class="brand">WorkFlowTrack</span>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="tasks.php">Tasks</a>
        <a href="workflow.php">Workflow</a>
        <a href="history.php">History</a>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
        <span style="margin-left:20px; font-size:13px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>
</nav>
<div class="container">
    <?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <div class="card">
        <h2>Create New Task</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Task Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>
            <div class="form-group">
                <label>Priority</label>
                <select name="priority">
                    <option>Low</option>
                    <option>Medium</option>
                    <option>High</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" required>
            </div>
            <div class="form-group">
                <label>Assigned To</label>
                <select name="assigned_to" required>
                    <?php if (isset($users)) $users->data_seek(0); ?>
                    <?php while ($u = $users->fetch_assoc()): ?>
                    <option value="<?php echo $u['user_id']; ?>"><?php echo htmlspecialchars($u['username']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Task List</h2>
        <form method="GET" style="display:flex; gap:10px; margin-bottom:16px;">
            <input type="text" name="search" placeholder="Search by title" value="<?php echo htmlspecialchars($search); ?>" style="flex:1; padding:8px; border:1px solid #ccc; border-radius:6px;">
            <select name="status" style="padding:8px; border:1px solid #ccc; border-radius:6px;">
                <option value="">All Status</option>
                <option <?php if($filter_status=='Pending') echo 'selected'; ?>>Pending</option>
                <option <?php if($filter_status=='In Progress') echo 'selected'; ?>>In Progress</option>
                <option <?php if($filter_status=='Done') echo 'selected'; ?>>Done</option>
            </select>
            <select name="priority" style="padding:8px; border:1px solid #ccc; border-radius:6px;">
                <option value="">All Priority</option>
                <option <?php if($filter_priority=='Low') echo 'selected'; ?>>Low</option>
                <option <?php if($filter_priority=='Medium') echo 'selected'; ?>>Medium</option>
                <option <?php if($filter_priority=='High') echo 'selected'; ?>>High</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="tasks.php" class="btn btn-secondary">Reset</a>
        </form>
        <table>
            <tr>
                <th>ID</th><th>Title</th><th>Priority</th><th>Deadline</th><th>Status</th><th>Assigned To</th><th>Actions</th>
            </tr>
            <?php while ($task = $tasks->fetch_assoc()): ?>
            <tr>
                <td><?php echo $task['task_id']; ?></td>
                <td><?php echo htmlspecialchars($task['title']); ?></td>
                <td><span class="badge badge-<?php echo strtolower($task['priority']); ?>"><?php echo $task['priority']; ?></span></td>
                <td><?php echo $task['deadline']; ?></td>
                <td><span class="badge badge-<?php echo strtolower(str_replace(' ','-',$task['status'])); ?>"><?php echo $task['status']; ?></span></td>
                <td><?php echo htmlspecialchars($task['assigned_name'] ?? ''); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
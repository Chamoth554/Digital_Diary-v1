<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user role
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch logs for interns
if ($role === 'Intern') {
    $stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $logs = $stmt->fetchAll();
} elseif ($role === 'Mentor') {
    $stmt = $pdo->prepare("SELECT dl.*, u.name AS intern_name FROM daily_logs dl 
                           JOIN users u ON dl.user_id = u.id WHERE dl.status = 'Pending'");
    $stmt->execute();
    $logs = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
    
    

        <?php if ($_SESSION['role'] === 'Intern'): ?>
            <div class="d-flex justify-content-between align-items-center">
            <h3>Intern Dashboard</h3>
            <a href="logout.php" class="btn btn-danger btn-md">Logout</a>
        </div>
    <p class="mt-4">Welcome, <?php echo $_SESSION['name']; ?>! Below are your logs:</p>
    
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Date</th>
                <th>Notes</th>
                <th>Tasks</th>
                <th>Time Spent</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['date']); ?></td>
                        <td><?php echo htmlspecialchars($log['notes']); ?></td>
                        <td><?php echo htmlspecialchars($log['tasks']); ?></td>
                        <td><?php echo $log['time_spent']; ?> minutes</td>
                        <td>
                            <?php
                            // Status badge with dynamic styling
                            if ($log['status'] === 'Pending') {
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                            } elseif ($log['status'] === 'Approved') {
                                echo '<span class="badge bg-success">Approved</span>';
                            } elseif ($log['status'] === 'Rejected') {
                                echo '<span class="badge bg-danger">Rejected</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No logs submitted yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="text-end mt-3">
    <a href="add_log.php" class="btn btn-primary btn-md">Create Log</a>
</div>

<?php elseif ($role === 'Mentor'): ?>
    
    <p class="mt-4">Welcome, Mr. <?php echo $_SESSION['name']; ?> ! Below are pending logs:</p>
    <!-- Mentor view -->
    <div class="container mt-4">
        <!-- Mentor Dashboard Header -->
        <div class="d-flex justify-content-between align-items-center">
            <h3>Mentor Dashboard</h3>
            <a href="logout.php" class="btn btn-danger btn-md">Logout</a>
        </div>

        <!-- Pending Logs Table -->
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>Intern Name</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th>Tasks</th>
                    <th>Time Spent</th>
                    <th>Status</th> <!-- Column for Status -->
                    <th>Action</th> <!-- Column for Approve/Reject -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch pending logs from the database
                $stmt = $pdo->prepare("SELECT * FROM daily_logs WHERE status = 'Pending'");
                $stmt->execute();
                $logs = $stmt->fetchAll();

                if (count($logs) > 0): 
                    foreach ($logs as $log): 
                        // Fetch the intern's name
                        $stmt_user = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                        $stmt_user->execute([$log['user_id']]);
                        $user = $stmt_user->fetch();
                ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $log['date']; ?></td>
                        <td><?php echo htmlspecialchars($log['notes']); ?></td>
                        <td><?php echo htmlspecialchars($log['tasks']); ?></td>
                        <td><?php echo $log['time_spent']; ?> minutes</td>

                        <!-- Display the current status of the log -->
                        <td>
                            <?php echo $log['status']; ?>
                        </td>

                        <td>
                            <!-- Show Approve/Reject buttons only if the log is still pending -->
                            <?php if ($log['status'] === 'Pending'): ?>
                                <form action="review_log.php" method="POST">
                                    <input type="hidden" name="log_id" value="<?php echo $log['id']; ?>">

                                    <!-- Approve and Reject Buttons -->
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <!-- If already approved or rejected, disable buttons -->
                                <span class="text-muted">Action Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No pending logs to review.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

    </div>
</body>
</html>
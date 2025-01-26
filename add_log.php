<?php
// Start the session and include the database connection
session_start();
require 'db.php';

// Check if the user is logged in and is an intern
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Intern') {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_log'])) {
    // Retrieve and sanitize input values
    $date = htmlspecialchars($_POST['date']);
    $notes = htmlspecialchars($_POST['notes']);
    $tasks = htmlspecialchars($_POST['tasks']);
    $time_spent = htmlspecialchars($_POST['time_spent']);
    $user_id = $_SESSION['user_id'];
    $file_path = null;

    // Handle file upload (optional)
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_error = $_FILES['file']['error'];

        // File upload validation
        if ($file_size <= 10000000) { // 10MB max file size
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
            if (in_array($file_ext, $allowed_ext)) {
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = 'uploads/' . $file_new_name;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    $file_path = $file_destination;
                } else {
                    $error = "Error uploading the file.";
                }
            } else {
                $error = "Invalid file type. Allowed types: jpg, jpeg, png, pdf, docx.";
            }
        } else {
            $error = "File is too large. Maximum size allowed: 10MB.";
        }
    }

    // Insert the new log into the database
    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO daily_logs (user_id, date, notes, tasks, time_spent, file_path, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $date, $notes, $tasks, $time_spent, $file_path]);

            // Redirect to the dashboard after successful log submission
            header('Location:dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error inserting log: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Log - Digital Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center">Add Daily Log</h3>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form action="add_log.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" id="date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tasks" class="form-label">Tasks</label>
                                <textarea name="tasks" id="tasks" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="time_spent" class="form-label">Time Spent (in minutes)</label>
                                <input type="number" name="time_spent" id="time_spent" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Attach File (optional)</label>
                                <input type="file" name="file" id="file" class="form-control">
                            </div>
                            <button type="submit" name="submit_log" class="btn btn-primary w-100">Submit Log</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

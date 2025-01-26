<?php
// Start the session and include the database connection
session_start();
require 'db.php';

// Check if the user is logged in and is a mentor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Mentor') {
    header('Location: login.php');
    exit;
}

// Handle the form submission (approve or reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_id = $_POST['log_id'];
    $action = $_POST['action'];

    // Prepare the SQL query based on the action
    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    }

    // Update the status of the log in the daily_logs table
    $stmt = $pdo->prepare("UPDATE daily_logs SET status = ? WHERE id = ?");
    $stmt->execute([$status, $log_id]);

    // Redirect back to the mentor dashboard
    header('Location: dashboard.php');
    exit;
}

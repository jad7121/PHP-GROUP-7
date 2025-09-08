<?php
// Start session for potential future use
session_start();

// Include database configuration
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$task_id = intval($_GET['id']);

// Update task status to completed
try {
    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE study_tasks SET status = 'completed' WHERE id = :id");
    
    // Bind parameters
    $stmt->bindParam(':id', $task_id);
    
    // Execute query
    $stmt->execute();
    
    // Set success message in session (for future enhancement)
    // $_SESSION['message'] = "Task marked as completed!";
    // $_SESSION['message_type'] = "success";
} catch (PDOException $e) {
    // Set error message in session (for future enhancement)
    // $_SESSION['message'] = "Error: " . $e->getMessage();
    // $_SESSION['message_type'] = "error";
}

// Redirect back to index page
header("Location: index.php");
exit;
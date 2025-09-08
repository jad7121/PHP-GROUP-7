<?php
// Start session for potential future use
session_start();

// Include database configuration
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'task' => null
];

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $subject = htmlspecialchars(trim($_POST['subject']));
    $task = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $priority = isset($_POST['priority']) ? htmlspecialchars($_POST['priority']) : 'medium';
    
    // Basic validation
    if (empty($subject) || empty($task) || empty($due_date)) {
        $response['message'] = "Please fill in all required fields";
    } else {
        try {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO study_tasks (subject, task_description, due_date, priority) 
                                  VALUES (:subject, :task, :due_date, :priority)");
            
            // Bind parameters
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':task', $task);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->bindParam(':priority', $priority);
            
            // Execute query
            if ($stmt->execute()) {
                $task_id = $conn->lastInsertId();
                
                // Fetch the newly created task
                $stmt = $conn->prepare("SELECT * FROM study_tasks WHERE id = :id");
                $stmt->bindParam(':id', $task_id);
                $stmt->execute();
                $task_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $response['success'] = true;
                $response['message'] = "Task added successfully!";
                $response['task'] = $task_data;
            } else {
                $response['message'] = "Unable to add task.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Database Error: " . $e->getMessage();
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
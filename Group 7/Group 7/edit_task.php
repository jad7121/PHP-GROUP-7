<?php
// Start session for potential future use
session_start();

// Include database configuration
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Initialize variables
$message = '';
$messageType = '';
$task = null;

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$task_id = intval($_GET['id']);

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    // Validate and sanitize input
    $subject = htmlspecialchars(trim($_POST['subject']));
    $task_description = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $priority = isset($_POST['priority']) ? htmlspecialchars($_POST['priority']) : 'medium';
    $status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : 'pending';
    
    // Basic validation
    if (empty($subject) || empty($task_description) || empty($due_date)) {
        $message = "Please fill in all required fields";
        $messageType = "error";
    } else {
        try {
            // Prepare SQL statement
            $stmt = $conn->prepare("UPDATE study_tasks 
                                  SET subject = :subject, 
                                      task_description = :task, 
                                      due_date = :due_date, 
                                      priority = :priority,
                                      status = :status
                                  WHERE id = :id");
            
            // Bind parameters
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':task', $task_description);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $task_id);
            
            // Execute query
            if ($stmt->execute()) {
                $message = "Task updated successfully!";
                $messageType = "success";
                
                // Redirect after short delay
                header("Refresh: 2; URL=index.php");
            } else {
                $message = "Unable to update task.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Fetch the task data
try {
    $stmt = $conn->prepare("SELECT * FROM study_tasks WHERE id = :id");
    $stmt->bindParam(':id', $task_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Task not found
        $message = "Task not found.";
        $messageType = "error";
        header("Refresh: 2; URL=index.php");
    }
} catch (PDOException $e) {
    $message = "Database Error: " . $e->getMessage();
    $messageType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Student Study Planner</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Study Planner</h1>
            <p>Edit your study task</p>
        </header>

        <main style="grid-template-columns: 1fr;">
            <section class="task-form-container">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Tasks</a>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($task): ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $task_id); ?>" method="post" class="task-form">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($task['subject']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="task">Task</label>
                            <textarea id="task" name="task" required><?php echo htmlspecialchars($task['task_description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select id="priority" name="priority">
                                <option value="high" <?php echo ($task['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                                <option value="medium" <?php echo ($task['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="low" <?php echo ($task['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>

                        <button type="submit" name="update_task" class="btn-add">Update Task</button>
                    </form>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Student Study Planner. All rights reserved.</p>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
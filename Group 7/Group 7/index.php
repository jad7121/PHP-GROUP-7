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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    // Validate and sanitize input
    $subject = htmlspecialchars(trim($_POST['subject']));
    $task = htmlspecialchars(trim($_POST['task']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $priority = isset($_POST['priority']) ? htmlspecialchars($_POST['priority']) : 'medium';
    
    // Basic validation
    if (empty($subject) || empty($task) || empty($due_date)) {
        $message = "Please fill in all required fields";
        $messageType = "error";
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
                $message = "Task added successfully!";
                $messageType = "success";
            } else {
                $message = "Unable to add task.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Fetch existing tasks
$tasks = [];
try {
    $stmt = $conn->prepare("SELECT * FROM study_tasks ORDER BY due_date ASC");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silently handle error - might be first run before table exists
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Study Planner</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Study Planner</h1>
            <p>Organize your study tasks efficiently</p>
        </header>

        <main>
            <section class="task-form-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="task-form">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="e.g. Mathematics" required>
                    </div>

                    <div class="form-group">
                        <label for="task">Task</label>
                        <textarea id="task" name="task" placeholder="Describe the study task..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" id="due_date" name="due_date" required>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority">
                            <option value="high">High</option>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>

                    <button type="submit" name="add_task" class="btn-add">Add To Planner</button>
                </form>
            </section>

            <section class="tasks-container">
                <h2>Your Study Tasks</h2>
                
                <?php if (empty($tasks)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <p>No study tasks yet. Add your first task above!</p>
                    </div>
                <?php else: ?>
                    <div class="tasks-list">
                        <?php foreach ($tasks as $task): ?>
                            <div class="task-card priority-<?php echo $task['priority']; ?>">
                                <div class="task-header">
                                    <h3><?php echo htmlspecialchars($task['subject']); ?></h3>
                                    <span class="priority-badge"><?php echo ucfirst($task['priority']); ?></span>
                                </div>
                                <p class="task-description"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                <div class="task-footer">
                                    <div class="due-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <span>Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?></span>
                                    </div>
                                    <div class="task-actions">
                                        <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                        <a href="mark_complete.php?id=<?php echo $task['id']; ?>" class="btn-complete"><i class="fas fa-check"></i></a>
                                        <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this task?');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
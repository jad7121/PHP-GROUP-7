/**
 * Student Study Planner JavaScript
 * Adds interactivity and enhanced user experience
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker with today's date as minimum
    const dueDateInput = document.getElementById('due_date');
    if (dueDateInput) {
        const today = new Date().toISOString().split('T')[0];
        dueDateInput.setAttribute('min', today);
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });

    // Add animation to task cards
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach((card, index) => {
        // Stagger the animation delay
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-in');
    });

    // AJAX form submission
    const taskForm = document.querySelector('.task-form');
    if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const subject = document.getElementById('subject').value.trim();
            const task = document.getElementById('task').value.trim();
            const dueDate = document.getElementById('due_date').value;
            const priority = document.getElementById('priority').value;
            
            if (!subject || !task || !dueDate) {
                showFormError('Please fill in all required fields');
                return false;
            }
            
            // Add loading state to button
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            submitBtn.disabled = true;
            
            // Create form data
            const formData = new FormData();
            formData.append('subject', subject);
            formData.append('task', task);
            formData.append('due_date', dueDate);
            formData.append('priority', priority);
            
            // Send AJAX request
            fetch('add_task_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.innerHTML = 'Add To Planner';
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Show success message
                    showAlert(data.message, 'success');
                    
                    // Add the new task to the list
                    addTaskToList(data.task);
                    
                    // Reset form
                    taskForm.reset();
                    
                    // Set minimum date again after form reset
                    const today = new Date().toISOString().split('T')[0];
                    dueDateInput.setAttribute('min', today);
                    
                    // Remove empty state if it exists
                    const emptyState = document.querySelector('.empty-state');
                    if (emptyState) {
                        emptyState.remove();
                        document.querySelector('.tasks-container').innerHTML += '<div class="tasks-list"></div>';
                    }
                } else {
                    // Show error message
                    showFormError(data.message);
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.innerHTML = 'Add To Planner';
                submitBtn.disabled = false;
                
                // Show error message
                showFormError('An error occurred. Please try again.');
                console.error('Error:', error);
            });
            
            return false;
        });
    }

    // Function to show form error
    function showFormError(message) {
        showAlert(message, 'error');
    }
    
    // Function to show alert
    function showAlert(message, type) {
        // Remove any existing alerts
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        // Insert at the top of the form
        const formContainer = document.querySelector('.task-form-container');
        formContainer.insertBefore(alert, formContainer.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    }
    
    // Function to add a new task to the list
    function addTaskToList(task) {
        // Get the tasks list container
        let tasksList = document.querySelector('.tasks-list');
        
        // If tasks list doesn't exist, create it
        if (!tasksList) {
            const tasksContainer = document.querySelector('.tasks-container');
            tasksList = document.createElement('div');
            tasksList.className = 'tasks-list';
            tasksContainer.appendChild(tasksList);
        }
        
        // Create task card element
        const taskCard = document.createElement('div');
        taskCard.className = `task-card priority-${task.priority} animate-in`;
        
        // Format the date
        const dueDate = new Date(task.due_date);
        const formattedDate = dueDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        
        // Set the HTML content
        taskCard.innerHTML = `
            <div class="task-header">
                <h3>${task.subject}</h3>
                <span class="priority-badge">${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}</span>
            </div>
            <p class="task-description">${task.task_description}</p>
            <div class="task-footer">
                <div class="due-date">
                    <i class="far fa-calendar-alt"></i>
                    <span>Due: ${formattedDate}</span>
                </div>
                <div class="task-actions">
                    <a href="edit_task.php?id=${task.id}" class="btn-edit"><i class="fas fa-edit"></i></a>
                    <a href="mark_complete.php?id=${task.id}" class="btn-complete"><i class="fas fa-check"></i></a>
                    <a href="delete_task.php?id=${task.id}" class="btn-delete" onclick="return confirm('Are you sure you want to delete this task?');"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        `;
        
        // Add the task card to the beginning of the list for better visibility
        tasksList.insertBefore(taskCard, tasksList.firstChild);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert {
            transition: opacity 0.5s ease;
        }
    `;
    document.head.appendChild(style);
});
-- Create the database for Student Study Planner
CREATE DATABASE IF NOT EXISTS student_planner;

-- Use the database
USE student_planner;

-- Create the tasks table
CREATE TABLE IF NOT EXISTS study_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(100) NOT NULL,
    task_description TEXT NOT NULL,
    due_date DATE NOT NULL,
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for faster queries
CREATE INDEX idx_subject ON study_tasks(subject);
CREATE INDEX idx_due_date ON study_tasks(due_date);
CREATE INDEX idx_status ON study_tasks(status);

-- Sample data (optional)
INSERT INTO study_tasks (subject, task_description, due_date, priority) VALUES
('Mathematics', 'Complete calculus homework', '2023-12-15', 'high'),
('Physics', 'Prepare for midterm exam', '2023-12-20', 'high'),
('Computer Science', 'Finish programming project', '2023-12-25', 'medium'),
('Literature', 'Read chapters 5-8 of assigned book', '2023-12-18', 'medium'),
('History', 'Research paper on World War II', '2023-12-22', 'low');
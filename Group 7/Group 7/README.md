# Student Study Planner

A modern PHP-based web application for students to organize and manage their study tasks efficiently. This application features a sleek, dark-themed UI with gradient accents and connects to a MySQL database through XAMPP.

## Features

- Add study tasks with subject, description, due date, and priority
- View all tasks in a visually appealing card layout
- Edit existing tasks
- Mark tasks as completed
- Delete tasks
- Responsive design for desktop and mobile devices
- Modern UI with animations and visual feedback

## Requirements

- XAMPP (or equivalent with PHP 7.4+ and MySQL)
- Web browser with JavaScript enabled

## Installation

1. Clone or download this repository to your XAMPP's `htdocs` folder
2. Start XAMPP and ensure Apache and MySQL services are running
3. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
4. Create a new database named `student_planner`
5. Import the `database.sql` file to set up the database structure
6. Access the application through your web browser at http://localhost/Projectwork%20php/

## Database Configuration

The database connection settings are in `config/database.php`. By default, it uses:

- Host: localhost
- Database: student_planner
- Username: root
- Password: (empty by default in XAMPP)

If your MySQL setup has different credentials, please update this file accordingly.

## Usage

1. **Adding Tasks**:
   - Fill in the subject, task description, due date, and select a priority
   - Click "Add To Planner" to save the task

2. **Viewing Tasks**:
   - All tasks are displayed in the "Your Study Tasks" section
   - Tasks are color-coded by priority (high, medium, low)

3. **Managing Tasks**:
   - Click the edit icon to modify a task
   - Click the check icon to mark a task as completed
   - Click the trash icon to delete a task

## File Structure

```
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── config/
│   └── database.php
├── database.sql
├── delete_task.php
├── edit_task.php
├── index.php
├── mark_complete.php
└── README.md
```

## Customization

You can customize the appearance by modifying the CSS variables in `assets/css/style.css`. The main color scheme is defined at the top of the file in the `:root` selector.

## License

This project is open-source and available for personal and educational use.

## Credits

- Font Awesome for icons
- Google Fonts (Poppins) for typography
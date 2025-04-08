<?php
require_once '../config/Database.php';
require_once '../models/User.php';
require_once '../models/Task.php';

$db = (new Database())->connect();

// Create admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$db->exec("INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin@example.com', '$adminPassword', 'admin')");

// Create regular user
$userPassword = password_hash('user123', PASSWORD_DEFAULT);
$db->exec("INSERT INTO users (name, email, password) VALUES ('Regular User', 'user@example.com', '$userPassword')");

// Create sample tasks
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));

$db->exec("INSERT INTO tasks (user_id, title, description, due_date, priority, status) VALUES 
    (1, 'Complete project setup', 'Set up the initial project structure', '$today', 'high', 'pending'),
    (1, 'Implement user authentication', 'Create login and registration system', '$tomorrow', 'high', 'in_progress'),
    (2, 'Learn PHP', 'Study PHP OOP concepts', '$yesterday', 'medium', 'pending'),
    (2, 'Write documentation', 'Document the project features', '$today', 'low', 'completed')");

echo "Database seeded successfully.\n";
?>
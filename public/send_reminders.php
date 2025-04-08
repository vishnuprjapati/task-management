<?php
require_once '../config/Database.php';
require_once '../models/Task.php';
require_once '../models/User.php';
require_once '../config/Mailer.php';

// Initialize database connection
$db = (new Database())->connect();

// Update task statuses based on due date
$taskModel = new Task();
$taskModel->updateStatusBasedOnDueDate();

// Send daily reminders
$taskController = new TaskController();
$taskController->sendDailyReminders();

echo "Daily reminders sent successfully.\n";
?>
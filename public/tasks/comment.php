<?php
require_once '../controllers/TaskController.php';

$taskController = new TaskController();
if (isset($_GET['task_id'])) {
    $taskController->addComment($_GET['task_id']);
} else {
    $_SESSION['error'] = 'Task ID not provided';
    header('Location: index.php');
    exit;
}
?>
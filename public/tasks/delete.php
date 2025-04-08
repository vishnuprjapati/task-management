<?php
require_once '../controllers/TaskController.php';

$taskController = new TaskController();
if (isset($_GET['id'])) {
    $taskController->delete($_GET['id']);
} else {
    $_SESSION['error'] = 'Task ID not provided';
    header('Location: index.php');
    exit;
}
?>
<?php
require_once '../controllers/UserController.php';

$userController = new UserController();
if (isset($_GET['id'])) {
    $userController->delete($_GET['id']);
} else {
    $_SESSION['error'] = 'User ID not provided';
    header('Location: index.php');
    exit;
}
?>
<?php
session_start();
require_once '../config/Auth.php';
require_once '../models/User.php';

class AuthController {
    private $auth;
    private $userModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->userModel = new User();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate input
            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: ../views/auth/register.php');
                exit;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Passwords do not match';
                header('Location: ../views/auth/register.php');
                exit;
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters';
                header('Location: ../views/auth/register.php');
                exit;
            }

            // Check if email exists
            if ($this->userModel->getByEmail($email)) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: ../views/auth/register.php');
                exit;
            }

            // Register user
            if ($this->auth->register($name, $email, $password)) {
                $_SESSION['success'] = 'Registration successful. Please login.';
                header('Location: ../views/auth/login.php');
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed';
                header('Location: ../views/auth/register.php');
                exit;
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Email and password are required';
                header('Location: ../views/auth/login.php');
                exit;
            }

            if ($this->auth->login($email, $password)) {
                header('Location: ../public/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                header('Location: ../views/auth/login.php');
                exit;
            }
        }
    }

    public function logout() {
        $this->auth->logout();
        header('Location: login.php');
        exit;
    }
}

// Handling the request based on the 'action' parameter
if (isset($_GET['action'])) {
    $authController = new AuthController();

    $action = $_GET['action'];

    if (method_exists($authController, $action)) {
        $authController->$action();
    } else {
        echo "Invalid action specified!";
    }
} else {
    echo "No action specified!";
}   
?>
<?php

require_once  __DIR__ . '/../config/Auth.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    private $auth;
    private $db;


    public function __construct() {
        $this->userModel = new User();
        $this->auth = new Auth();
        $this->db = (new Database())->connect();
        $this->userModel = new User();
    }

    public function index() {
        if (!$this->auth->isAdmin()) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: ../public/index.php');
            exit;
        }

        $users = $this->userModel->getAll();
        return ['users' => $users];
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $role = $_POST['role'] ?? 'user';
    
            // Validation
            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match";
                header('Location: /task-manager/views/users/create.php');
                exit;
            }
    
            if ($this->userModel->getByEmail($email)) {
                $_SESSION['error'] = "Email already exists";
                header('Location: /task-manager/views/users/create.php');
                exit;
            }
    
            // Create user
            if ($this->auth->register($name, $email, $password, $role)) {
                $_SESSION['success'] = "User created successfully";
                header('Location: /task-manager/views/users/index.php');
                exit;
            } else {
                $_SESSION['error'] = "Failed to create user";
                header('Location: /task-manager/views/users/create.php');
                exit;
            }
        }
    }


    public function getUser($id) {
        return $this->userModel->getById($id);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'user';
            $newPassword = $_POST['new_password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;

            // Validate required fields
            if (empty($name) || empty($email) || empty($userId)) {
                $_SESSION['error'] = "All fields are required";
                header("Location: /task-manager/views/users/edit.php?id=$userId");
                exit;
            }

            // Password change validation
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['error'] = "New passwords do not match";
                    header("Location: /task-manager/views/users/edit.php?id=$userId");
                    exit;
                }
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            try {
                // Update user in database
                if (!empty($newPassword)) {
                    $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $hashedPassword, $userId]);
                } else {
                    $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $userId]);
                }

                $_SESSION['success'] = "User updated successfully";
                header('Location: /task-manager/views/users/index.php');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = "Failed to update user: " . $e->getMessage();
                header("Location: /task-manager/views/users/edit.php?id=$userId");
                exit;
            }
        }
    }

    public function delete() {
        // Check if ID is provided
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'User ID not provided';
            header('Location: /task-manager/views/users/index.php');
            exit;
        }
    
        $userId = $_GET['id'];
    
        // Prevent admin from deleting themselves
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['error'] = 'You cannot delete your own account';
            header('Location: /task-manager/views/users/index.php');
            exit;
        }
    
        try {
            // Check if user exists
            $user = $this->userModel->getById($userId);
            if (!$user) {
                $_SESSION['error'] = 'User not found';
                header('Location: /task-manager/views/users/index.php');
                exit;
            }
    
            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$userId])) {
                $_SESSION['success'] = 'User deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete user';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    
        header('Location: /task-manager/views/users/index.php');
        exit;
    }
}


// Handling the request based on the 'action' parameter
if (isset($_GET['action'])) {
    $userController = new UserController();

    $action = $_GET['action'];

    if (method_exists($userController, $action)) {
        if($action == "update" || $action == "delete")  {
            $userController->$action($_GET['id']);
        }
        else {
            $userController->$action();
        }
    } else {
        echo "Invalid action specified!";
    }
}
?>
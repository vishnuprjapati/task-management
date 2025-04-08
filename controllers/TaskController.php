<?php
require_once  __DIR__ . '/../models/Task.php';
require_once  __DIR__ . '/../models/Comment.php';
require_once  __DIR__ . '/../config/Auth.php';
require_once  __DIR__ . '/../config/Mailer.php';
require_once  __DIR__ . '/../config/Security.php';

class TaskController {
    private $taskModel;
    private $commentModel;
    private $auth;
    private $mailer;
    private $security;

    public function __construct() {
        $this->taskModel = new Task();
        $this->commentModel = new Comment();
        $this->auth = new Auth();
        $this->mailer = new Mailer();
        $this->security = new Security();
    }

    public function index() {
        $user = $this->auth->getUser();
        if (!$user) {
            header('Location: login.php');
            exit;
        }

        // Update task statuses based on due date
        $this->taskModel->updateStatusBasedOnDueDate();

        $tasks = [];
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $priority = isset($_GET['priority']) ? $_GET['priority'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        if ($search) {
            $tasks = $this->taskModel->search($user['id'], $search, $status, $priority);
        } else {
            if ($status) {
                $tasks = $this->taskModel->getByStatus($user['id'], $status);
            } else {
                $tasks = $this->auth->isAdmin() ? $this->taskModel->getAll() : $this->taskModel->getAllForUser($user['id']);
            }
        }

        $pending = $this->taskModel->getByStatus($user['id'], 'pending');
        $inProgress = $this->taskModel->getByStatus($user['id'], 'in_progress');
        $pastDue = $this->taskModel->getByStatus($user['id'], 'past_due');
        $completed = $this->taskModel->getByStatus($user['id'], 'completed');

        return [
            'tasks' => $tasks,
            'pendingCount' => count($pending),
            'inProgressCount' => count($inProgress),
            'pastDueCount' => count($pastDue),
            'completedCount' => count($completed),
            'dueToday' => $this->taskModel->getDueToday($user['id']),
            'pastDue' => $this->taskModel->getPastDue($user['id'])
        ];
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            
            $user = $this->auth->getUser();
            if (!$this->security->verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Invalid CSRF token');
            }
            if (!$user) {
                header('Location: login.php');
                exit;
            }

            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $dueDate = $_POST['due_date'];
            $priority = $_POST['priority'];

            if (empty($title) || empty($dueDate)) {
                $_SESSION['error'] = 'Title and due date are required';

                $_SESSION['form_data'] = $_POST;

                header('Location: create.php');
                exit;
            }

            if ($this->taskModel->create($user['id'], $title, $description, $dueDate, $priority)) {
                $_SESSION['success'] = 'Task created successfully';
                header('Location: ../views/tasks/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Failed to create task';

                $_SESSION['form_data'] = $_POST;

                header('Location: create.php');
                exit;
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->auth->getUser();
            if (!$user) {
                header('Location: login.php');
                exit;
            }

            // Check if task belongs to user (unless admin)
            $task = $this->taskModel->getById($id);
            if (!$task || (!$this->auth->isAdmin() && $task['user_id'] != $user['id'])) {
                $_SESSION['error'] = 'Task not found or unauthorized';
                header('Location: index.php');
                exit;
            }

            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $dueDate = $_POST['due_date'];
            $priority = $_POST['priority'];
            $status = $_POST['status'];

            if (empty($title) || empty($dueDate)) {
                $_SESSION['error'] = 'Title and due date are required';
                header("Location: edit.php?id=$id");
                exit;
            }

            if ($this->taskModel->update($id, $title, $description, $dueDate, $priority, $status)) {
                // Send email notification if status changed
                if ($task['status'] != $status) {
                    $this->sendStatusChangeEmail($task, $status);
                }

                $_SESSION['success'] = 'Task updated successfully';
                header('Location: ../views/tasks/index.php');
                
                exit;
            } else {
                $_SESSION['error'] = 'Failed to update task';
                header("Location: edit.php?id=$id");
                exit;
            }
        }
    }

    public function delete($id) {
        $user = $this->auth->getUser();
        
        if (!$user) {
            header('Location: login.php');
            exit;
        }

        // Check if task belongs to user (unless admin)
        $task = $this->taskModel->getById($id);
        if (!$task || (!$this->auth->isAdmin() && $task['user_id'] != $user['id'])) {
            $_SESSION['error'] = 'Task not found or unauthorized';
            header('Location: index.php');
            exit;
        }

        if ($this->taskModel->delete($id)) {
            $_SESSION['success'] = 'Task deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete task';
        }
        header('Location: ../views/tasks/index.php');
        exit;
    }

    public function show($id) {
        $user = $this->auth->getUser();
        if (!$user) {
            header('Location: login.php');
            exit;
        }

        $task = $this->taskModel->getById($id);
        if (!$task || (!$this->auth->isAdmin() && $task['user_id'] != $user['id'])) {
            $_SESSION['error'] = 'Task not found or unauthorized';
            header('Location: index.php');
            exit;
        }

        $comments = $this->commentModel->getByTask($id);

        return [
            'task' => $task,
            'comments' => $comments
        ];
    }

    public function addComment($taskId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->auth->getUser();
            if (!$user) {
                header('Location: login.php');
                exit;
            }

            // Check if task belongs to user (unless admin)
            $task = $this->taskModel->getById($taskId);
            if (!$task || (!$this->auth->isAdmin() && $task['user_id'] != $user['id'])) {
                $_SESSION['error'] = 'Task not found or unauthorized';
                header('Location: index.php');
                exit;
            }

            $content = trim($_POST['content']);
            if (empty($content)) {
                $_SESSION['error'] = 'Comment cannot be empty';
                header("Location: show.php?id=$taskId");
                exit;
            }

            if ($this->commentModel->create($taskId, $user['id'], $content)) {
                $_SESSION['success'] = 'Comment added successfully';
            } else {
                $_SESSION['error'] = 'Failed to add comment';
            }
            header("Location: show.php?id=$taskId");
            exit;
        }
    }

    public function assignTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->auth->isAdmin()) {
            $taskId = $_POST['task_id'];
            $assignedTo = $_POST['assigned_to'];
            $user = $this->auth->getUser();

            if ($this->taskModel->assignTask($taskId, $user['id'], $assignedTo)) {
                // Send email notification
                $task = $this->taskModel->getById($taskId);
                $assignedUser = (new User())->getById($assignedTo);
                
                $subject = "New Task Assigned: {$task['title']}";
                $body = "You have been assigned a new task:<br><br>
                         <strong>Title:</strong> {$task['title']}<br>
                         <strong>Description:</strong> {$task['description']}<br>
                         <strong>Due Date:</strong> {$task['due_date']}<br>
                         <strong>Priority:</strong> " . ucfirst($task['priority']) . "<br><br>
                         <a href='http://yourdomain.com/tasks/show.php?id={$task['id']}'>View Task</a>";
                
                $this->mailer->send($assignedUser['email'], $subject, $body);

                $_SESSION['success'] = 'Task assigned successfully';
            } else {
                $_SESSION['error'] = 'Failed to assign task';
            }
            header('Location: index.php');
            exit;
        }
    }

    private function sendStatusChangeEmail($task, $newStatus) {
        $user = (new User())->getById($task['user_id']);
        
        $subject = "Task Status Updated: {$task['title']}";
        $body = "The status of your task has been updated:<br><br>
                 <strong>Title:</strong> {$task['title']}<br>
                 <strong>Previous Status:</strong> " . ucfirst(str_replace('_', ' ', $task['status'])) . "<br>
                 <strong>New Status:</strong> " . ucfirst(str_replace('_', ' ', $newStatus)) . "<br><br>
                 <a href='http://yourdomain.com/tasks/show.php?id={$task['id']}'>View Task</a>";
        
        $this->mailer->send($user['email'], $subject, $body);
    }

    public function sendDailyReminders() {
        $users = (new User())->getAll();
        
        foreach ($users as $user) {
            $dueToday = $this->taskModel->getDueToday($user['id']);
            $pastDue = $this->taskModel->getPastDue($user['id']);
            
            if (!empty($dueToday) || !empty($pastDue)) {
                $subject = "Daily Task Reminder";
                $body = "<h3>Task Reminder</h3>";
                
                if (!empty($dueToday)) {
                    $body .= "<h4>Tasks Due Today:</h4><ul>";
                    foreach ($dueToday as $task) {
                        $body .= "<li><strong>{$task['title']}</strong> (Priority: " . ucfirst($task['priority']) . ")</li>";
                    }
                    $body .= "</ul>";
                }
                
                if (!empty($pastDue)) {
                    $body .= "<h4>Past Due Tasks:</h4><ul>";
                    foreach ($pastDue as $task) {
                        $daysLate = (new DateTime($task['due_date']))->diff(new DateTime())->days;
                        $body .= "<li><strong>{$task['title']}</strong> - {$daysLate} day(s) late</li>";
                    }
                    $body .= "</ul>";
                }
                
                $body .= "<p><a href='http://yourdomain.com'>View All Tasks</a></p>";
                
                $this->mailer->send($user['email'], $subject, $body);
            }
        }
    }
}


// Handling the request based on the 'action' parameter
if (isset($_GET['action'])) {
    $taskController = new TaskController();

    $action = $_GET['action'];

    if (method_exists($taskController, $action)) {
        if($action == "update" || $action == "delete")  {
            $taskController->$action($_GET['id']);
        }
        else {
            $taskController->$action();
        }
    } else {
        echo "Invalid action specified!";
    }
}
// else {
//     echo "No action specified!";
// }  
?>
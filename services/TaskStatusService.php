<?php
// services/TaskStatusService.php
require_once __DIR__.'/../config/Database.php';
require_once __DIR__.'/../config/Mailer.php';

class TaskStatusService {
    private $db;
    private $mailer;

    public function __construct() {
        $this->db = (new Database())->connect();
        $this->mailer = new Mailer();
    }

    public function runDailyUpdate() {
        try {
            $this->updateOverdueTasks();
            $this->sendDailyNotifications();
            return true;
        } catch (Exception $e) {
            error_log("TaskStatusService Error: ".$e->getMessage());
            return false;
        }
    }

    private function updateOverdueTasks() {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE tasks 
                SET status = 'past_due', 
                    last_status_update = :now
                WHERE due_date < :today 
                AND status NOT IN ('completed', 'past_due')
                AND (last_status_update IS NULL OR DATE(last_status_update) < :today)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':today' => $today, ':now' => $now]);
        
        return $stmt->rowCount();
    }

    private function sendDailyNotifications() {
        $today = date('Y-m-d');
        
        // Get all users with due/past-due tasks
        $users = $this->getUsersWithPendingTasks();
        
        foreach ($users as $user) {
            $this->sendUserNotification($user);
        }
    }

    private function getUsersWithPendingTasks() {
        $today = date('Y-m-d');
        
        $sql = "SELECT DISTINCT u.id, u.email, u.name 
                FROM users u
                JOIN tasks t ON u.id = t.user_id
                WHERE (t.due_date = :today OR t.status = 'past_due')
                AND t.status != 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':today' => $today]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function sendUserNotification($user) {
        $tasks = $this->getTasksForNotification($user['id']);
        
        if (!empty($tasks)) {
            $subject = "Task Reminder: ".count($tasks)." pending tasks";
            $body = $this->generateEmailBody($user['name'], $tasks);
            $this->mailer->send($user['email'], $subject, $body);
        }
    }

    private function getTasksForNotification($userId) {
        $today = date('Y-m-d');
        
        $sql = "SELECT id, title, due_date, priority, status
                FROM tasks
                WHERE user_id = :user_id
                AND (due_date = :today OR status = 'past_due')
                AND status != 'completed'
                ORDER BY 
                    CASE WHEN due_date = :today THEN 0 ELSE 1 END,
                    CASE priority 
                        WHEN 'high' THEN 0 
                        WHEN 'medium' THEN 1 
                        ELSE 2 
                    END";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':today' => $today]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // private function generateEmailBody($name, $tasks) {
    //     $dueToday = [];
    //     $pastDue = [];
        
    //     foreach ($tasks as $task) {
    //         if ($task['due_date'] == date('Y-m-d')) {
    //             $dueToday[] = $task;
    //         } else {
    //             $daysLate = (new DateTime($task['due_date']))->diff(new DateTime())->days;
    //             $task['days_late'] = $daysLate;
    //             $pastDue[] = $task;
    //         }
    //     }
        
    //     ob_start();
    //     include __DIR__.'/../views/emails/daily_reminder.php';
    //     return ob_get_clean();
    // }

    private function generateEmailBody($name, $tasks) {
        $dueToday = [];
        $pastDue = [];
        
        foreach ($tasks as $task) {
            if ($task['due_date'] == date('Y-m-d')) {
                $dueToday[] = $task;
            } else {
                $daysLate = (new DateTime($task['due_date']))->diff(new DateTime())->days;
                $task['days_late'] = $daysLate;
                $pastDue[] = $task;
            }
        }
        
        // Extract variables for the template
        $emailData = [
            'name' => $name,
            'dueToday' => $dueToday,
            'pastDue' => $pastDue,
            'appUrl' => APP_URL
        ];
        
        return $this->renderTemplate('daily_reminder.php', $emailData);
    }
    
    private function renderTemplate($template, $data) {
        extract($data); // This makes array keys available as variables
        ob_start();
        include __DIR__.'/../views/emails/'.$template;
        return ob_get_clean();
    }
}
?>
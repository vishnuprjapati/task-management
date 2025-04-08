<?php
// services/NotificationService.php
class NotificationService {
    private $taskModel;
    private $mailer;
    private $userModel;

    public function __construct() {
        $this->taskModel = new Task();
        $this->mailer = new Mailer();
        $this->userModel = new User();
    }

    public function sendDailyTaskReminders() {
        // 1. Update all expired tasks first
        $this->taskModel->updateExpiredTasks();
        
        // 2. Get all users with active tasks
        $users = $this->userModel->getAllUsersWithTasks();
        
        foreach ($users as $user) {
            $dueToday = $this->taskModel->getTasksDueOn($user['id'], date('Y-m-d'));
            $pastDue = $this->taskModel->getPastDueTasks($user['id']);
            
            if (!empty($dueToday) || !empty($pastDue)) {
                $this->sendUserNotification($user, $dueToday, $pastDue);
            }
        }
    }

    private function sendUserNotification($user, $dueToday, $pastDue) {
        $subject = "Daily Task Reminder - " . date('M j, Y');
        
        $html = $this->buildEmailTemplate($user, $dueToday, $pastDue);
        $text = $this->buildTextVersion($user, $dueToday, $pastDue);
        
        $this->mailer->send(
            $user['email'],
            $subject,
            $html,
            $text
        );
    }

    private function buildEmailTemplate($user, $dueToday, $pastDue) {
        ob_start();
        include __DIR__ . '/../views/emails/daily_reminder.php';
        return ob_get_clean();
    }
    
    // ... other methods
}
?>
<?php
require_once  __DIR__ . '/../config/Database.php';

class Task {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($userId, $title, $description, $dueDate, $priority) {
        $stmt = $this->db->prepare("
            INSERT INTO tasks (user_id, title, description, due_date, priority) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $title, $description, $dueDate, $priority]);
    }

    public function update($id, $title, $description, $dueDate, $priority, $status) {
        $stmt = $this->db->prepare("
            UPDATE tasks 
            SET title = ?, description = ?, due_date = ?, priority = ?, status = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$title, $description, $dueDate, $priority, $status, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
        
    }

    public function getAllForUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM tasks ORDER BY due_date");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByStatus($userId, $status) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id = ? AND status = ? ORDER BY due_date");
        $stmt->execute([$userId, $status]);
        return $stmt->fetchAll();
    }

    public function getDueToday($userId) {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT * FROM tasks 
            WHERE user_id = ? AND due_date = ? AND status != 'completed'
            ORDER BY priority DESC
        ");
        $stmt->execute([$userId, $today]);
        return $stmt->fetchAll();
    }

    public function getPastDue($userId) {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT * FROM tasks 
            WHERE user_id = ? AND due_date < ? AND status != 'completed'
            ORDER BY due_date ASC
        ");
        $stmt->execute([$userId, $today]);
        return $stmt->fetchAll();
    }

    public function updateStatusBasedOnDueDate() {
        $today = date('Y-m-d');
        // Update tasks that are past due
        $stmt = $this->db->prepare("
            UPDATE tasks 
            SET status = 'past_due' 
            WHERE due_date < ? AND status NOT IN ('completed', 'past_due')
        ");
        $stmt->execute([$today]);
    }

    public function assignTask($taskId, $assignedBy, $assignedTo) {
        // First check if task exists
        $task = $this->getById($taskId);
        if (!$task) return false;

        // Update task user_id
        $stmt = $this->db->prepare("UPDATE tasks SET user_id = ? WHERE id = ?");
        $stmt->execute([$assignedTo, $taskId]);

        // Record the assignment
        $stmt = $this->db->prepare("
            INSERT INTO task_assignments (task_id, assigned_by, assigned_to) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$taskId, $assignedBy, $assignedTo]);
    }

    public function search($userId, $searchTerm, $status = null, $priority = null) {
        $sql = "SELECT * FROM tasks WHERE user_id = ? AND (title LIKE ? OR description LIKE ?)";
        $params = [$userId, "%$searchTerm%", "%$searchTerm%"];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if ($priority) {
            $sql .= " AND priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY due_date";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // models/Task.php
public function updateExpiredTasks() {
    $now = date('Y-m-d H:i:s');
    
    // Update tasks that are past due
    $stmt = $this->db->prepare("
        UPDATE tasks 
        SET status = 'past_due' 
        WHERE due_date < ? 
        AND status NOT IN ('completed', 'past_due')
    ");
    $stmt->execute([$now]);
    
    return $stmt->rowCount();
}
}
?>
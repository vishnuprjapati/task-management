<?php
require_once  __DIR__ . '/../config/Database.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($taskId, $userId, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (task_id, user_id, content) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$taskId, $userId, $content]);
    }

    public function getByTask($taskId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE task_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
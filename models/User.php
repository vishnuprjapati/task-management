<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // models/User.php
public function getAllUsersWithTasks() {
    $stmt = $this->db->prepare("
        SELECT DISTINCT u.* FROM users u
        JOIN tasks t ON u.id = t.user_id
        WHERE t.status != 'completed'
        AND u.notifications_enabled = 1
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function update($id, $name, $email, $role) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $role, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
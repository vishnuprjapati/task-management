<?php
// In your Database.php config
class Database {
    private $host = 'localhost';
    private $db_name = 'task_manager';
    private $username = 'root';
    private $password = ''; // Use your actual password
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}", 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true // For better performance
                ]
            );
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database server is busy. Please try again later.");
        }
    }
}
?>
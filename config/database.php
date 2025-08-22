<?php
// Database configuration for Joe's Coaches Admin System
class Database {
    private $host = 'localhost';
    private $database_name = 'joes_coaches_admin';
    private $username = 'root';  // Change this to your MySQL username
    private $password = '';      // Use your MySQL password
    private $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            throw new Exception('Database connection error');
        }
        
        return $this->conn;
    }
}
?>

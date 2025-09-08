<?php
/**
 * Database Connection Configuration
 * 
 * This file handles the connection to the XAMPP MySQL database
 */

class Database {
    // Database credentials
    private $host = 'localhost';
    private $db_name = 'student_planner';
    private $username = 'root';
    private $password = ''; // Default XAMPP password is empty
    private $conn;
    
    /**
     * Get the database connection
     * 
     * @return PDO|null Database connection object or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
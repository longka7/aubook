<?php
class Database {
    private $host = "localhost";
    private $db_name = "aubook_db";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        try {
            $socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
            $dsn = "mysql:unix_socket={$socket};dbname={$this->db_name};charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>
<?php
// models/User.php

class User {
    private $conn;
    private $table = "users";

    public $id;
    public $phone;
    public $full_name;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đăng ký người dùng mới
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  SET phone=:phone, full_name=:full_name, password=:password, role=:role";

        $stmt = $this->conn->prepare($query);

        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Đăng nhập
    public function login() {
        $query = "SELECT id, phone, full_name, password, role 
                  FROM " . $this->table . " 
                  WHERE phone = :phone 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    // Kiểm tra số điện thoại đã tồn tại
    public function phoneExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE phone = :phone LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Tìm mẹ bầu theo số điện thoại
    public function findPregnantUserByPhone($phone) {
        $query = "SELECT id, phone, full_name, role 
                  FROM " . $this->table . " 
                  WHERE phone = :phone AND role = 'me_bau' 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $phone = htmlspecialchars(strip_tags($phone));
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug - có thể bỏ sau khi fix
        if(!$result) {
            // Thử tìm bất kỳ user nào với số điện thoại này
            $debug_query = "SELECT id, phone, full_name, role FROM " . $this->table . " WHERE phone = :phone LIMIT 1";
            $debug_stmt = $this->conn->prepare($debug_query);
            $debug_stmt->bindParam(":phone", $phone);
            $debug_stmt->execute();
            $debug_result = $debug_stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Search for pregnant user with phone: " . $phone);
            error_log("Found any user: " . print_r($debug_result, true));
        }
        
        return $result;
    }

    // Lấy thông tin user theo ID
    public function getUserById($id) {
        $query = "SELECT id, phone, full_name, role 
                  FROM " . $this->table . " 
                  WHERE id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<?php
// models/Pregnancy.php

class Pregnancy {
    private $conn;
    private $table = "pregnancy_info";

    public $id;
    public $user_id;
    public $conception_date;
    public $due_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo thông tin thai kỳ
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, conception_date=:conception_date, due_date=:due_date";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":conception_date", $this->conception_date);
        $stmt->bindParam(":due_date", $this->due_date);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Tính ngày dự sinh (cộng 280 ngày từ ngày thụ thai)
    public function calculateDueDate($conception_date) {
        $date = new DateTime($conception_date);
        $date->add(new DateInterval('P280D'));
        return $date->format('Y-m-d');
    }

    // Lấy thông tin thai kỳ theo user_id
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin thai kỳ
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET conception_date=:conception_date, due_date=:due_date 
                  WHERE user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":conception_date", $this->conception_date);
        $stmt->bindParam(":due_date", $this->due_date);

        return $stmt->execute();
    }
}
?>
<?php
// models/FamilyConnection.php

class FamilyConnection {
    private $conn;
    private $table = "family_connections";

    public $id;
    public $family_user_id;
    public $pregnant_user_id;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo kết nối gia đình - mẹ bầu
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET family_user_id=:family_user_id, 
                      pregnant_user_id=:pregnant_user_id, 
                      status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":family_user_id", $this->family_user_id);
        $stmt->bindParam(":pregnant_user_id", $this->pregnant_user_id);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    // Lấy danh sách kết nối của gia đình (chỉ approved)
    public function getConnectionsByFamilyId($family_id) {
        $query = "SELECT fc.*, u.phone, u.full_name, 
                         p.conception_date, p.due_date
                  FROM " . $this->table . " fc
                  INNER JOIN users u ON fc.pregnant_user_id = u.id
                  LEFT JOIN pregnancy_info p ON u.id = p.user_id
                  WHERE fc.family_user_id = :family_id AND fc.status = 'approved'
                  ORDER BY fc.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":family_id", $family_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra kết nối đã tồn tại
    public function connectionExists($family_id, $pregnant_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE family_user_id = :family_id 
                  AND pregnant_user_id = :pregnant_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":family_id", $family_id);
        $stmt->bindParam(":pregnant_id", $pregnant_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>
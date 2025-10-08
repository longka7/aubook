<?php
// models/Notification.php

class Notification {
    private $conn;
    private $table = "notifications";

    public $id;
    public $user_id;
    public $type;
    public $from_user_id;
    public $connection_id;
    public $is_read;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo thông báo mới
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, 
                      type=:type, 
                      from_user_id=:from_user_id, 
                      connection_id=:connection_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":from_user_id", $this->from_user_id);
        $stmt->bindParam(":connection_id", $this->connection_id);

        return $stmt->execute();
    }

    // Lấy thông báo chưa đọc của user
    public function getUnreadNotifications($user_id) {
        $query = "SELECT n.*, u.full_name, u.phone, u.role 
                  FROM " . $this->table . " n
                  INNER JOIN users u ON n.from_user_id = u.id
                  WHERE n.user_id = :user_id AND n.is_read = 0
                  ORDER BY n.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm số thông báo chưa đọc
    public function countUnread($user_id) {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id AND is_read = 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Đánh dấu đã đọc
    public function markAsRead($notification_id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = 1 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $notification_id);

        return $stmt->execute();
    }

    // Đánh dấu tất cả đã đọc
    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = 1 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    // Xóa thông báo theo connection_id
    public function deleteByConnectionId($connection_id) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE connection_id = :connection_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":connection_id", $connection_id);

        return $stmt->execute();
    }
}
?>
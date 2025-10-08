<?php
// controllers/NotificationController.php

session_start();
require_once 'config/database.php';
require_once 'models/Notification.php';
require_once 'models/FamilyConnection.php';
require_once 'models/User.php';

class NotificationController {
    private $db;
    private $notification;
    private $familyConnection;
    private $user;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->notification = new Notification($this->db);
        $this->familyConnection = new FamilyConnection($this->db);
        $this->user = new User($this->db);
    }

    // Trang thông báo
    public function index() {
        $notifications = $this->notification->getUnreadNotifications($_SESSION['user_id']);
        require_once 'views/notifications/index.php';
    }

    // Chấp nhận yêu cầu kết nối
    public function acceptConnection() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $connection_id = $_POST['connection_id'] ?? '';
            $notification_id = $_POST['notification_id'] ?? '';

            if(empty($connection_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            // Cập nhật status kết nối thành approved
            $query = "UPDATE family_connections 
                      SET status = 'approved', updated_at = NOW() 
                      WHERE id = :id AND pregnant_user_id = :pregnant_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $connection_id);
            $stmt->bindParam(":pregnant_id", $_SESSION['user_id']);
            
            if($stmt->execute()) {
                // Đánh dấu thông báo đã đọc
                if(!empty($notification_id)) {
                    $this->notification->markAsRead($notification_id);
                }
                
                // Lấy thông tin gia đình để tạo thông báo ngược lại
                $conn_query = "SELECT family_user_id FROM family_connections WHERE id = :id";
                $conn_stmt = $this->db->prepare($conn_query);
                $conn_stmt->bindParam(":id", $connection_id);
                $conn_stmt->execute();
                $connection = $conn_stmt->fetch(PDO::FETCH_ASSOC);
                
                if($connection) {
                    // Tạo thông báo cho gia đình
                    $this->notification->user_id = $connection['family_user_id'];
                    $this->notification->type = 'connection_approved';
                    $this->notification->from_user_id = $_SESSION['user_id'];
                    $this->notification->connection_id = $connection_id;
                    $this->notification->create();
                }
                
                echo json_encode(['success' => true, 'message' => 'Đã chấp nhận yêu cầu kết nối']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể chấp nhận yêu cầu']);
            }
        }
    }

    // Từ chối yêu cầu kết nối
    public function rejectConnection() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $connection_id = $_POST['connection_id'] ?? '';
            $notification_id = $_POST['notification_id'] ?? '';

            if(empty($connection_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            // Lấy thông tin gia đình trước khi xóa
            $conn_query = "SELECT family_user_id FROM family_connections WHERE id = :id";
            $conn_stmt = $this->db->prepare($conn_query);
            $conn_stmt->bindParam(":id", $connection_id);
            $conn_stmt->execute();
            $connection = $conn_stmt->fetch(PDO::FETCH_ASSOC);

            // Xóa kết nối
            $query = "DELETE FROM family_connections 
                      WHERE id = :id AND pregnant_user_id = :pregnant_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $connection_id);
            $stmt->bindParam(":pregnant_id", $_SESSION['user_id']);
            
            if($stmt->execute()) {
                // Xóa thông báo cũ
                if(!empty($notification_id)) {
                    $this->notification->markAsRead($notification_id);
                }
                
                // Tạo thông báo từ chối cho gia đình
                if($connection) {
                    $this->notification->user_id = $connection['family_user_id'];
                    $this->notification->type = 'connection_rejected';
                    $this->notification->from_user_id = $_SESSION['user_id'];
                    $this->notification->connection_id = null;
                    $this->notification->create();
                }
                
                echo json_encode(['success' => true, 'message' => 'Đã từ chối yêu cầu kết nối']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể từ chối yêu cầu']);
            }
        }
    }

    // Đếm số thông báo chưa đọc (dùng cho badge)
    public function countUnread() {
        $count = $this->notification->countUnread($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
    }
}
?>
<?php
// controllers/FamilyController.php

session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/FamilyConnection.php';
require_once 'models/Pregnancy.php';
require_once 'models/Notification.php';

class FamilyController {
    private $db;
    private $user;
    private $familyConnection;
    private $pregnancy;
    private $notification;

    public function __construct() {
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'gia_dinh') {
            header('Location: index.php');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->familyConnection = new FamilyConnection($this->db);
        $this->pregnancy = new Pregnancy($this->db);
        $this->notification = new Notification($this->db);
    }

    // Trang tìm kiếm mẹ bầu
    public function searchPregnant() {
        require_once 'views/family/search_pregnant.php';
    }

    // Tìm mẹ bầu theo số điện thoại
    public function findPregnant() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = trim($_POST['phone'] ?? ''); // Thêm trim để loại bỏ khoảng trắng

            if(empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số điện thoại']);
                exit;
            }

            $pregnant_user = $this->user->findPregnantUserByPhone($phone);

            if($pregnant_user) {
                echo json_encode([
                    'success' => true,
                    'found' => true,
                    'user' => $pregnant_user
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'found' => false,
                    'message' => 'Mẹ bầu chưa "có mặt" ở đây!'
                ]);
            }
        }
    }

    // Kết nối với mẹ bầu
    public function connectPregnant() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pregnant_user_id = $_POST['pregnant_user_id'] ?? '';

            if(empty($pregnant_user_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            // Kiểm tra đã kết nối chưa
            if($this->familyConnection->connectionExists($_SESSION['user_id'], $pregnant_user_id)) {
                echo json_encode(['success' => false, 'message' => 'Đã gửi yêu cầu kết nối với mẹ bầu này']);
                exit;
            }

            // Tạo kết nối với status = pending
            $this->familyConnection->family_user_id = $_SESSION['user_id'];
            $this->familyConnection->pregnant_user_id = $pregnant_user_id;
            $this->familyConnection->status = 'pending';

            if($this->familyConnection->create()) {
                $connection_id = $this->db->lastInsertId();
                
                // Tạo thông báo cho mẹ bầu
                $this->notification->user_id = $pregnant_user_id;
                $this->notification->type = 'connection_request';
                $this->notification->from_user_id = $_SESSION['user_id'];
                $this->notification->connection_id = $connection_id;
                $this->notification->create();
                
                echo json_encode(['success' => true, 'message' => 'Đã gửi yêu cầu kết nối, vui lòng chờ mẹ bầu chấp nhận']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể kết nối']);
            }
        }
    }

    // Dashboard gia đình
    public function dashboard() {
        $connections = $this->familyConnection->getConnectionsByFamilyId($_SESSION['user_id']);
        require_once 'views/family/dashboard.php';
    }
}
?>
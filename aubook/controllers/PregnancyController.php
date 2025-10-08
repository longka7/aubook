<?php
// controllers/PregnancyController.php

session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Pregnancy.php';

class PregnancyController {
    private $db;
    private $pregnancy;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->pregnancy = new Pregnancy($this->db);
    }

    // Trang nhập thông tin ngày thụ thai
    public function pregnancyInfo() {
        require_once 'views/pregnancy/pregnancy_info.php';
    }

    // Lưu thông tin thai kỳ
    public function savePregnancyInfo() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $conception_date = $_POST['conception_date'] ?? '';

            if(empty($conception_date)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập ngày thụ thai']);
                exit;
            }

            // Tính ngày dự sinh
            $due_date = $this->pregnancy->calculateDueDate($conception_date);

            $this->pregnancy->user_id = $_SESSION['user_id'];
            $this->pregnancy->conception_date = $conception_date;
            $this->pregnancy->due_date = $due_date;

            if($this->pregnancy->create()) {
                echo json_encode([
                    'success' => true, 
                    'due_date' => $due_date,
                    'message' => 'Lưu thông tin thành công'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể lưu thông tin']);
            }
        }
    }

    // Trang dashboard mẹ bầu
    public function dashboard() {
        $pregnancy_info = $this->pregnancy->getByUserId($_SESSION['user_id']);
        require_once 'views/pregnancy/dashboard.php';
    }

    // Trang theo dõi tuần thai
    public function weeklyTracker() {
        $pregnancy_info = $this->pregnancy->getByUserId($_SESSION['user_id']);
        require_once 'views/pregnancy/weekly_tracker.php';
    }
}
?>
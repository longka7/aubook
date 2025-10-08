<?php
// controllers/AuthController.php

session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/OTP.php';
require_once 'models/Pregnancy.php';
require_once 'models/FamilyConnection.php';

class AuthController {
    private $db;
    private $user;
    private $otp;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->otp = new OTP($this->db);
    }

    // Hiển thị trang chủ/đăng nhập
    public function index() {
        // Không tự động redirect, chỉ hiển thị trang chủ
        require_once 'views/auth/index.php';
    }

    // Chọn vai trò
    public function selectRole() {
        require_once 'views/auth/select_role.php';
    }

    // Trang đăng ký
    public function registerForm() {
        $role = $_GET['role'] ?? '';
        if(!in_array($role, ['me_bau', 'gia_dinh'])) {
            header('Location: index.php?action=select_role');
            exit;
        }
        require_once 'views/auth/register.php';
    }

    // Gửi OTP
    public function sendOTP() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_POST['phone'] ?? '';
            
            if(empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số điện thoại']);
                exit;
            }

            $this->otp->phone = $phone;
            $otp_code = $this->otp->generate();

            if($otp_code) {
                // Gửi OTP qua SMS
                $this->otp->sendSMS($otp_code);
                
                // Lưu phone vào session - QUAN TRỌNG
                $_SESSION['register_phone'] = $phone;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Mã OTP đã được gửi',
                    'otp' => $otp_code // Chỉ để demo, production phải bỏ
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể gửi OTP']);
            }
        }
    }

    // Xác thực OTP
    public function verifyOTP() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_SESSION['register_phone'] ?? '';
            $otp_input = $_POST['otp'] ?? '';

            if(empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Phiên làm việc hết hạn, vui lòng thử lại']);
                exit;
            }

            if(empty($otp_input)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã OTP']);
                exit;
            }

            $this->otp->phone = $phone;
            if($this->otp->verify($otp_input)) {
                $_SESSION['otp_verified'] = true;
                echo json_encode(['success' => true, 'message' => 'Xác thực thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Mã OTP không đúng hoặc đã hết hạn']);
            }
        }
    }

    // Đăng ký
    public function register() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(!isset($_SESSION['otp_verified'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng xác thực OTP']);
                exit;
            }

            $phone = $_SESSION['register_phone'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            $this->user->phone = $phone;
            $this->user->full_name = $full_name;
            $this->user->password = $password;
            $this->user->role = $role;

            if($this->user->register()) {
                unset($_SESSION['register_phone']);
                unset($_SESSION['otp_verified']);
                
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['role'] = $role;
                $_SESSION['full_name'] = $full_name;

                echo json_encode(['success' => true, 'role' => $role]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại']);
            }
        }
    }

    // Đăng nhập
    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';

            $this->user->phone = $phone;
            $this->user->password = $password;

            if($this->user->login()) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['role'] = $this->user->role;
                $_SESSION['full_name'] = $this->user->full_name;

                echo json_encode(['success' => true, 'role' => $this->user->role]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Số điện thoại hoặc mật khẩu không đúng']);
            }
        }
    }

    // Đăng xuất
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // Chuyển hướng theo role
    private function redirect() {
        $role = $_SESSION['role'] ?? '';
        if($role == 'me_bau') {
            header('Location: index.php?action=pregnancy_info');
        } else {
            header('Location: index.php?action=family_dashboard');
        }
        exit;
    }
}
?>
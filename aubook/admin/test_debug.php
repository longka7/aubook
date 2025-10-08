<?php
/**
 * File debug để kiểm tra hệ thống admin
 * Truy cập: http://localhost/aubook/admin/test_debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Debug</title>
    <style>
        body {
            font-family: monospace;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        .test-box {
            background: #252526;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #007acc;
        }
        .success {
            border-left-color: #4ec9b0;
        }
        .error {
            border-left-color: #f48771;
        }
        h2 {
            color: #4ec9b0;
        }
        .code {
            background: #1e1e1e;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 ADMIN SYSTEM DEBUG</h1>
    
    <!-- TEST 1: Kiểm tra file tồn tại -->
    <div class="test-box <?php echo file_exists('../config/database.php') ? 'success' : 'error'; ?>">
        <h2>1. Kiểm tra file database.php</h2>
        <?php if (file_exists('../config/database.php')): ?>
            ✅ File tồn tại: <code>../config/database.php</code>
        <?php else: ?>
            ❌ File không tồn tại: <code>../config/database.php</code>
        <?php endif; ?>
    </div>
    
    <!-- TEST 2: Kiểm tra Model Admin -->
    <div class="test-box <?php echo file_exists('../models/Admin.php') ? 'success' : 'error'; ?>">
        <h2>2. Kiểm tra file Admin.php</h2>
        <?php if (file_exists('../models/Admin.php')): ?>
            ✅ File tồn tại: <code>../models/Admin.php</code>
        <?php else: ?>
            ❌ File không tồn tại: <code>../models/Admin.php</code>
        <?php endif; ?>
    </div>
    
    <!-- TEST 3: Kết nối Database -->
    <div class="test-box">
        <h2>3. Test kết nối Database</h2>
        <?php
        try {
            require_once '../config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                echo "✅ Kết nối database thành công!<br>";
                echo "Database: <code>aubook_db</code>";
            } else {
                echo "❌ Không thể kết nối database";
            }
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage();
        }
        ?>
    </div>
    
    <!-- TEST 4: Kiểm tra bảng admins -->
    <div class="test-box">
        <h2>4. Kiểm tra bảng admins</h2>
        <?php
        try {
            $query = "SELECT * FROM admins WHERE username = 'admin' LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ Tìm thấy tài khoản admin!<br><br>";
                echo "<strong>Thông tin:</strong><br>";
                echo "ID: " . $admin['id'] . "<br>";
                echo "Username: " . $admin['username'] . "<br>";
                echo "Email: " . $admin['email'] . "<br>";
                echo "Role: " . $admin['role'] . "<br>";
                echo "Status: " . $admin['status'] . "<br>";
                echo "Password hash: " . substr($admin['password'], 0, 30) . "...<br>";
                echo "Password length: " . strlen($admin['password']) . " ký tự<br>";
            } else {
                echo "❌ Không tìm thấy tài khoản admin";
            }
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage();
        }
        ?>
    </div>
    
    <!-- TEST 5: Test password verify -->
    <div class="test-box">
        <h2>5. Test password_verify</h2>
        <?php
        if (isset($admin)) {
            $test_password = 'admin123';
            $is_valid = password_verify($test_password, $admin['password']);
            
            if ($is_valid) {
                echo "✅ Password 'admin123' ĐÚNG!<br>";
                echo "Password hash trong database hoạt động tốt.";
            } else {
                echo "❌ Password 'admin123' SAI!<br>";
                echo "Password hash trong database không khớp.<br><br>";
                echo "<strong>🔧 Giải pháp:</strong><br>";
                echo "1. Truy cập: <a href='create_password.php' style='color: #4ec9b0;'>create_password.php</a><br>";
                echo "2. Copy password hash mới<br>";
                echo "3. Update vào database";
            }
        }
        ?>
    </div>
    
    <!-- TEST 6: Test Admin Model -->
    <div class="test-box">
        <h2>6. Test Admin Model Login</h2>
        <?php
        try {
            require_once '../models/Admin.php';
            $adminModel = new Admin($db);
            
            echo "✅ Admin Model được load thành công<br><br>";
            
            // Test login
            $result = $adminModel->login('admin', 'admin123');
            
            if ($result) {
                echo "✅✅✅ LOGIN THÀNH CÔNG!<br><br>";
                echo "<strong>Thông tin đăng nhập:</strong><br>";
                echo "ID: " . $result['id'] . "<br>";
                echo "Username: " . $result['username'] . "<br>";
                echo "Full name: " . $result['full_name'] . "<br>";
                echo "Role: " . $result['role'] . "<br><br>";
                echo "<strong>🎉 Hệ thống hoạt động bình thường!</strong><br>";
                echo "Bạn có thể đăng nhập tại: <a href='login.php' style='color: #4ec9b0;'>login.php</a>";
            } else {
                echo "❌ LOGIN THẤT BẠI<br><br>";
                echo "Vấn đề có thể nằm ở:<br>";
                echo "- Password hash không khớp<br>";
                echo "- Logic trong Admin Model có lỗi<br>";
                echo "- Status không phải 'active'";
            }
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage();
        }
        ?>
    </div>
    
    <!-- TEST 7: Thông tin PHP -->
    <div class="test-box">
        <h2>7. Thông tin PHP</h2>
        <?php
        echo "PHP Version: " . phpversion() . "<br>";
        echo "PDO: " . (extension_loaded('pdo') ? '✅ Đã cài' : '❌ Chưa cài') . "<br>";
        echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Đã cài' : '❌ Chưa cài') . "<br>";
        ?>
    </div>
    
    <div class="test-box" style="margin-top: 30px;">
        <h2>📋 TÓM TẮT & GIẢI PHÁP</h2>
        <p>Nếu tất cả test đều PASS (✅) nhưng vẫn không đăng nhập được:</p>
        <ol>
            <li>Xóa cache browser (Ctrl + Shift + Delete)</li>
            <li>Thử trình duyệt ẩn danh (Incognito)</li>
            <li>Kiểm tra session PHP có hoạt động không</li>
            <li>Xem lỗi trong Console của browser (F12)</li>
        </ol>
        
        <p style="margin-top: 20px;">
            <a href="login.php" style="color: #4ec9b0; text-decoration: none; font-weight: bold;">← Quay lại trang đăng nhập</a>
        </p>
    </div>
</body>
</html>
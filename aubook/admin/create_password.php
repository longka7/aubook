<?php
/**
 * File này để tạo password hash mới cho admin
 * Truy cập: http://localhost/aubook/admin/create_password.php
 */

// Password muốn hash
$password = 'admin123';

// Tạo hash
$hash = password_hash($password, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Password Hash</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
        }
        .success-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #4caf50;
        }
        .code-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            overflow-x: auto;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Tạo Password Hash cho Admin</h1>
        
        <div class="info-box">
            <strong>📋 Thông tin:</strong><br>
            Password gốc: <strong><?php echo htmlspecialchars($password); ?></strong>
        </div>
        
        <div class="success-box">
            <strong>✅ Password Hash đã tạo thành công!</strong>
        </div>
        
        <h3>🔑 Password Hash:</h3>
        <div class="code-box">
            <?php echo $hash; ?>
        </div>
        
        <h3>📝 SQL để cập nhật:</h3>
        <div class="code-box">
UPDATE `admins` <br>
SET `password` = '<?php echo $hash; ?>' <br>
WHERE `username` = 'admin';
        </div>
        
        <h3>🧪 Test Password:</h3>
        <?php
        // Test verify
        $test_password = 'admin123';
        $is_valid = password_verify($test_password, $hash);
        ?>
        
        <div class="<?php echo $is_valid ? 'success-box' : 'info-box'; ?>">
            <strong>Test password_verify('admin123', hash):</strong> 
            <?php echo $is_valid ? '✅ PASS' : '❌ FAIL'; ?>
        </div>
        
        <hr>
        
        <h3>🚀 Hướng dẫn sử dụng:</h3>
        <ol>
            <li>Copy password hash ở trên</li>
            <li>Vào phpMyAdmin</li>
            <li>Chọn database <code>aubook_db</code></li>
            <li>Chọn bảng <code>admins</code></li>
            <li>Click <strong>Edit</strong> bản ghi admin</li>
            <li>Paste password hash mới vào trường <code>password</code></li>
            <li>Click <strong>Go</strong></li>
            <li>Thử đăng nhập lại với: <br>
                Username: <strong>admin</strong><br>
                Password: <strong>admin123</strong>
            </li>
        </ol>
        
        <a href="login.php" class="btn">← Quay lại trang đăng nhập</a>
    </div>
</body>
</html>
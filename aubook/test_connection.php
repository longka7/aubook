<?php
/**
 * File test kết nối MySQL cho macOS XAMPP
 * Lưu file này ở thư mục gốc: /aubook/test_connection.php
 * Truy cập: http://localhost/aubook/test_connection.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test MySQL Connection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .success { border-left: 5px solid #4caf50; }
        .error { border-left: 5px solid #f44336; }
        .warning { border-left: 5px solid #ff9800; }
        .info { border-left: 5px solid #2196f3; }
        h2 { margin-top: 0; color: #333; }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 TEST MYSQL CONNECTION - macOS XAMPP</h1>
    
    <!-- Test 1: PDO có cài không -->
    <div class="box <?php echo extension_loaded('pdo') && extension_loaded('pdo_mysql') ? 'success' : 'error'; ?>">
        <h2>1. Kiểm tra PDO Extension</h2>
        <?php if (extension_loaded('pdo') && extension_loaded('pdo_mysql')): ?>
            ✅ PDO: Đã cài<br>
            ✅ PDO MySQL: Đã cài
        <?php else: ?>
            ❌ PDO hoặc PDO MySQL chưa được cài!
        <?php endif; ?>
    </div>
    
    <!-- Test 2: Thử các cách kết nối -->
    <div class="box info">
        <h2>2. Thử các phương thức kết nối</h2>
        <?php
        $configs = [
            [
                'name' => 'Unix Socket (Cách tốt nhất cho macOS XAMPP)',
                'dsn' => 'mysql:unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock;dbname=aubook_db;charset=utf8mb4'
            ],
            [
                'name' => 'Localhost',
                'dsn' => 'mysql:host=localhost;dbname=aubook_db;charset=utf8mb4'
            ],
            [
                'name' => '127.0.0.1',
                'dsn' => 'mysql:host=127.0.0.1;dbname=aubook_db;charset=utf8mb4'
            ],
            [
                'name' => 'Localhost với port 3306',
                'dsn' => 'mysql:host=localhost;port=3306;dbname=aubook_db;charset=utf8mb4'
            ]
        ];
        
        $success_config = null;
        
        foreach ($configs as $config) {
            echo "<strong>{$config['name']}:</strong><br>";
            echo "<code style='font-size: 11px;'>{$config['dsn']}</code><br>";
            
            try {
                $conn = new PDO($config['dsn'], 'root', '');
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                echo "✅ <span style='color: green;'>KẾT NỐI THÀNH CÔNG!</span><br><br>";
                $success_config = $config;
                break; // Tìm được cách kết nối rồi thì dừng
                
            } catch(PDOException $e) {
                echo "❌ <span style='color: red;'>Thất bại: " . $e->getMessage() . "</span><br><br>";
            }
        }
        ?>
    </div>
    
    <?php if ($success_config): ?>
        <!-- Thành công -->
        <div class="box success">
            <h2>✅ KẾT NỐI THÀNH CÔNG!</h2>
            <p><strong>Phương thức hoạt động:</strong> <?php echo $success_config['name']; ?></p>
            <p><strong>DSN:</strong> <code><?php echo $success_config['dsn']; ?></code></p>
            
            <?php
            // Test query
            try {
                $stmt = $conn->query("SELECT VERSION() as version");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p><strong>MySQL Version:</strong> " . $result['version'] . "</p>";
                
                // Kiểm tra database aubook_db
                $stmt = $conn->query("SHOW DATABASES LIKE 'aubook_db'");
                if ($stmt->rowCount() > 0) {
                    echo "<p>✅ Database <code>aubook_db</code> tồn tại</p>";
                    
                    // Kiểm tra bảng admins
                    $stmt = $conn->query("SHOW TABLES FROM aubook_db LIKE 'admins'");
                    if ($stmt->rowCount() > 0) {
                        echo "<p>✅ Bảng <code>admins</code> tồn tại</p>";
                        
                        // Đếm số admin
                        $stmt = $conn->query("SELECT COUNT(*) as count FROM aubook_db.admins");
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo "<p>👤 Số lượng admin: <strong>" . $result['count'] . "</strong></p>";
                    } else {
                        echo "<p>⚠️ Bảng <code>admins</code> chưa tồn tại</p>";
                    }
                } else {
                    echo "<p>⚠️ Database <code>aubook_db</code> chưa tồn tại</p>";
                }
            } catch(PDOException $e) {
                echo "<p>❌ Lỗi query: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <div class="box info">
            <h2>📝 Code cho file config/database.php</h2>
            <p>Copy code này vào file <code>config/database.php</code>:</p>
            <pre><?php
$code = <<<'PHP'
<?php
class Database {
    private $host = "localhost";
    private $db_name = "aubook_db";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        try {
PHP;

if (strpos($success_config['dsn'], 'unix_socket') !== false) {
    $code .= <<<'PHP'

            $socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
            $dsn = "mysql:unix_socket={$socket};dbname={$this->db_name};charset=utf8mb4";
PHP;
} elseif (strpos($success_config['dsn'], '127.0.0.1') !== false) {
    $code .= <<<'PHP'

            $dsn = "mysql:host=127.0.0.1;dbname={$this->db_name};charset=utf8mb4";
PHP;
} elseif (strpos($success_config['dsn'], 'port=3306') !== false) {
    $code .= <<<'PHP'

            $dsn = "mysql:host=localhost;port=3306;dbname={$this->db_name};charset=utf8mb4";
PHP;
} else {
    $code .= <<<'PHP'

            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
PHP;
}

$code .= <<<'PHP'

            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>
PHP;

echo htmlspecialchars($code);
?></pre>
        </div>
        
    <?php else: ?>
        <!-- Thất bại -->
        <div class="box error">
            <h2>❌ KHÔNG THỂ KẾT NỐI MYSQL</h2>
            <p><strong>Nguyên nhân có thể:</strong></p>
            <ol>
                <li><strong>MySQL chưa được khởi động</strong></li>
                <li>Database <code>aubook_db</code> chưa được tạo</li>
                <li>Username/Password không đúng</li>
                <li>Port MySQL không phải 3306</li>
            </ol>
        </div>
        
        <div class="box warning">
            <h2>🔧 CÁCH KHẮC PHỤC</h2>
            
            <h3>1. Kiểm tra MySQL có đang chạy không:</h3>
            <p>Mở XAMPP Control Panel và đảm bảo MySQL đang chạy (nút Start màu xanh)</p>
            <pre>sudo /Applications/XAMPP/xamppfiles/xampp startmysql</pre>
            
            <h3>2. Kiểm tra MySQL từ Terminal:</h3>
            <pre>/Applications/XAMPP/xamppfiles/bin/mysql -u root -p</pre>
            <p>Nhập password (mặc định để trống, nhấn Enter)</p>
            
            <h3>3. Tạo database aubook_db:</h3>
            <pre>CREATE DATABASE aubook_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>
            
            <h3>4. Kiểm tra port MySQL:</h3>
            <p>Mở file: <code>/Applications/XAMPP/xamppfiles/etc/my.cnf</code></p>
            <p>Tìm dòng: <code>port = 3306</code></p>
            
            <h3>5. Restart XAMPP:</h3>
            <pre>sudo /Applications/XAMPP/xamppfiles/xampp restart</pre>
        </div>
    <?php endif; ?>
    
    <div class="box info">
        <h2>📚 Tài liệu tham khảo</h2>
        <ul>
            <li><a href="https://www.apachefriends.org/faq_osx.html" target="_blank">XAMPP macOS FAQ</a></li>
            <li><a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
        </ul>
    </div>
</body>
</html>
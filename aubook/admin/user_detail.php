<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Lấy thông tin admin
$admin_info = $admin->getAdminById($_SESSION['admin_id']);

// Lấy ID user
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id === 0) {
    header('Location: users.php');
    exit();
}

// Lấy thông tin user
$user = $admin->getUserById($user_id);

if (!$user) {
    header('Location: users.php');
    exit();
}

// Lấy các bài đăng của user
$query = "SELECT p.*, 
                 (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
                 (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count
          FROM posts p
          WHERE p.user_id = :user_id
          ORDER BY p.created_at DESC
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nếu là mẹ bầu, lấy danh sách gia đình
$family_connections = [];
if ($user['role'] === 'me_bau') {
    $query = "SELECT fc.*, u.full_name, u.phone, u.status
              FROM family_connections fc
              JOIN users u ON fc.family_user_id = u.id
              WHERE fc.pregnant_user_id = :user_id
              ORDER BY fc.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $family_connections = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Nếu là gia đình, lấy thông tin mẹ bầu
$pregnant_user = null;
if ($user['role'] === 'gia_dinh') {
    $query = "SELECT fc.*, u.full_name, u.phone, u.status, pi.conception_date, pi.due_date
              FROM family_connections fc
              JOIN users u ON fc.pregnant_user_id = u.id
              LEFT JOIN pregnancy_info pi ON u.id = pi.user_id
              WHERE fc.family_user_id = :user_id AND fc.status = 'approved'
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $pregnant_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết User - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .user-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .user-header-content {
            display: flex;
            gap: 30px;
            align-items: start;
        }
        
        .user-avatar-large {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 40px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .user-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .user-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .info-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        
        .info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .post-item {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .post-content {
            color: #374151;
            margin-bottom: 10px;
        }
        
        .post-meta {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #6b7280;
        }
        
        .connection-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .pregnancy-timeline {
            background: linear-gradient(135deg, #fce7f3, #fef3c7);
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .timeline-content {
            flex: 1;
        }
        
        .timeline-label {
            font-size: 12px;
            color: #6b7280;
        }
        
        .timeline-value {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-icon">👶</span>
                    <span class="logo-text">Aubook Admin</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <span class="menu-icon">📊</span>
                    <span class="menu-text">Dashboard</span>
                </a>
                
                <a href="users.php" class="menu-item active">
                    <span class="menu-icon">👥</span>
                    <span class="menu-text">Quản lý User</span>
                </a>
                
                <a href="posts.php" class="menu-item">
                    <span class="menu-icon">📝</span>
                    <span class="menu-text">Bài đăng User</span>
                </a>
                
                <a href="articles.php" class="menu-item">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">Cẩm nang</span>
                </a>
                
                <a href="settings.php" class="menu-item">
                    <span class="menu-icon">⚙️</span>
                    <span class="menu-text">Cài đặt</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($admin_info['full_name'], 0, 1)); ?>
                    </div>
                    <div class="admin-info">
                        <div class="admin-name"><?php echo htmlspecialchars($admin_info['full_name']); ?></div>
                        <div class="admin-role"><?php echo ucfirst($admin_info['role']); ?></div>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout">
                    <span>🚪</span> Đăng xuất
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <a href="users.php" class="back-link">
                ← Quay lại danh sách
            </a>
            
            <!-- User Header -->
            <div class="user-header">
                <div class="user-header-content">
                    <div class="user-avatar-large">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        
                        <div class="user-meta">
                            <div class="user-meta-item">
                                <span>📱</span>
                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <div class="user-meta-item">
                                <span class="badge <?php echo $user['role'] === 'me_bau' ? 'badge-pink' : 'badge-blue'; ?>">
                                    <?php echo $user['role'] === 'me_bau' ? '🤰 Mẹ bầu' : '👨‍👩‍👧 Gia đình'; ?>
                                </span>
                            </div>
                            <div class="user-meta-item">
                                <span class="badge badge-<?php 
                                    echo $user['status'] === 'active' ? 'success' : 
                                         ($user['status'] === 'locked' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">User ID</div>
                                <div class="info-value">#<?php echo $user['id']; ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Ngày đăng ký</div>
                                <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Tổng bài đăng</div>
                                <div class="info-value"><?php echo $user['total_posts']; ?> bài</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Kết nối</div>
                                <div class="info-value"><?php echo $user['total_connections']; ?> người</div>
                            </div>
                        </div>
                        
                        <?php if ($user['status'] === 'locked' && $user['locked_reason']): ?>
                            <div style="margin-top: 15px; padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <strong>⚠️ Lý do khóa:</strong> <?php echo htmlspecialchars($user['locked_reason']); ?>
                                <br>
                                <small>Khóa lúc: <?php echo date('d/m/Y H:i', strtotime($user['locked_at'])); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin thai kỳ (nếu là mẹ bầu) -->
            <?php if ($user['role'] === 'me_bau' && $user['conception_date']): ?>
                <div class="section">
                    <div class="section-title">🤰 Thông tin thai kỳ</div>
                    
                    <div class="pregnancy-timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon">📅</div>
                            <div class="timeline-content">
                                <div class="timeline-label">Ngày thụ thai</div>
                                <div class="timeline-value"><?php echo date('d/m/Y', strtotime($user['conception_date'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon">🎯</div>
                            <div class="timeline-content">
                                <div class="timeline-label">Ngày dự sinh</div>
                                <div class="timeline-value"><?php echo date('d/m/Y', strtotime($user['due_date'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-icon">⏱️</div>
                            <div class="timeline-content">
                                <div class="timeline-label">Tuần thai hiện tại</div>
                                <div class="timeline-value">
                                    <?php 
                                    $conception = new DateTime($user['conception_date']);
                                    $now = new DateTime();
                                    $diff = $conception->diff($now);
                                    $weeks = floor($diff->days / 7);
                                    $days = $diff->days % 7;
                                    echo "Tuần {$weeks} • Ngày {$days}";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (count($family_connections) > 0): ?>
                        <h4 style="margin-top: 20px; margin-bottom: 15px;">👨‍👩‍👧 Gia đình theo dõi (<?php echo count($family_connections); ?>)</h4>
                        <?php foreach ($family_connections as $connection): ?>
                            <div class="connection-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($connection['full_name']); ?></strong>
                                    <span style="color: #6b7280; margin-left: 10px;"><?php echo $connection['phone']; ?></span>
                                </div>
                                <span class="badge badge-<?php echo $connection['status'] === 'approved' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($connection['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Thông tin mẹ bầu (nếu là gia đình) -->
            <?php if ($user['role'] === 'gia_dinh' && $pregnant_user): ?>
                <div class="section">
                    <div class="section-title">🤰 Mẹ bầu đang theo dõi</div>
                    
                    <div class="connection-item">
                        <div>
                            <strong><?php echo htmlspecialchars($pregnant_user['full_name']); ?></strong>
                            <span style="color: #6b7280; margin-left: 10px;"><?php echo $pregnant_user['phone']; ?></span>
                        </div>
                        <a href="user_detail.php?id=<?php echo $pregnant_user['pregnant_user_id']; ?>" class="btn-action btn-view">
                            👁️ Xem chi tiết
                        </a>
                    </div>
                    
                    <?php if ($pregnant_user['conception_date']): ?>
                        <div class="pregnancy-timeline" style="margin-top: 15px;">
                            <div class="timeline-item">
                                <div class="timeline-icon">📅</div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Ngày thụ thai</div>
                                    <div class="timeline-value"><?php echo date('d/m/Y', strtotime($pregnant_user['conception_date'])); ?></div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-icon">🎯</div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Ngày dự sinh</div>
                                    <div class="timeline-value"><?php echo date('d/m/Y', strtotime($pregnant_user['due_date'])); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Bài đăng gần đây -->
            <div class="section">
                <div class="section-title">📝 Bài đăng gần đây (<?php echo count($posts); ?>)</div>
                
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item">
                            <div class="post-content">
                                <?php echo htmlspecialchars($post['content']); ?>
                            </div>
                            <?php if ($post['image_url']): ?>
                                <img src="../<?php echo $post['image_url']; ?>" style="max-width: 200px; border-radius: 8px; margin: 10px 0;">
                            <?php endif; ?>
                            <div class="post-meta">
                                <span>❤️ <?php echo $post['like_count']; ?> lượt thích</span>
                                <span>💬 <?php echo $post['comment_count']; ?> bình luận</span>
                                <span>🕒 <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></span>
                                <span class="badge badge-<?php 
                                    echo $post['status'] === 'approved' ? 'success' : 
                                         ($post['status'] === 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">Chưa có bài đăng nào</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
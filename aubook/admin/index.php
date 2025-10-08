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

// Lấy thống kê
$stats = $admin->getDashboardStats();
$recent_users = $admin->getRecentUsers(5);
$recent_posts = $admin->getRecentPosts(5);
$pending_reports = $admin->getPendingReports(5);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
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
                <a href="index.php" class="menu-item active">
                    <span class="menu-icon">📊</span>
                    <span class="menu-text">Dashboard</span>
                </a>
                
                <a href="users.php" class="menu-item">
                    <span class="menu-icon">👥</span>
                    <span class="menu-text">Quản lý User</span>
                    <?php if ($stats['total_users'] > 0): ?>
                        <span class="menu-badge"><?php echo $stats['total_users']; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="posts.php" class="menu-item">
                    <span class="menu-icon">📝</span>
                    <span class="menu-text">Bài đăng User</span>
                    <?php if ($stats['posts_pending'] > 0): ?>
                        <span class="menu-badge warning"><?php echo $stats['posts_pending']; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="articles.php" class="menu-item">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">Cẩm nang</span>
                    <span class="menu-badge"><?php echo $stats['articles_published']; ?></span>
                </a>
                
                <a href="reports.php" class="menu-item">
                    <span class="menu-icon">⚠️</span>
                    <span class="menu-text">Báo cáo vi phạm</span>
                    <?php if ($stats['reports_pending'] > 0): ?>
                        <span class="menu-badge danger"><?php echo $stats['reports_pending']; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="statistics.php" class="menu-item">
                    <span class="menu-icon">📈</span>
                    <span class="menu-text">Thống kê</span>
                </a>
                
                <a href="notifications.php" class="menu-item">
                    <span class="menu-icon">🔔</span>
                    <span class="menu-text">Thông báo</span>
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
            <header class="content-header">
                <div>
                    <h1>Dashboard</h1>
                    <p>Tổng quan hệ thống Aubook</p>
                </div>
                <div class="header-actions">
                    <button class="btn-icon" title="Làm mới">
                        <span onclick="location.reload()">🔄</span>
                    </button>
                </div>
            </header>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">👥</div>
                    <div class="stat-content">
                        <div class="stat-label">Tổng người dùng</div>
                        <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-trend positive">+<?php echo $stats['new_users_month']; ?> tháng này</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">📝</div>
                    <div class="stat-content">
                        <div class="stat-label">Tổng bài đăng</div>
                        <div class="stat-value"><?php echo number_format($stats['total_posts']); ?></div>
                        <div class="stat-trend positive">+<?php echo $stats['posts_today']; ?> hôm nay</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">🔗</div>
                    <div class="stat-content">
                        <div class="stat-label">Kết nối</div>
                        <div class="stat-value"><?php echo number_format($stats['total_connections']); ?></div>
                        <div class="stat-trend"><?php echo $stats['connections_pending']; ?> chờ duyệt</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon purple">🤰</div>
                    <div class="stat-content">
                        <div class="stat-label">Mẹ bầu</div>
                        <div class="stat-value"><?php echo number_format($stats['total_me_bau']); ?></div>
                        <div class="stat-trend">Gia đình: <?php echo number_format($stats['total_gia_dinh']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Users -->
                <div class="card">
                    <div class="card-header">
                        <h3>Người dùng mới</h3>
                        <a href="users.php" class="btn-sm">Xem tất cả →</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_users) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($recent_users as $user): ?>
                                    <div class="list-item">
                                        <div class="list-item-avatar">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <div class="list-item-content">
                                            <div class="list-item-title"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                            <div class="list-item-subtitle"><?php echo htmlspecialchars($user['phone']); ?></div>
                                        </div>
                                        <div class="list-item-meta">
                                            <span class="badge <?php echo $user['role'] === 'me_bau' ? 'badge-pink' : 'badge-blue'; ?>">
                                                <?php echo $user['role'] === 'me_bau' ? 'Mẹ bầu' : 'Gia đình'; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="empty-state">Chưa có người dùng mới</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Posts -->
                <div class="card">
                    <div class="card-header">
                        <h3>Bài đăng gần đây</h3>
                        <a href="posts.php" class="btn-sm">Xem tất cả →</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_posts) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($recent_posts as $post): ?>
                                    <div class="list-item">
                                        <div class="list-item-content">
                                            <div class="list-item-title"><?php echo htmlspecialchars(mb_substr($post['content'], 0, 50)) . '...'; ?></div>
                                            <div class="list-item-subtitle">
                                                Bởi <?php echo htmlspecialchars($post['full_name']); ?> • 
                                                <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                            </div>
                                        </div>
                                        <div class="list-item-meta">
                                            <span class="badge badge-<?php 
                                                echo $post['status'] === 'approved' ? 'success' : 
                                                     ($post['status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php 
                                                    echo $post['status'] === 'approved' ? 'Đã duyệt' : 
                                                         ($post['status'] === 'pending' ? 'Chờ duyệt' : 'Từ chối'); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="empty-state">Chưa có bài đăng nào</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Pending Reports -->
            <?php if (count($pending_reports) > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>⚠️ Báo cáo chờ xử lý</h3>
                        <a href="reports.php" class="btn-sm">Xem tất cả →</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($pending_reports as $report): ?>
                                <div class="list-item">
                                    <div class="list-item-content">
                                        <div class="list-item-title">
                                            <?php echo htmlspecialchars(mb_substr($report['post_content'], 0, 60)) . '...'; ?>
                                        </div>
                                        <div class="list-item-subtitle">
                                            Báo cáo bởi <?php echo htmlspecialchars($report['reporter_name']); ?> • 
                                            Lý do: <?php echo ucfirst($report['reason']); ?>
                                        </div>
                                    </div>
                                    <div class="list-item-meta">
                                        <a href="reports.php?id=<?php echo $report['id']; ?>" class="btn-sm btn-danger">Xử lý</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
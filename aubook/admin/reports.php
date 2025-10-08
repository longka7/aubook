<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$admin_info = $admin->getAdminById($_SESSION['admin_id']);

// Xử lý actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $report_id = $_POST['report_id'] ?? 0;
    
    if ($action === 'resolve') {
        $status = $_POST['status'] ?? 'reviewed';
        $admin_note = $_POST['admin_note'] ?? '';
        $post_action = $_POST['post_action'] ?? 'none';
        
        $report = $admin->getReportById($report_id);
        
        // Xử lý bài đăng nếu cần
        if ($post_action === 'hide') {
            $admin->hidePost($report['post_id'], $_SESSION['admin_id']);
        } elseif ($post_action === 'delete') {
            $admin->deletePost($report['post_id'], $_SESSION['admin_id']);
            // Gửi thông báo đến user
            $admin->sendNotificationToUser($report['post_author_id'], 'post_deleted', $_SESSION['admin_id'], null, $report['post_id']);
        }
        
        // Cập nhật trạng thái báo cáo
        if ($admin->resolveReport($report_id, $_SESSION['admin_id'], $status, $admin_note)) {
            // Gửi thông báo đến user bị báo cáo
            if ($status === 'resolved') {
                $admin->sendNotificationToUser($report['post_author_id'], 'post_reported', $_SESSION['admin_id'], null, $report['post_id']);
            }
            
            $message = 'Đã xử lý báo cáo thành công!';
            $message_type = 'success';
        }
    }
}

// Lấy tham số lọc
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;

$reports = $admin->getAllReports($status, $page, $per_page);
$total_reports = $admin->getReportsCount($status);
$total_pages = ceil($total_reports / $per_page);

$stats = $admin->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo vi phạm - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .report-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #f59e0b;
        }
        
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .report-info {
            flex: 1;
        }
        
        .report-reason {
            display: inline-block;
            padding: 6px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .report-meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .post-preview {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .post-preview-content {
            color: #374151;
            margin-bottom: 10px;
        }
        
        .post-preview-image {
            max-width: 200px;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        .resolve-form {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        
        .resolve-form.active {
            display: block;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filters form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            align-items: end;
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
                
                <a href="users.php" class="menu-item">
                    <span class="menu-icon">👥</span>
                    <span class="menu-text">Quản lý User</span>
                </a>
                
                <a href="posts.php" class="menu-item">
                    <span class="menu-icon">📝</span>
                    <span class="menu-text">Bài đăng User</span>
                </a>
                
                <a href="reports.php" class="menu-item active">
                    <span class="menu-icon">⚠️</span>
                    <span class="menu-text">Báo cáo vi phạm</span>
                    <?php if ($stats['reports_pending'] > 0): ?>
                        <span class="menu-badge danger"><?php echo $stats['reports_pending']; ?></span>
                    <?php endif; ?>
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
            <header class="content-header">
                <div>
                    <h1>Báo cáo vi phạm</h1>
                    <p>Xử lý các báo cáo vi phạm từ user và admin</p>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <span><?php echo $message_type === 'success' ? '✅' : '❌'; ?></span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Stats Mini -->
            <div class="stats-mini">
                <div class="stat-mini">
                    <div class="stat-mini-label">Tổng báo cáo</div>
                    <div class="stat-mini-value"><?php echo number_format($total_reports); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Chờ xử lý</div>
                    <div class="stat-mini-value" style="color: #ef4444;"><?php echo number_format($stats['reports_pending']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Đã xử lý</div>
                    <div class="stat-mini-value" style="color: #10b981;">
                        <?php echo number_format($total_reports - $stats['reports_pending']); ?>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="reviewed" <?php echo $status === 'reviewed' ? 'selected' : ''; ?>>Đã xem xét</option>
                            <option value="resolved" <?php echo $status === 'resolved' ? 'selected' : ''; ?>>Đã giải quyết</option>
                            <option value="dismissed" <?php echo $status === 'dismissed' ? 'selected' : ''; ?>>Bỏ qua</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-filter">🔍 Lọc</button>
                </form>
            </div>
            
            <!-- Reports List -->
            <?php if (count($reports) > 0): ?>
                <?php foreach ($reports as $report): ?>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-info">
                                <span class="report-reason">
                                    <?php 
                                        $reason_icons = [
                                            'spam' => '🚫 Spam',
                                            'harassment' => '😡 Quấy rối',
                                            'inappropriate' => '🔞 Không phù hợp',
                                            'false_info' => '📰 Thông tin sai',
                                            'other' => '❓ Khác'
                                        ];
                                        echo $reason_icons[$report['reason']] ?? 'Khác';
                                    ?>
                                </span>
                                <div class="report-meta">
                                    <strong>Báo cáo bởi:</strong> 
                                    <?php echo $report['reporter_name'] ? htmlspecialchars($report['reporter_name']) : 'Admin'; ?> 
                                    • <?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?>
                                </div>
                                <div class="report-meta">
                                    <strong>Tác giả bài đăng:</strong> 
                                    <?php echo htmlspecialchars($report['post_author_name']); ?> 
                                    (<?php echo htmlspecialchars($report['post_author_phone']); ?>)
                                </div>
                                <?php if ($report['description']): ?>
                                    <div class="report-meta" style="margin-top: 10px;">
                                        <strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($report['description'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="badge badge-<?php 
                                    echo $report['status'] === 'pending' ? 'warning' : 
                                         ($report['status'] === 'resolved' ? 'success' : 'info'); 
                                ?>">
                                    <?php 
                                        $status_text = [
                                            'pending' => 'Chờ xử lý',
                                            'reviewed' => 'Đã xem',
                                            'resolved' => 'Đã giải quyết',
                                            'dismissed' => 'Bỏ qua'
                                        ];
                                        echo $status_text[$report['status']];
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Post Preview -->
                        <div class="post-preview">
                            <strong>📝 Bài đăng bị báo cáo (ID: #<?php echo $report['post_id']; ?>):</strong>
                            <div class="post-preview-content">
                                <?php echo nl2br(htmlspecialchars($report['post_content'])); ?>
                            </div>
                            <?php if ($report['image_url']): ?>
                                <img src="../<?php echo htmlspecialchars($report['image_url']); ?>" class="post-preview-image" alt="Post image">
                            <?php endif; ?>
                            <div style="margin-top: 10px; font-size: 13px; color: #6b7280;">
                                Trạng thái bài đăng: 
                                <span class="badge badge-<?php 
                                    echo $report['post_status'] === 'approved' ? 'success' : 'warning'; 
                                ?>">
                                    <?php echo ucfirst($report['post_status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($report['status'] === 'pending'): ?>
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button 
                                    class="btn-action btn-view"
                                    onclick="toggleResolveForm(<?php echo $report['id']; ?>)"
                                >
                                    ⚙️ Xử lý báo cáo
                                </button>
                            </div>
                            
                            <!-- Resolve Form -->
                            <div class="resolve-form" id="resolveForm<?php echo $report['id']; ?>">
                                <form method="POST">
                                    <input type="hidden" name="action" value="resolve">
                                    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                    
                                    <div class="form-group" style="margin-bottom: 15px;">
                                        <label><strong>Hành động với bài đăng:</strong></label>
                                        <select name="post_action" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                                            <option value="none">Không làm gì</option>
                                            <option value="hide">Ẩn bài đăng</option>
                                            <option value="delete">Xóa bài đăng (gửi thông báo)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" style="margin-bottom: 15px;">
                                        <label><strong>Trạng thái báo cáo:</strong></label>
                                        <select name="status" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                                            <option value="reviewed">Đã xem xét</option>
                                            <option value="resolved">Đã giải quyết</option>
                                            <option value="dismissed">Bỏ qua</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" style="margin-bottom: 15px;">
                                        <label><strong>Ghi chú của admin:</strong></label>
                                        <textarea 
                                            name="admin_note" 
                                            rows="3" 
                                            style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;"
                                            placeholder="Ghi chú về cách xử lý..."
                                        ></textarea>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <button type="button" onclick="toggleResolveForm(<?php echo $report['id']; ?>)" class="btn-action" style="background: #e5e7eb; color: #374151;">
                                            Hủy
                                        </button>
                                        <button type="submit" class="btn-action btn-unlock">
                                            ✅ Xác nhận xử lý
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <?php if ($report['admin_note']): ?>
                                <div style="background: #e8f5e9; padding: 12px; border-radius: 8px; margin-top: 15px;">
                                    <strong>📝 Ghi chú admin:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($report['admin_note'])); ?>
                                    <div style="margin-top: 8px; font-size: 12px; color: #6b7280;">
                                        Xử lý lúc: <?php echo date('d/m/Y H:i', strtotime($report['reviewed_at'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>">← Trước</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>">Sau →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="empty-state">Không có báo cáo nào</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        function toggleResolveForm(reportId) {
            const form = document.getElementById('resolveForm' + reportId);
            form.classList.toggle('active');
        }
    </script>
</body>
</html>
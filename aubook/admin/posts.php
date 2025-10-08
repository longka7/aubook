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
    $post_id = $_POST['post_id'] ?? 0;
    
    switch ($action) {
        case 'approve':
            if ($admin->approvePost($post_id, $_SESSION['admin_id'])) {
                $message = 'Đã duyệt bài đăng thành công!';
                $message_type = 'success';
            }
            break;
            
        case 'reject':
            $reason = $_POST['reason'] ?? 'Vi phạm quy định';
            if ($admin->rejectPost($post_id, $_SESSION['admin_id'], $reason)) {
                // Gửi thông báo đến user
                $post = $admin->getPostById($post_id);
                // Tạo notification type mới hoặc dùng có sẵn
                $message = 'Đã từ chối bài đăng!';
                $message_type = 'success';
            }
            break;
            
        case 'hide':
            if ($admin->hidePost($post_id, $_SESSION['admin_id'])) {
                $message = 'Đã ẩn bài đăng!';
                $message_type = 'success';
            }
            break;
            
        case 'unhide':
            if ($admin->approvePost($post_id, $_SESSION['admin_id'])) {
                $message = 'Đã hiển thị lại bài đăng!';
                $message_type = 'success';
            }
            break;
            
        case 'delete':
            $post = $admin->getPostById($post_id);
            if ($admin->deletePost($post_id, $_SESSION['admin_id'])) {
                // Gửi thông báo đến user
                $admin->sendNotificationToUser($post['user_id'], 'post_deleted', $_SESSION['admin_id'], null, $post_id);
                $message = 'Đã xóa bài đăng và thông báo đến user!';
                $message_type = 'success';
            }
            break;
            
        case 'report':
            $reason = $_POST['reason'] ?? 'spam';
            $description = $_POST['description'] ?? '';
            $post = $admin->getPostById($post_id);
            
            if ($admin->createReport($post_id, $_SESSION['admin_id'], $reason, $description)) {
                // Gửi thông báo đến user
                $admin->sendNotificationToUser($post['user_id'], 'post_reported', $_SESSION['admin_id'], null, $post_id);
                $message = 'Đã gửi báo cáo vi phạm và thông báo đến user!';
                $message_type = 'success';
            }
            break;
    }
}

// Lấy tham số lọc
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;

$posts = $admin->getAllPosts($search, $status, $page, $per_page);
$total_posts = $admin->getPostsCount($search, $status);
$total_pages = ceil($total_posts / $per_page);

$stats = $admin->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bài đăng - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .post-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        
        .post-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .post-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .author-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }
        
        .author-info h4 {
            margin: 0;
            font-size: 15px;
            color: #1f2937;
        }
        
        .author-info p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }
        
        .post-content {
            margin-bottom: 15px;
            color: #374151;
            line-height: 1.6;
        }
        
        .post-image {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        
        .post-stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .post-actions {
            display: flex;
            gap: 8px;
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
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .report-reason-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }
        
        .reason-option {
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        
        .reason-option:hover {
            border-color: #667eea;
            background: #f3f4f6;
        }
        
        .reason-option.selected {
            border-color: #667eea;
            background: #e0e7ff;
        }
        
        .reason-option input[type="radio"] {
            display: none;
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
                
                <a href="posts.php" class="menu-item active">
                    <span class="menu-icon">📝</span>
                    <span class="menu-text">Bài đăng User</span>
                    <?php if ($stats['posts_pending'] > 0): ?>
                        <span class="menu-badge warning"><?php echo $stats['posts_pending']; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="reports.php" class="menu-item">
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
                    <h1>Quản lý Bài đăng</h1>
                    <p>Kiểm duyệt và quản lý bài đăng của user</p>
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
                    <div class="stat-mini-label">Tổng bài đăng</div>
                    <div class="stat-mini-value"><?php echo number_format($stats['total_posts']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Chờ duyệt</div>
                    <div class="stat-mini-value" style="color: #f59e0b;"><?php echo number_format($stats['posts_pending']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Hôm nay</div>
                    <div class="stat-mini-value" style="color: #10b981;">+<?php echo number_format($stats['posts_today']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Báo cáo chờ xử lý</div>
                    <div class="stat-mini-value" style="color: #ef4444;"><?php echo number_format($stats['reports_pending']); ?></div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="search">Tìm kiếm</label>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            placeholder="Nội dung bài đăng..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                            <option value="hidden" <?php echo $status === 'hidden' ? 'selected' : ''; ?>>Đã ẩn</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-filter">🔍 Lọc</button>
                </form>
            </div>
            
            <!-- Posts List -->
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-author">
                                <div class="author-avatar">
                                    <?php echo strtoupper(substr($post['full_name'], 0, 1)); ?>
                                </div>
                                <div class="author-info">
                                    <h4><?php echo htmlspecialchars($post['full_name']); ?></h4>
                                    <p><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?> • <?php echo htmlspecialchars($post['phone']); ?></p>
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-<?php 
                                    echo $post['status'] === 'approved' ? 'success' : 
                                         ($post['status'] === 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php 
                                        echo $post['status'] === 'approved' ? 'Đã duyệt' : 
                                             ($post['status'] === 'pending' ? 'Chờ duyệt' : 
                                             ($post['status'] === 'hidden' ? 'Đã ẩn' : 'Từ chối')); 
                                    ?>
                                </span>
                                <?php if ($post['report_count'] > 0): ?>
                                    <span class="badge badge-danger" style="margin-left: 5px;">
                                        ⚠️ <?php echo $post['report_count']; ?> báo cáo
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        
                        <?php if ($post['image_url']): ?>
                            <img src="../<?php echo htmlspecialchars($post['image_url']); ?>" class="post-image" alt="Post image">
                        <?php endif; ?>
                        
                        <div class="post-footer">
                            <div class="post-stats">
                                <span>❤️ <?php echo $post['like_count']; ?> lượt thích</span>
                                <span>💬 <?php echo $post['comment_count']; ?> bình luận</span>
                                <span>ID: #<?php echo $post['id']; ?></span>
                            </div>
                            
                            <div class="post-actions">
                                <?php if ($post['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn-action btn-unlock" title="Duyệt bài">
                                            ✅ Duyệt
                                        </button>
                                    </form>
                                    
                                    <button 
                                        class="btn-action btn-lock" 
                                        onclick="openRejectModal(<?php echo $post['id']; ?>)"
                                        title="Từ chối"
                                    >
                                        ❌ Từ chối
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($post['status'] === 'approved' || $post['status'] === 'hidden'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="<?php echo $post['status'] === 'approved' ? 'hide' : 'unhide'; ?>">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn-action btn-lock" title="<?php echo $post['status'] === 'approved' ? 'Ẩn' : 'Hiện'; ?>">
                                            <?php echo $post['status'] === 'approved' ? '👁️‍🗨️ Ẩn' : '👁️ Hiện'; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button 
                                    class="btn-action" 
                                    style="background: #fef3c7; color: #92400e;"
                                    onclick="openReportModal(<?php echo $post['id']; ?>)"
                                    title="Báo cáo vi phạm"
                                >
                                    ⚠️ Báo cáo
                                </button>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button 
                                        type="submit" 
                                        class="btn-action btn-delete"
                                        onclick="return confirm('Bạn có chắc muốn xóa bài đăng này? User sẽ nhận được thông báo.')"
                                        title="Xóa"
                                    >
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                ← Trước
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">
                                Sau →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="empty-state">Không tìm thấy bài đăng nào</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Modal Từ chối bài đăng -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">❌ Từ chối bài đăng</div>
            <div class="modal-body">
                <form id="rejectForm" method="POST">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="post_id" id="rejectPostId">
                    <div class="form-group">
                        <label>Lý do từ chối:</label>
                        <textarea 
                            name="reason" 
                            rows="3" 
                            style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;"
                            placeholder="Nhập lý do từ chối..."
                            required
                        ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeRejectModal()" class="btn-action" style="background: #e5e7eb; color: #374151;">Hủy</button>
                <button onclick="document.getElementById('rejectForm').submit()" class="btn-action btn-delete">❌ Từ chối</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Báo cáo vi phạm -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">⚠️ Báo cáo vi phạm</div>
            <div class="modal-body">
                <form id="reportForm" method="POST">
                    <input type="hidden" name="action" value="report">
                    <input type="hidden" name="post_id" id="reportPostId">
                    
                    <label>Lý do báo cáo:</label>
                    <div class="report-reason-grid">
                        <label class="reason-option">
                            <input type="radio" name="reason" value="spam" required>
                            <div>🚫 Spam</div>
                        </label>
                        <label class="reason-option">
                            <input type="radio" name="reason" value="harassment" required>
                            <div>😡 Quấy rối</div>
                        </label>
                        <label class="reason-option">
                            <input type="radio" name="reason" value="inappropriate" required>
                            <div>🔞 Không phù hợp</div>
                        </label>
                        <label class="reason-option">
                            <input type="radio" name="reason" value="false_info" required>
                            <div>📰 Thông tin sai</div>
                        </label>
                    </div>
                    
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Mô tả chi tiết:</label>
                        <textarea 
                            name="description" 
                            rows="3" 
                            style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;"
                            placeholder="Mô tả chi tiết vi phạm..."
                        ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeReportModal()" class="btn-action" style="background: #e5e7eb; color: #374151;">Hủy</button>
                <button onclick="document.getElementById('reportForm').submit()" class="btn-action" style="background: #f59e0b; color: white;">⚠️ Gửi báo cáo</button>
            </div>
        </div>
    </div>
    
    <script>
        function openRejectModal(postId) {
            document.getElementById('rejectPostId').value = postId;
            document.getElementById('rejectModal').classList.add('active');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }
        
        function openReportModal(postId) {
            document.getElementById('reportPostId').value = postId;
            document.getElementById('reportModal').classList.add('active');
        }
        
        function closeReportModal() {
            document.getElementById('reportModal').classList.remove('active');
        }
        
        // Đóng modal khi click bên ngoài
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
        
        // Highlight lý do được chọn
        document.querySelectorAll('.reason-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.reason-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html>
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

// Xử lý actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;
    
    switch ($action) {
        case 'lock':
            $reason = $_POST['reason'] ?? 'Vi phạm quy định';
            if ($admin->lockUser($user_id, $_SESSION['admin_id'], $reason)) {
                $message = 'Đã khóa tài khoản thành công!';
                $message_type = 'success';
            } else {
                $message = 'Có lỗi xảy ra khi khóa tài khoản!';
                $message_type = 'error';
            }
            break;
            
        case 'unlock':
            if ($admin->unlockUser($user_id, $_SESSION['admin_id'])) {
                $message = 'Đã mở khóa tài khoản thành công!';
                $message_type = 'success';
            } else {
                $message = 'Có lỗi xảy ra khi mở khóa tài khoản!';
                $message_type = 'error';
            }
            break;
            
        case 'delete':
            if ($admin->deleteUser($user_id, $_SESSION['admin_id'])) {
                $message = 'Đã xóa tài khoản thành công!';
                $message_type = 'success';
            } else {
                $message = 'Có lỗi xảy ra khi xóa tài khoản!';
                $message_type = 'error';
            }
            break;
    }
}

// Lấy tham số tìm kiếm và lọc
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;

// Lấy danh sách user
$users = $admin->getAllUsers($search, $role, $status, $page, $per_page);
$total_users = $admin->getUsersCount($search, $role, $status);
$total_pages = ceil($total_users / $per_page);

// Thống kê nhanh
$stats = $admin->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý User - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filters form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-filter {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-filter:hover {
            transform: translateY(-2px);
        }
        
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-mini {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-mini-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .stat-mini-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-view {
            background: #e0e7ff;
            color: #4338ca;
        }
        
        .btn-view:hover {
            background: #c7d2fe;
        }
        
        .btn-lock {
            background: #fef3c7;
            color: #92400e;
        }
        
        .btn-lock:hover {
            background: #fde68a;
        }
        
        .btn-unlock {
            background: #d1fae5;
            color: #065f46;
        }
        
        .btn-unlock:hover {
            background: #a7f3d0;
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-delete:hover {
            background: #fecaca;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-decoration: none;
            color: #374151;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background: #f3f4f6;
            border-color: #667eea;
        }
        
        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
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
        }
        
        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        @media (max-width: 1024px) {
            .filters form {
                grid-template-columns: 1fr;
            }
            
            .stats-mini {
                grid-template-columns: repeat(2, 1fr);
            }
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
                    <span class="menu-badge"><?php echo $stats['total_users']; ?></span>
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
                </a>
                
                <a href="reports.php" class="menu-item">
                    <span class="menu-icon">⚠️</span>
                    <span class="menu-text">Báo cáo vi phạm</span>
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
                    <h1>Quản lý User</h1>
                    <p>Quản lý tài khoản người dùng trong hệ thống</p>
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
                    <div class="stat-mini-label">Tổng user</div>
                    <div class="stat-mini-value"><?php echo number_format($total_users); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Mẹ bầu</div>
                    <div class="stat-mini-value" style="color: #ec4899;"><?php echo number_format($stats['total_me_bau']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Gia đình</div>
                    <div class="stat-mini-value" style="color: #3b82f6;"><?php echo number_format($stats['total_gia_dinh']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Mới tháng này</div>
                    <div class="stat-mini-value" style="color: #10b981;">+<?php echo number_format($stats['new_users_month']); ?></div>
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
                            placeholder="Tên hoặc số điện thoại..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Vai trò</label>
                        <select id="role" name="role">
                            <option value="">Tất cả</option>
                            <option value="me_bau" <?php echo $role === 'me_bau' ? 'selected' : ''; ?>>Mẹ bầu</option>
                            <option value="gia_dinh" <?php echo $role === 'gia_dinh' ? 'selected' : ''; ?>>Gia đình</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="locked" <?php echo $status === 'locked' ? 'selected' : ''; ?>>Locked</option>
                            <option value="deleted" <?php echo $status === 'deleted' ? 'selected' : ''; ?>>Deleted</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-filter">🔍 Lọc</button>
                </form>
            </div>
            
            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Danh sách User (<?php echo number_format($total_users); ?>)</h3>
                    <span>Trang <?php echo $page; ?>/<?php echo $total_pages; ?></span>
                </div>
                <div class="card-body">
                    <?php if (count($users) > 0): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Số điện thoại</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>#<?php echo $user['id']; ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div class="user-avatar">
                                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                    </div>
                                                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $user['role'] === 'me_bau' ? 'badge-pink' : 'badge-blue'; ?>">
                                                    <?php echo $user['role'] === 'me_bau' ? '🤰 Mẹ bầu' : '👨‍👩‍👧 Gia đình'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $user['status'] === 'active' ? 'success' : 
                                                         ($user['status'] === 'locked' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="actions">
                                                    <a href="user_detail.php?id=<?php echo $user['id']; ?>" class="btn-action btn-view" title="Xem chi tiết">
                                                        👁️ Xem
                                                    </a>
                                                    
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <button 
                                                            class="btn-action btn-lock" 
                                                            onclick="openLockModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')"
                                                            title="Khóa tài khoản"
                                                        >
                                                            🔒 Khóa
                                                        </button>
                                                    <?php elseif ($user['status'] === 'locked'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="unlock">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <button 
                                                                type="submit" 
                                                                class="btn-action btn-unlock"
                                                                onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?')"
                                                                title="Mở khóa"
                                                            >
                                                                🔓 Mở khóa
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($user['status'] !== 'deleted'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <button 
                                                                type="submit" 
                                                                class="btn-action btn-delete"
                                                                onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')"
                                                                title="Xóa"
                                                            >
                                                                🗑️ Xóa
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>&status=<?php echo $status; ?>">
                                        ← Trước
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>&status=<?php echo $status; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role; ?>&status=<?php echo $status; ?>">
                                        Sau →
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="empty-state">Không tìm thấy user nào</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal Khóa tài khoản -->
    <div id="lockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                🔒 Khóa tài khoản
            </div>
            <div class="modal-body">
                <p>Bạn có chắc muốn khóa tài khoản: <strong id="lockUserName"></strong>?</p>
                <form id="lockForm" method="POST">
                    <input type="hidden" name="action" value="lock">
                    <input type="hidden" name="user_id" id="lockUserId">
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="lockReason">Lý do khóa:</label>
                        <textarea 
                            id="lockReason" 
                            name="reason" 
                            rows="3" 
                            style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;"
                            placeholder="Nhập lý do khóa tài khoản..."
                            required
                        ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeLockModal()" class="btn-action" style="background: #e5e7eb; color: #374151;">
                    Hủy
                </button>
                <button onclick="document.getElementById('lockForm').submit()" class="btn-action btn-lock">
                    🔒 Khóa tài khoản
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function openLockModal(userId, userName) {
            document.getElementById('lockUserId').value = userId;
            document.getElementById('lockUserName').textContent = userName;
            document.getElementById('lockModal').classList.add('active');
        }
        
        function closeLockModal() {
            document.getElementById('lockModal').classList.remove('active');
            document.getElementById('lockReason').value = '';
        }
        
        // Đóng modal khi click bên ngoài
        document.getElementById('lockModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLockModal();
            }
        });
    </script>
</body>
</html>
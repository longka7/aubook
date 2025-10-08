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

$message = '';
$message_type = '';

// Xử lý actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $article_id = $_POST['article_id'] ?? 0;
    
    if ($action === 'delete' && $article_id) {
        if ($admin->deleteArticle($article_id, $_SESSION['admin_id'])) {
            $message = 'Xóa bài viết thành công!';
            $message_type = 'success';
        }
    }
}

// Lấy tham số lọc
$search = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$status = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;

$articles = $admin->getAllArticles($search, $category_id, $status, $page, $per_page);
$total_articles = $admin->getArticlesCount($search, $category_id, $status);
$total_pages = ceil($total_articles / $per_page);

$categories = $admin->getAllCategories();
$stats = $admin->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Cẩm nang - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .article-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
            transition: all 0.2s;
        }
        
        .article-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .article-thumbnail {
            width: 200px;
            height: 140px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .article-thumbnail-placeholder {
            width: 200px;
            height: 140px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            flex-shrink: 0;
        }
        
        .article-content {
            flex: 1;
        }
        
        .article-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .article-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .article-summary {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        
        .article-meta {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }
        
        .article-actions {
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
            grid-template-columns: 2fr 1fr 1fr auto;
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
                
                <a href="articles.php" class="menu-item active">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">Cẩm nang</span>
                    <span class="menu-badge"><?php echo $stats['articles_published']; ?></span>
                </a>
                
                <a href="categories.php" class="menu-item">
                    <span class="menu-icon">📂</span>
                    <span class="menu-text">Danh mục</span>
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
                    <h1>Quản lý Cẩm nang</h1>
                    <p>Tạo và quản lý bài viết hướng dẫn</p>
                </div>
                <a href="article_form.php" class="btn-primary">
                    ➕ Tạo bài viết mới
                </a>
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
                    <div class="stat-mini-label">Tổng bài viết</div>
                    <div class="stat-mini-value"><?php echo number_format($total_articles); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Đã xuất bản</div>
                    <div class="stat-mini-value" style="color: #10b981;"><?php echo number_format($stats['articles_published']); ?></div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-label">Danh mục</div>
                    <div class="stat-mini-value" style="color: #667eea;"><?php echo count($categories); ?></div>
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
                            placeholder="Tiêu đề hoặc nội dung..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Danh mục</label>
                        <select id="category_id" name="category_id">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Nháp</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                            <option value="unpublished" <?php echo $status === 'unpublished' ? 'selected' : ''; ?>>Đã ẩn</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-filter">🔍 Lọc</button>
                </form>
            </div>
            
            <!-- Articles List -->
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <?php if ($article['thumbnail']): ?>
                            <img src="../<?php echo htmlspecialchars($article['thumbnail']); ?>" class="article-thumbnail" alt="Thumbnail">
                        <?php else: ?>
                            <div class="article-thumbnail-placeholder">
                                📚
                            </div>
                        <?php endif; ?>
                        
                        <div class="article-content">
                            <div class="article-header">
                                <div style="flex: 1;">
                                    <div class="article-title"><?php echo htmlspecialchars($article['title']); ?></div>
                                    <div class="article-meta">
                                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background-color: <?php echo $article['category_color']; ?>20; color: <?php echo $article['category_color']; ?>">
                                            <?php echo htmlspecialchars($article['category_name']); ?>
                                        </span>
                                        <span>✍️ <?php echo htmlspecialchars($article['author_name']); ?></span>
                                        <span>📅 <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></span>
                                        <span>👁️ <?php echo number_format($article['view_count']); ?> lượt xem</span>
                                        <?php if ($article['is_featured']): ?>
                                            <span>⭐ Nổi bật</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="badge badge-<?php 
                                    echo $article['status'] === 'published' ? 'success' : 
                                         ($article['status'] === 'draft' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php 
                                        echo $article['status'] === 'published' ? 'Đã xuất bản' : 
                                             ($article['status'] === 'draft' ? 'Nháp' : 'Đã ẩn'); 
                                    ?>
                                </span>
                            </div>
                            
                            <?php if ($article['summary']): ?>
                                <div class="article-summary">
                                    <?php echo htmlspecialchars($article['summary']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="article-actions">
                                <a href="../views/cam_nang_detail.php?id=<?php echo $article['id']; ?>" class="btn-action btn-view" target="_blank" title="Xem trước">
                                    👁️ Xem
                                </a>
                                
                                <a href="article_form.php?id=<?php echo $article['id']; ?>" class="btn-action" style="background: #dbeafe; color: #1e40af;" title="Chỉnh sửa">
                                    ✏️ Sửa
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                    <button 
                                        type="submit" 
                                        class="btn-action btn-delete"
                                        onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')"
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
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">
                                ← Trước
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">
                                Sau →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="empty-state">Chưa có bài viết nào. <a href="article_form.php">Tạo bài viết mới →</a></p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
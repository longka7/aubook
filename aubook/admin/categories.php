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
    
    switch ($action) {
        case 'create':
            $name = $_POST['name'] ?? '';
            $slug = $admin->generateSlug($_POST['slug'] ?? $name);
            $description = $_POST['description'] ?? '';
            $icon = $_POST['icon'] ?? '📚';
            $color = $_POST['color'] ?? '#667eea';
            $display_order = $_POST['display_order'] ?? 0;
            
            if ($admin->createCategory($name, $slug, $description, $icon, $color, $display_order)) {
                $message = 'Tạo danh mục thành công!';
                $message_type = 'success';
            } else {
                $message = 'Có lỗi xảy ra!';
                $message_type = 'error';
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $slug = $admin->generateSlug($_POST['slug'] ?? $name);
            $description = $_POST['description'] ?? '';
            $icon = $_POST['icon'] ?? '📚';
            $color = $_POST['color'] ?? '#667eea';
            $display_order = $_POST['display_order'] ?? 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if ($admin->updateCategory($id, $name, $slug, $description, $icon, $color, $display_order, $is_active)) {
                $message = 'Cập nhật danh mục thành công!';
                $message_type = 'success';
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($admin->deleteCategory($id)) {
                $message = 'Xóa danh mục thành công!';
                $message_type = 'success';
            }
            break;
    }
}

$categories = $admin->getAllCategories();
$stats = $admin->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s;
            border-left: 4px solid;
        }
        
        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .category-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .category-icon {
            font-size: 36px;
        }
        
        .category-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .category-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .category-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        
        .category-stats {
            font-size: 13px;
            color: #6b7280;
        }
        
        .category-actions {
            display: flex;
            gap: 8px;
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
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .color-picker {
            width: 100%;
            height: 40px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
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
                
                <a href="articles.php" class="menu-item">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">Cẩm nang</span>
                </a>
                
                <a href="categories.php" class="menu-item active">
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
                    <h1>Quản lý Danh mục Cẩm nang</h1>
                    <p>Tổ chức và phân loại bài viết</p>
                </div>
                <button class="btn-primary" onclick="openCreateModal()">
                    ➕ Tạo danh mục mới
                </button>
            </header>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <span><?php echo $message_type === 'success' ? '✅' : '❌'; ?></span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Categories Grid -->
            <div class="category-grid">
                <?php foreach ($categories as $cat): ?>
                    <div class="category-card" style="border-left-color: <?php echo htmlspecialchars($cat['color']); ?>">
                        <div class="category-header">
                            <div class="category-icon"><?php echo $cat['icon']; ?></div>
                            <div>
                                <div class="category-title"><?php echo htmlspecialchars($cat['name']); ?></div>
                                <div style="font-size: 12px; color: #6b7280;">
                                    <?php echo $cat['is_active'] ? '✅ Active' : '❌ Inactive'; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="category-description">
                            <?php echo htmlspecialchars($cat['description']); ?>
                        </div>
                        
                        <div class="category-meta">
                            <div class="category-stats">
                                📝 <?php echo $cat['article_count']; ?> bài viết<br>
                                📊 Thứ tự: <?php echo $cat['display_order']; ?>
                            </div>
                            <div class="category-actions">
                                <button 
                                    class="btn-action btn-view" 
                                    onclick='openEditModal(<?php echo json_encode($cat); ?>)'
                                    title="Chỉnh sửa"
                                >
                                    ✏️
                                </button>
                                <?php if ($cat['article_count'] == 0): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                        <button 
                                            type="submit" 
                                            class="btn-action btn-delete"
                                            onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')"
                                            title="Xóa"
                                        >
                                            🗑️
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    
    <!-- Modal Tạo/Sửa danh mục -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalTitle">Tạo danh mục mới</div>
            <div class="modal-body">
                <form id="categoryForm" method="POST">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="categoryId">
                    
                    <div class="form-group">
                        <label>Tên danh mục *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="categoryName"
                            required
                            placeholder="VD: Thai kỳ"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Slug (URL)</label>
                        <input 
                            type="text" 
                            name="slug" 
                            id="categorySlug"
                            placeholder="VD: thai-ky (để trống để tự động tạo)"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea 
                            name="description" 
                            id="categoryDescription"
                            rows="3"
                            placeholder="Mô tả ngắn về danh mục"
                        ></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Icon (Emoji)</label>
                            <input 
                                type="text" 
                                name="icon" 
                                id="categoryIcon"
                                placeholder="📚"
                                maxlength="2"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label>Màu sắc</label>
                            <input 
                                type="color" 
                                name="color" 
                                id="categoryColor"
                                value="#667eea"
                                class="color-picker"
                            >
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Thứ tự hiển thị</label>
                            <input 
                                type="number" 
                                name="display_order" 
                                id="categoryOrder"
                                value="0"
                                min="0"
                            >
                        </div>
                        
                        <div class="form-group" id="activeGroup" style="display: none;">
                            <label style="display: flex; align-items: center; gap: 8px; margin-top: 28px;">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    id="categoryActive"
                                    checked
                                >
                                <span>Kích hoạt</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeCategoryModal()" class="btn-action" style="background: #e5e7eb; color: #374151;">
                    Hủy
                </button>
                <button onclick="document.getElementById('categoryForm').submit()" class="btn-primary">
                    💾 Lưu
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Tạo danh mục mới';
            document.getElementById('formAction').value = 'create';
            document.getElementById('categoryForm').reset();
            document.getElementById('activeGroup').style.display = 'none';
            document.getElementById('categoryModal').classList.add('active');
        }
        
        function openEditModal(category) {
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
            document.getElementById('formAction').value = 'update';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categorySlug').value = category.slug;
            document.getElementById('categoryDescription').value = category.description || '';
            document.getElementById('categoryIcon').value = category.icon;
            document.getElementById('categoryColor').value = category.color;
            document.getElementById('categoryOrder').value = category.display_order;
            document.getElementById('categoryActive').checked = category.is_active == 1;
            document.getElementById('activeGroup').style.display = 'block';
            document.getElementById('categoryModal').classList.add('active');
        }
        
        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.remove('active');
        }
        
        // Đóng modal khi click bên ngoài
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });
    </script>
</body>
</html>
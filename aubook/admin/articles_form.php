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
$article = null;
$is_edit = false;

// Kiểm tra edit mode
if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
    $article = $admin->getArticleById($article_id);
    if ($article) {
        $is_edit = true;
    }
}

// Xử lý upload ảnh
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
    $upload_dir = '../uploads/articles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $new_filename = 'article_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
        $thumbnail = 'uploads/articles/' . $new_filename;
    }
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_article'])) {
    $data = [
        'category_id' => $_POST['category_id'] ?? 0,
        'title' => $_POST['title'] ?? '',
        'slug' => !empty($_POST['slug']) ? $admin->generateSlug($_POST['slug']) : $admin->generateSlug($_POST['title']),
        'summary' => $_POST['summary'] ?? '',
        'content' => $_POST['content'] ?? '',
        'thumbnail' => $thumbnail ?? $_POST['existing_thumbnail'] ?? null,
        'author_id' => $_SESSION['admin_id'],
        'status' => $_POST['status'] ?? 'draft',
        'published_at' => ($_POST['status'] === 'published') ? date('Y-m-d H:i:s') : null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'meta_title' => $_POST['meta_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? ''
    ];
    
    if ($is_edit) {
        if ($admin->updateArticle($article_id, $data)) {
            $message = 'Cập nhật bài viết thành công!';
            $message_type = 'success';
            $article = $admin->getArticleById($article_id);
        }
    } else {
        $new_id = $admin->createArticle($data);
        if ($new_id) {
            header('Location: article_form.php?id=' . $new_id . '&success=1');
            exit();
        }
    }
}

$categories = $admin->getAllCategories();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Chỉnh sửa' : 'Tạo mới'; ?> bài viết - Aubook Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <style>
        .form-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .thumbnail-preview {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            margin-top: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .btn-save {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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
                
                <a href="articles.php" class="menu-item active">
                    <span class="menu-icon">📚</span>
                    <span class="menu-text">Cẩm nang</span>
                </a>
                
                <a href="categories.php" class="menu-item">
                    <span class="menu-icon">📂</span>
                    <span class="menu-text">Danh mục</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="btn-logout">
                    <span>🚪</span> Đăng xuất
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <div>
                    <h1><?php echo $is_edit ? '✏️ Chỉnh sửa bài viết' : '➕ Tạo bài viết mới'; ?></h1>
                    <p>Viết bài hướng dẫn cho mẹ bầu</p>
                </div>
                <a href="articles.php" class="btn-action" style="background: #e5e7eb; color: #374151;">
                    ← Quay lại
                </a>
            </header>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <span><?php echo $message_type === 'success' ? '✅' : '❌'; ?></span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="message success">
                    <span>✅</span>
                    <span>Tạo bài viết thành công!</span>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="form-container">
                <!-- Thông tin cơ bản -->
                <div class="form-card">
                    <div class="form-section-title">📝 Thông tin cơ bản</div>
                    
                    <div class="form-group">
                        <label>Tiêu đề bài viết *</label>
                        <input 
                            type="text" 
                            name="title" 
                            placeholder="VD: 10 điều cần biết trong thai kỳ"
                            value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Slug (URL)</label>
                            <input 
                                type="text" 
                                name="slug" 
                                placeholder="VD: 10-dieu-can-biet-trong-thai-ky"
                                value="<?php echo htmlspecialchars($article['slug'] ?? ''); ?>"
                            >
                            <small style="color: #6b7280;">Để trống để tự động tạo từ tiêu đề</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Danh mục *</label>
                            <select name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option 
                                        value="<?php echo $cat['id']; ?>"
                                        <?php echo (isset($article['category_id']) && $article['category_id'] == $cat['id']) ? 'selected' : ''; ?>
                                    >
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Tóm tắt</label>
                        <textarea 
                            name="summary" 
                            rows="3"
                            placeholder="Tóm tắt ngắn gọn nội dung bài viết (hiển thị trong danh sách)"
                        ><?php echo htmlspecialchars($article['summary'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Nội dung -->
                <div class="form-card">
                    <div class="form-section-title">📄 Nội dung bài viết</div>
                    
                    <div class="form-group">
                        <textarea 
                            id="content" 
                            name="content"
                        ><?php echo $article['content'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Hình ảnh -->
                <div class="form-card">
                    <div class="form-section-title">🖼️ Hình ảnh đại diện</div>
                    
                    <div class="form-group">
                        <label>Upload hình ảnh</label>
                        <input type="file" name="thumbnail" accept="image/*" onchange="previewImage(this)">
                        <?php if (isset($article['thumbnail'])): ?>
                            <input type="hidden" name="existing_thumbnail" value="<?php echo htmlspecialchars($article['thumbnail']); ?>">
                            <img src="../<?php echo htmlspecialchars($article['thumbnail']); ?>" class="thumbnail-preview" id="preview">
                        <?php else: ?>
                            <img id="preview" class="thumbnail-preview" style="display: none;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Cài đặt -->
                <div class="form-card">
                    <div class="form-section-title">⚙️ Cài đặt</div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status">
                                <option value="draft" <?php echo (isset($article['status']) && $article['status'] === 'draft') ? 'selected' : ''; ?>>Nháp</option>
                                <option value="published" <?php echo (isset($article['status']) && $article['status'] === 'published') ? 'selected' : ''; ?>>Xuất bản</option>
                                <option value="unpublished" <?php echo (isset($article['status']) && $article['status'] === 'unpublished') ? 'selected' : ''; ?>>Ẩn</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; margin-top: 28px;">
                                <input 
                                    type="checkbox" 
                                    name="is_featured"
                                    <?php echo (isset($article['is_featured']) && $article['is_featured']) ? 'checked' : ''; ?>
                                >
                                <span>⭐ Đánh dấu nổi bật</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Meta Title (SEO)</label>
                        <input 
                            type="text" 
                            name="meta_title"
                            placeholder="Tiêu đề hiển thị trên Google"
                            value="<?php echo htmlspecialchars($article['meta_title'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Meta Description (SEO)</label>
                        <textarea 
                            name="meta_description" 
                            rows="2"
                            placeholder="Mô tả hiển thị trên Google (160 ký tự)"
                        ><?php echo htmlspecialchars($article['meta_description'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="articles.php" class="btn-action" style="background: #e5e7eb; color: #374151;">
                        Hủy
                    </a>
                    <button type="submit" name="save_article" class="btn-save">
                        💾 <?php echo $is_edit ? 'Cập nhật' : 'Lưu'; ?> bài viết
                    </button>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 600,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | image media link | code fullscreen | help',
            content_style: 'body { font-family:Arial,sans-serif; font-size:16px; line-height:1.7; max-width:800px; margin:0 auto; } img { max-width: 100%; height: auto; }',
            
            // Upload ảnh
            images_upload_url: 'upload_image.php',
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'upload_image.php');
                    
                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };
                    
                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }
                        
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }
                        
                        const json = JSON.parse(xhr.responseText);
                        
                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        
                        resolve(json.location);
                    };
                    
                    xhr.onerror = () => {
                        reject('Image upload failed');
                    };
                    
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    
                    xhr.send(formData);
                });
            },
            
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            
            // Cấu hình thêm
            paste_data_images: true,
            image_advtab: true,
            image_caption: true
        });
        
        // Preview image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gia Đình - Aubook</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8F9FA;
            color: #333;
            line-height: 1.6;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            padding: 24px 20px;
        }

        .header-content {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            font-size: 1.5rem;
        }

        .btn-logout {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .dashboard-main {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .actions-bar {
            margin-bottom: 24px;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            text-align: center;
        }

        .connections-list h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .connection-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #F0F0F0;
        }

        .user-info {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFE8ED 0%, #FFD4D4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .user-details h3 {
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .user-details .phone {
            color: #666;
            font-size: 0.9rem;
        }

        .badge {
            padding: 6px 16px;
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-item .label {
            color: #666;
            font-size: 0.85rem;
        }

        .info-item .value {
            color: #333;
            font-weight: 600;
            font-size: 1rem;
        }

        .info-item .value.highlight {
            color: #FF7B9C;
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            background: #E0E0E0;
            border-radius: 6px;
            overflow: hidden;
            margin: 16px 0 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF7B9C 0%, #FFA8B8 100%);
            border-radius: 6px;
        }

        .progress-text {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .no-info {
            color: #666;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #333;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="index.php?action=newsfeed" style="padding: 8px 16px; background: rgba(255, 255, 255, 0.2); color: white; text-decoration: none; border-radius: 8px; font-size: 1.2rem;">📰</a>
                <a href="index.php?action=notifications" style="padding: 8px 16px; background: rgba(255, 255, 255, 0.2); color: white; text-decoration: none; border-radius: 8px; font-size: 1.2rem;">🔔</a>
                <a href="index.php?action=logout" class="btn-logout">Đăng xuất</a>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="actions-bar">
            <a href="index.php?action=search_pregnant" class="btn-primary">
                + Kết nối với mẹ bầu
            </a>
        </div>

        <?php if(!empty($connections)): ?>
        <div class="connections-list">
            <h2>Danh sách theo dõi</h2>
            
            <?php foreach($connections as $connection): ?>
            <div class="card">
                <div class="connection-header">
                    <div class="user-info">
                        <div class="avatar">👤</div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($connection['full_name']); ?></h3>
                            <p class="phone"><?php echo htmlspecialchars($connection['phone']); ?></p>
                        </div>
                    </div>
                    <span class="badge">Mẹ bầu</span>
                </div>
                
                <?php if($connection['conception_date']): ?>
                <div class="pregnancy-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Ngày thụ thai</span>
                            <span class="value"><?php echo date('d/m/Y', strtotime($connection['conception_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Ngày dự sinh</span>
                            <span class="value highlight"><?php echo date('d/m/Y', strtotime($connection['due_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Tuần thai</span>
                            <span class="value">
                                <?php 
                                    $conception = new DateTime($connection['conception_date']);
                                    $now = new DateTime();
                                    $diff = $conception->diff($now);
                                    $weeks = floor($diff->days / 7);
                                    $days = $diff->days % 7;
                                    echo $weeks . ' tuần ' . $days . ' ngày';
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Còn lại</span>
                            <span class="value">
                                <?php 
                                    $due = new DateTime($connection['due_date']);
                                    $remaining = $now->diff($due);
                                    echo $remaining->days . ' ngày';
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php 
                            $total_days = 280;
                            $passed_days = $diff->days;
                            $percentage = min(100, ($passed_days / $total_days) * 100);
                            echo $percentage;
                        ?>%"></div>
                    </div>
                    <p class="progress-text"><?php echo round($percentage); ?>% hoàn thành</p>
                </div>
                <?php else: ?>
                <p class="no-info">Chưa có thông tin thai kỳ</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📱</div>
            <h3>Chưa có kết nối nào</h3>
            <p>Hãy tìm kiếm và kết nối với mẹ bầu để theo dõi hành trình thai kỳ</p>
            <a href="index.php?action=search_pregnant" class="btn-primary">
                Tìm kiếm mẹ bầu
            </a>
        </div>
        <?php endif; ?>

        <!-- Form đăng bài mới -->
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px;">Chia sẻ khoảnh khắc</h3>
            <form id="createPostForm" enctype="multipart/form-data">
                <textarea name="content" placeholder="Bạn đang nghĩ gì?" style="width: 100%; min-height: 80px; padding: 12px; border: 2px solid #E0E0E0; border-radius: 10px; font-family: inherit; resize: vertical; margin-bottom: 10px;"></textarea>
                <div style="position: relative; display: inline-block; width: 100%;">
                    <img id="imagePreview" style="max-width: 100%; border-radius: 10px; margin-bottom: 10px; display: none;" alt="Preview">
                    <button type="button" onclick="removeImage()" id="removeImageBtn" style="display: none; position: absolute; top: 10px; right: 10px; background: rgba(255, 59, 59, 0.9); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 1.2rem; line-height: 1;">×</button>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="padding: 8px 16px; background: #F5F5F5; border-radius: 8px; cursor: pointer;">
                        📷 Thêm ảnh
                        <input type="file" id="imageInput" name="image" accept=".png,.jpg,.jpeg" style="display: none;" onchange="previewImage(event)">
                    </label>
                    <button type="submit" style="padding: 10px 30px; background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Đăng</button>
                </div>
            </form>
        </div>

        <?php 
        // Load bài đăng của user
        require_once 'models/Post.php';
        $database = new Database();
        $db = $database->getConnection();
        $postModel = new Post($db);
        $myPosts = $postModel->getPostsByUserId($_SESSION['user_id'], 10);
        
        if(!empty($myPosts)): 
        ?>
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px;">Bài đăng của bạn</h3>
            <?php foreach($myPosts as $post): ?>
            <div style="padding: 15px; background: #FFF5F7; border-radius: 10px; margin-bottom: 15px;" id="post-<?php echo $post['id']; ?>">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div style="color: #666; font-size: 0.85rem;">
                        <?php 
                            $created = new DateTime($post['created_at']);
                            $now = new DateTime();
                            $diff_time = $now->diff($created);
                            if($diff_time->days > 0) echo $diff_time->days . ' ngày trước';
                            elseif($diff_time->h > 0) echo $diff_time->h . ' giờ trước';
                            elseif($diff_time->i > 0) echo $diff_time->i . ' phút trước';
                            else echo 'Vừa xong';
                        ?>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button onclick="editPost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars(str_replace(["\r", "\n"], ['\\r', '\\n'], $post['content']), ENT_QUOTES); ?>')" style="padding: 5px 10px; background: #FF7B9C; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.85rem;">✏️ Sửa</button>
                        <button onclick="deletePost(<?php echo $post['id']; ?>)" style="padding: 5px 10px; background: #F44336; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.85rem;">🗑️ Xóa</button>
                    </div>
                </div>
                <div id="post-content-<?php echo $post['id']; ?>"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                <textarea id="post-edit-<?php echo $post['id']; ?>" style="display: none; width: 100%; min-height: 80px; padding: 10px; border: 2px solid #FF7B9C; border-radius: 8px; margin-bottom: 10px;"></textarea>
                <div id="post-edit-actions-<?php echo $post['id']; ?>" style="display: none; gap: 10px; margin-bottom: 10px;">
                    <button onclick="saveEdit(<?php echo $post['id']; ?>)" style="padding: 8px 16px; background: #FF7B9C; color: white; border: none; border-radius: 5px; cursor: pointer;">Lưu</button>
                    <button onclick="cancelEdit(<?php echo $post['id']; ?>)" style="padding: 8px 16px; background: #E0E0E0; color: #666; border: none; border-radius: 5px; cursor: pointer;">Hủy</button>
                </div>
                <?php if($post['image_url']): ?>
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="max-width: 100%; border-radius: 8px; margin-bottom: 10px;" alt="Post image">
                <?php endif; ?>
                <div style="display: flex; gap: 15px; color: #666; font-size: 0.9rem;">
                    <span>❤️ <?php echo $post['like_count']; ?> thích</span>
                    <span>💬 <?php echo $post['comment_count']; ?> bình luận</span>
                    <span>🔗 <?php echo $post['share_count']; ?> chia sẻ</span>
                </div>
            </div>
            <?php endforeach; ?>
            <a href="index.php?action=newsfeed" style="display: block; text-align: center; color: #FF7B9C; text-decoration: none; font-weight: 600;">Xem tất cả bài đăng →</a>
        </div>
        <?php endif; ?>

        <div class="actions-bar">
            <a href="index.php?action=search_pregnant" class="btn-primary">
                + Kết nối với mẹ bầu
            </a>
        </div>

        <?php if(!empty($connections)): ?>
        <div class="connections-list">
            <h2>Danh sách theo dõi</h2>
            
            <?php foreach($connections as $connection): ?>
            <div class="card">
                <div class="connection-header">
                    <div class="user-info">
                        <div class="avatar">👤</div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($connection['full_name']); ?></h3>
                            <p class="phone"><?php echo htmlspecialchars($connection['phone']); ?></p>
                        </div>
                    </div>
                    <span class="badge">Mẹ bầu</span>
                </div>
                
                <?php if($connection['conception_date']): ?>
                <div class="pregnancy-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Ngày thụ thai</span>
                            <span class="value"><?php echo date('d/m/Y', strtotime($connection['conception_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Ngày dự sinh</span>
                            <span class="value highlight"><?php echo date('d/m/Y', strtotime($connection['due_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Tuần thai</span>
                            <span class="value">
                                <?php 
                                    $conception = new DateTime($connection['conception_date']);
                                    $now = new DateTime();
                                    $diff = $conception->diff($now);
                                    $weeks = floor($diff->days / 7);
                                    $days = $diff->days % 7;
                                    echo $weeks . ' tuần ' . $days . ' ngày';
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Còn lại</span>
                            <span class="value">
                                <?php 
                                    $due = new DateTime($connection['due_date']);
                                    $remaining = $now->diff($due);
                                    echo $remaining->days . ' ngày';
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php 
                            $total_days = 280;
                            $passed_days = $diff->days;
                            $percentage = min(100, ($passed_days / $total_days) * 100);
                            echo $percentage;
                        ?>%"></div>
                    </div>
                    <p class="progress-text"><?php echo round($percentage); ?>% hoàn thành</p>
                </div>
                <?php else: ?>
                <p class="no-info">Chưa có thông tin thai kỳ</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📱</div>
            <h3>Chưa có kết nối nào</h3>
            <p>Hãy tìm kiếm và kết nối với mẹ bầu để theo dõi hành trình thai kỳ</p>
            <a href="index.php?action=search_pregnant" class="btn-primary">
                Tìm kiếm mẹ bầu
            </a>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Preview ảnh trước khi đăng
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            const file = event.target.files[0];
            
            if(file) {
                // Kiểm tra loại file
                const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if(!allowedTypes.includes(file.type)) {
                    alert('Chỉ chấp nhận file .png hoặc .jpg');
                    event.target.value = '';
                    return;
                }
                
                // Kiểm tra kích thước (5MB)
                if(file.size > 5 * 1024 * 1024) {
                    alert('File ảnh không được vượt quá 5MB');
                    event.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    removeBtn.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        // Xóa ảnh preview
        function removeImage() {
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('removeImageBtn').style.display = 'none';
            document.getElementById('imageInput').value = '';
        }

        // Xử lý đăng bài
        document.getElementById('createPostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('index.php?action=create_post', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch(error) {
                alert('Có lỗi xảy ra');
            }
        });
    </script>
</body>
</html>
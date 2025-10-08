<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mẹ Bầu - Aubook</title>
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

        .btn-notification {
            position: relative;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #FF3B3B;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .dashboard-main {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .card h2,
        .card h3 {
            margin-bottom: 16px;
            color: #333;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #F0F0F0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: #666;
            font-size: 0.95rem;
        }

        .info-row .value {
            color: #333;
            font-weight: 600;
        }

        .info-row .value.highlight {
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
            transition: width 0.5s ease;
        }

        .progress-text {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .share-link-box {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        .share-link-box input {
            flex: 1;
            padding: 12px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .btn-copy {
            padding: 12px 20px;
            background: #FF7B9C;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h1>
            <div style="display: flex; align-items: center;">
                <a href="index.php?action=newsfeed" class="btn-notification">📰</a>
                <a href="index.php?action=notifications" class="btn-notification">
                    🔔
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                </a>
                <a href="index.php?action=logout" class="btn-logout">Đăng xuất</a>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <?php if($pregnancy_info): ?>
        <div class="card">
            <h2>Thông tin Thai kỳ</h2>
            <div class="info-row">
                <span class="label">Ngày thụ thai:</span>
                <span class="value"><?php echo date('d/m/Y', strtotime($pregnancy_info['conception_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Ngày dự sinh:</span>
                <span class="value highlight"><?php echo date('d/m/Y', strtotime($pregnancy_info['due_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Tuần thai:</span>
                <span class="value">
                    <?php 
                        $conception = new DateTime($pregnancy_info['conception_date']);
                        $now = new DateTime();
                        $diff = $conception->diff($now);
                        $weeks = floor($diff->days / 7);
                        $days = $diff->days % 7;
                        echo $weeks . ' tuần ' . $days . ' ngày';
                    ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Còn lại:</span>
                <span class="value">
                    <?php 
                        $due = new DateTime($pregnancy_info['due_date']);
                        $remaining = $now->diff($due);
                        echo $remaining->days . ' ngày';
                    ?>
                </span>
            </div>
        </div>

        <a href="index.php?action=weekly_tracker" style="display: block; text-decoration: none; margin-bottom: 20px;">
            <div class="card" style="background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%); color: white; text-align: center; cursor: pointer;">
                <h3 style="color: white;">📊 Xem theo dõi tuần thai chi tiết</h3>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Theo dõi sự phát triển của bé từng tuần</p>
            </div>
        </a>

        <div class="card">
            <h3>Tiến trình thai kỳ</h3>
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

        <div class="card">
            <h3>💡 Lời khuyên hôm nay</h3>
            <p>Hãy uống đủ nước, nghỉ ngơi đầy đủ và tập thể dục nhẹ nhàng. Đừng quên bổ sung vitamin cho mẹ và bé!</p>
        </div>

        <div class="card">
            <h3>Chia sẻ với người thân</h3>
            <p>Mời người thân theo dõi hành trình thai kỳ cùng bạn</p>
            <div class="share-link-box">
                <input type="text" id="shareLink" value="tenapp.com/<?php echo $_SESSION['user_id']; ?>" readonly>
                <button class="btn-copy" onclick="copyShareLink()">Sao chép</button>
            </div>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <p>Chưa có thông tin thai kỳ</p>
            <a href="index.php?action=pregnancy_info" class="btn-primary">Nhập thông tin</a>
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

        <?php if($pregnancy_info): ?>
        <div class="card">
            <h2>Thông tin Thai kỳ</h2>
            <div class="info-row">
                <span class="label">Ngày thụ thai:</span>
                <span class="value"><?php echo date('d/m/Y', strtotime($pregnancy_info['conception_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Ngày dự sinh:</span>
                <span class="value highlight"><?php echo date('d/m/Y', strtotime($pregnancy_info['due_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Tuần thai:</span>
                <span class="value">
                    <?php 
                        $conception = new DateTime($pregnancy_info['conception_date']);
                        $now = new DateTime();
                        $diff = $conception->diff($now);
                        $weeks = floor($diff->days / 7);
                        $days = $diff->days % 7;
                        echo $weeks . ' tuần ' . $days . ' ngày';
                    ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Còn lại:</span>
                <span class="value">
                    <?php 
                        $due = new DateTime($pregnancy_info['due_date']);
                        $remaining = $now->diff($due);
                        echo $remaining->days . ' ngày';
                    ?>
                </span>
            </div>
        </div>

        <a href="index.php?action=weekly_tracker" style="display: block; text-decoration: none; margin-bottom: 20px;">
            <div class="card" style="background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%); color: white; text-align: center; cursor: pointer;">
                <h3 style="color: white;">📊 Xem theo dõi tuần thai chi tiết</h3>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Theo dõi sự phát triển của bé từng tuần</p>
            </div>
        </a>

        <div class="card">
            <h2>Thông tin Thai kỳ</h2>
            <div class="info-row">
                <span class="label">Ngày thụ thai:</span>
                <span class="value"><?php echo date('d/m/Y', strtotime($pregnancy_info['conception_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Ngày dự sinh:</span>
                <span class="value highlight"><?php echo date('d/m/Y', strtotime($pregnancy_info['due_date'])); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Tuần thai:</span>
                <span class="value">
                    <?php 
                        $conception = new DateTime($pregnancy_info['conception_date']);
                        $now = new DateTime();
                        $diff = $conception->diff($now);
                        $weeks = floor($diff->days / 7);
                        $days = $diff->days % 7;
                        echo $weeks . ' tuần ' . $days . ' ngày';
                    ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Còn lại:</span>
                <span class="value">
                    <?php 
                        $due = new DateTime($pregnancy_info['due_date']);
                        $remaining = $now->diff($due);
                        echo $remaining->days . ' ngày';
                    ?>
                </span>
            </div>
        </div>

        <div class="card">
            <h3>Tiến trình thai kỳ</h3>
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

        <div class="card">
            <h3>💡 Lời khuyên hôm nay</h3>
            <p>Hãy uống đủ nước, nghỉ ngơi đầy đủ và tập thể dục nhẹ nhàng. Đừng quên bổ sung vitamin cho mẹ và bé!</p>
        </div>

        <div class="card">
            <h3>Chia sẻ với người thân</h3>
            <p>Mời người thân theo dõi hành trình thai kỳ cùng bạn</p>
            <div class="share-link-box">
                <input type="text" id="shareLink" value="tenapp.com/<?php echo $_SESSION['user_id']; ?>" readonly>
                <button class="btn-copy" onclick="copyShareLink()">Sao chép</button>
            </div>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <p>Chưa có thông tin thai kỳ</p>
            <a href="index.php?action=pregnancy_info" class="btn-primary">Nhập thông tin</a>
        </div>
        <?php endif; ?>
    </main>

    <script>
        function copyShareLink() {
            const linkInput = document.getElementById('shareLink');
            linkInput.select();
            document.execCommand('copy');
            alert('Đã sao chép link!');
        }

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

        // Sửa bài đăng
        function editPost(postId, content) {
            document.getElementById('post-content-' + postId).style.display = 'none';
            document.getElementById('post-edit-' + postId).style.display = 'block';
            document.getElementById('post-edit-' + postId).value = content.replace(/\\r\\n|\\n/g, '\n');
            document.getElementById('post-edit-actions-' + postId).style.display = 'flex';
        }

        // Hủy sửa
        function cancelEdit(postId) {
            document.getElementById('post-content-' + postId).style.display = 'block';
            document.getElementById('post-edit-' + postId).style.display = 'none';
            document.getElementById('post-edit-actions-' + postId).style.display = 'none';
        }

        // Lưu chỉnh sửa
        async function saveEdit(postId) {
            const content = document.getElementById('post-edit-' + postId).value;
            
            if(!content.trim()) {
                alert('Nội dung không được để trống');
                return;
            }
            
            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('content', content);
            
            try {
                const response = await fetch('index.php?action=update_post', {
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
        }

        // Xóa bài đăng
        async function deletePost(postId) {
            if(!confirm('Bạn có chắc muốn xóa bài viết này?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('post_id', postId);
            
            try {
                const response = await fetch('index.php?action=delete_post', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    document.getElementById('post-' + postId).remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch(error) {
                alert('Có lỗi xảy ra');
            }
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

        // Load số lượng thông báo chưa đọc
        async function loadNotificationCount() {
            try {
                const response = await fetch('index.php?action=count_unread_notifications');
                const data = await response.json();
                
                if(data.count > 0) {
                    document.getElementById('notificationBadge').textContent = data.count;
                    document.getElementById('notificationBadge').style.display = 'flex';
                }
            } catch(error) {
                console.error('Không thể tải thông báo');
            }
        }

        // Load khi trang vừa tải xong
        loadNotificationCount();

        // Tự động load lại mỗi 30 giây
        setInterval(loadNotificationCount, 30000);
    </script>
</body>
</html>
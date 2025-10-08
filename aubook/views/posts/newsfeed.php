<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - Aubook</title>
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

        .header {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            padding: 24px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-icon {
            position: relative;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.2rem;
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
            display: none;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Form tạo bài đăng */
        .create-post-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .create-post-form textarea {
            width: 100%;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
        }

        .create-post-form textarea:focus {
            outline: none;
            border-color: #FF7B9C;
        }

        .create-post-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .btn-upload-image {
            padding: 8px 16px;
            background: #F5F5F5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-post {
            padding: 10px 30px;
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .image-preview {
            margin-top: 10px;
            max-width: 100%;
            border-radius: 10px;
            display: none;
        }

        /* Post card */
        .post-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .post-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFE8ED 0%, #FFD4D4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 12px;
        }

        .post-author-info h3 {
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .post-author-info p {
            font-size: 0.85rem;
            color: #999;
        }

        .post-content {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .post-image {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .post-stats {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #F0F0F0;
            color: #666;
            font-size: 0.9rem;
        }

        .post-actions {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }

        .post-action-btn {
            flex: 1;
            padding: 10px;
            background: none;
            border: none;
            color: #666;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .post-action-btn:hover {
            color: #FF7B9C;
        }

        .post-action-btn.liked {
            color: #FF7B9C;
            font-weight: 600;
        }

        /* Comments section */
        .comments-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #F0F0F0;
        }

        .comment {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #FFE8ED;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .comment-content {
            flex: 1;
            background: #F5F5F5;
            padding: 10px 12px;
            border-radius: 10px;
        }

        .comment-author {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .comment-text {
            font-size: 0.9rem;
        }

        .comment-form {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .comment-input {
            flex: 1;
            padding: 10px;
            border: 2px solid #E0E0E0;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .comment-input:focus {
            outline: none;
            border-color: #FF7B9C;
        }

        .btn-send-comment {
            padding: 10px 20px;
            background: #FF7B9C;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>Tin tức</h1>
            <div class="header-actions">
                <a href="index.php?action=notifications" class="btn-icon">
                    🔔
                    <span class="notification-badge" id="notificationBadge">0</span>
                </a>
                <a href="index.php?action=<?php echo $_SESSION['role'] == 'me_bau' ? 'pregnancy_dashboard' : 'family_dashboard'; ?>" class="btn-icon">🏠</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Form tạo bài đăng -->
        <div class="create-post-card">
            <form id="createPostForm" enctype="multipart/form-data">
                <textarea name="content" placeholder="Bạn đang nghĩ gì?" required></textarea>
                <img id="imagePreview" class="image-preview" alt="Preview">
                <div class="create-post-actions">
                    <label class="btn-upload-image">
                        📷 Thêm ảnh
                        <input type="file" name="image" accept=".png,.jpg,.jpeg" style="display: none;" onchange="previewImage(event)">
                    </label>
                    <button type="submit" class="btn-post">Đăng</button>
                </div>
            </form>
        </div>

        <!-- Danh sách bài đăng -->
        <div id="postsList">
            <?php if(!empty($posts)): ?>
                <?php foreach($posts as $post): ?>
                <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                    <div class="post-header">
                        <div class="post-avatar">
                            <?php echo $post['role'] == 'me_bau' ? '👶' : '👨‍👩‍👧'; ?>
                        </div>
                        <div class="post-author-info">
                            <h3><?php echo htmlspecialchars($post['full_name']); ?></h3>
                            <p><?php 
                                $created = new DateTime($post['created_at']);
                                $now = new DateTime();
                                $diff = $now->diff($created);
                                if($diff->days > 0) echo $diff->days . ' ngày trước';
                                elseif($diff->h > 0) echo $diff->h . ' giờ trước';
                                elseif($diff->i > 0) echo $diff->i . ' phút trước';
                                else echo 'Vừa xong';
                            ?></p>
                        </div>
                    </div>

                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>

                    <?php if($post['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" class="post-image" alt="Post image">
                    <?php endif; ?>

                    <div class="post-stats">
                        <span><?php echo $post['like_count']; ?> lượt thích</span>
                        <span><?php echo $post['comment_count']; ?> bình luận • <?php echo $post['share_count']; ?> chia sẻ</span>
                    </div>

                    <div class="post-actions">
                        <button class="post-action-btn <?php echo $post['user_liked'] > 0 ? 'liked' : ''; ?>" onclick="toggleLike(<?php echo $post['id']; ?>, this)">
                            ❤️ Thích
                        </button>
                        <button class="post-action-btn" onclick="showComments(<?php echo $post['id']; ?>)">
                            💬 Bình luận
                        </button>
                        <button class="post-action-btn" onclick="sharePost(<?php echo $post['id']; ?>)">
                            🔗 Chia sẻ
                        </button>
                    </div>

                    <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                        <div class="comments-list" id="comments-list-<?php echo $post['id']; ?>"></div>
                        <div class="comment-form">
                            <input type="text" class="comment-input" placeholder="Viết bình luận..." id="comment-input-<?php echo $post['id']; ?>">
                            <button class="btn-send-comment" onclick="addComment(<?php echo $post['id']; ?>)">Gửi</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-state">
                <h3>Chưa có bài đăng nào</h3>
                <p>Hãy là người đầu tiên chia sẻ khoảnh khắc của bạn!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Preview ảnh trước khi đăng
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        // Tạo bài đăng
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

        // Toggle like
        async function toggleLike(postId, button) {
            const isLiked = button.classList.contains('liked');
            const action = isLiked ? 'unlike_post' : 'like_post';
            
            const formData = new FormData();
            formData.append('post_id', postId);
            
            try {
                const response = await fetch('index.php?action=' + action, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    button.classList.toggle('liked');
                    location.reload();
                }
            } catch(error) {
                console.error('Lỗi:', error);
            }
        }

        // Hiển thị comments
        async function showComments(postId) {
            const commentsSection = document.getElementById('comments-' + postId);
            
            if(commentsSection.style.display === 'none') {
                commentsSection.style.display = 'block';
                await loadComments(postId);
            } else {
                commentsSection.style.display = 'none';
            }
        }

        // Load comments
        async function loadComments(postId) {
            try {
                const response = await fetch('index.php?action=get_comments&post_id=' + postId);
                const data = await response.json();
                
                if(data.success) {
                    const commentsList = document.getElementById('comments-list-' + postId);
                    commentsList.innerHTML = '';
                    
                    data.comments.forEach(comment => {
                        const commentHtml = `
                            <div class="comment">
                                <div class="comment-avatar">${comment.role == 'me_bau' ? '👶' : '👨‍👩‍👧'}</div>
                                <div class="comment-content">
                                    <div class="comment-author">${comment.full_name}</div>
                                    <div class="comment-text">${comment.content}</div>
                                </div>
                            </div>
                        `;
                        commentsList.innerHTML += commentHtml;
                    });
                }
            } catch(error) {
                console.error('Lỗi:', error);
            }
        }

        // Thêm comment
        async function addComment(postId) {
            const input = document.getElementById('comment-input-' + postId);
            const content = input.value.trim();
            
            if(!content) return;
            
            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('content', content);
            
            try {
                const response = await fetch('index.php?action=add_comment', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    input.value = '';
                    await loadComments(postId);
                    location.reload();
                }
            } catch(error) {
                console.error('Lỗi:', error);
            }
        }

        // Chia sẻ
        async function sharePost(postId) {
            const formData = new FormData();
            formData.append('post_id', postId);
            
            try {
                const response = await fetch('index.php?action=share_post', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    alert(data.message);
                    location.reload();
                }
            } catch(error) {
                console.error('Lỗi:', error);
            }
        }

        // Load thông báo
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

        loadNotificationCount();
        setInterval(loadNotificationCount, 30000);
    </script>
</body>
</html>
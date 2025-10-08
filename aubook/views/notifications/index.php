<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo - Aubook</title>
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

        .btn-back {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .notification-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .notification-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFE8ED 0%, #FFD4D4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .notification-info h3 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 4px;
        }

        .notification-info p {
            font-size: 0.9rem;
            color: #666;
        }

        .notification-message {
            background: #FFF5F7;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #666;
            font-size: 0.95rem;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-accept {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
        }

        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 123, 156, 0.3);
        }

        .btn-reject {
            background: #F5F5F5;
            color: #666;
        }

        .btn-reject:hover {
            background: #E0E0E0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #999;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>Thông báo</h1>
            <a href="index.php?action=pregnancy_dashboard" class="btn-back">← Quay lại</a>
        </div>
    </header>

    <div class="container">
        <?php if(!empty($notifications)): ?>
            <?php foreach($notifications as $notif): ?>
            <div class="notification-card">
                <div class="notification-header">
                    <div class="notification-icon">👨‍👩‍👧</div>
                    <div class="notification-info">
                        <h3><?php echo htmlspecialchars($notif['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($notif['phone']); ?></p>
                    </div>
                </div>

                <?php if($notif['type'] == 'connection_request'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> muốn kết nối và theo dõi hành trình thai kỳ của bạn.
                </div>

                <div class="notification-actions">
                    <button class="btn btn-accept" onclick="acceptConnection(<?php echo $notif['connection_id']; ?>, <?php echo $notif['id']; ?>)">
                        Chấp nhận
                    </button>
                    <button class="btn btn-reject" onclick="rejectConnection(<?php echo $notif['connection_id']; ?>, <?php echo $notif['id']; ?>)">
                        Từ chối
                    </button>
                </div>
                
                <?php elseif($notif['type'] == 'post_like'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> đã thích bài viết của bạn.
                </div>
                
                <?php elseif($notif['type'] == 'post_comment'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> đã bình luận về bài viết của bạn.
                </div>
                
                <?php elseif($notif['type'] == 'post_share'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> đã chia sẻ bài viết của bạn.
                </div>
                
                <?php elseif($notif['type'] == 'connection_approved'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> đã chấp nhận yêu cầu kết nối của bạn.
                </div>
                
                <?php elseif($notif['type'] == 'connection_rejected'): ?>
                <div class="notification-message">
                    <strong><?php echo htmlspecialchars($notif['full_name']); ?></strong> đã từ chối yêu cầu kết nối của bạn.
                </div>
                <?php endif; ?>

                <div class="notification-time">
                    <?php 
                        $created = new DateTime($notif['created_at']);
                        $now = new DateTime();
                        $diff = $now->diff($created);
                        
                        if($diff->days > 0) {
                            echo $diff->days . ' ngày trước';
                        } elseif($diff->h > 0) {
                            echo $diff->h . ' giờ trước';
                        } elseif($diff->i > 0) {
                            echo $diff->i . ' phút trước';
                        } else {
                            echo 'Vừa xong';
                        }
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🔔</div>
            <h3>Không có thông báo mới</h3>
            <p>Bạn sẽ nhận được thông báo khi có yêu cầu kết nối</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        async function acceptConnection(connectionId, notificationId) {
            const formData = new FormData();
            formData.append('connection_id', connectionId);
            formData.append('notification_id', notificationId);

            try {
                const response = await fetch('index.php?action=accept_connection', {
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

        async function rejectConnection(connectionId, notificationId) {
            if(!confirm('Bạn có chắc muốn từ chối yêu cầu kết nối này?')) {
                return;
            }

            const formData = new FormData();
            formData.append('connection_id', connectionId);
            formData.append('notification_id', notificationId);

            try {
                const response = await fetch('index.php?action=reject_connection', {
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
    </script>
</body>
</html>
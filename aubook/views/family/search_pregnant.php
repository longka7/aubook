<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Mẹ Bầu - Aubook</title>
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

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
        }

        .search-pregnant-container {
            padding: 20px 0;
        }

        .page-title {
            font-size: 1.6rem;
            color: #FF7B9C;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .page-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #FF7B9C;
            box-shadow: 0 0 0 4px rgba(255, 123, 156, 0.1);
        }

        .btn {
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 123, 156, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #333;
            border: 2px solid #E0E0E0;
            margin-top: 10px;
        }

        .message .error {
            background: #FFEBEE;
            color: #F44336;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .found-screen,
        .not-found-screen {
            text-align: center;
            padding: 40px 20px;
        }

        .user-info-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFE8ED 0%, #FFD4D4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .user-phone {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .user-role {
            color: #FF7B9C;
            font-weight: 500;
        }

        .info-text {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-top: 20px;
        }

        .error-icon,
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .error-icon {
            background: #FFEBEE;
            color: #F44336;
        }

        .success-icon {
            background: #E8F5E9;
            color: #4CAF50;
        }

        .auto-redirect {
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }

        #countdown {
            color: #FF7B9C;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-pregnant-container">
            <h2 class="page-title">Nhập số điện thoại</h2>
            <p class="page-subtitle">Số điện thoại của mẹ bầu là gì?</p>

            <form id="searchForm">
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại mẹ bầu" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </form>
            
            <div id="searchResult" class="search-result"></div>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value;
            const formData = new FormData();
            formData.append('phone', phone);
            
            try {
                const response = await fetch('index.php?action=find_pregnant', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success && data.found) {
                    showFoundScreen(data.user);
                } else if(data.success && !data.found) {
                    showNotFoundScreen();
                } else {
                    document.getElementById('searchResult').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('searchResult').innerHTML = 
                    '<p class="error">Có lỗi xảy ra</p>';
            }
        });

        function showFoundScreen(user) {
            const container = document.querySelector('.search-pregnant-container');
            container.innerHTML = `
                <div class="found-screen">
                    <h2 class="page-title">Đã tìm thấy tài khoản của mẹ bầu</h2>
                    <p class="page-subtitle">Hãy gửi lời kết nối để đồng hành cùng mẹ trong hành trình thai kỳ nhé!</p>
                    
                    <div class="user-info-card">
                        <div class="user-avatar">👤</div>
                        <p class="user-phone">${user.phone}</p>
                        <p class="user-role">Mẹ bầu</p>
                    </div>
                    
                    <button class="btn btn-primary" onclick="sendConnectionRequest(${user.id})">
                        Gửi yêu cầu
                    </button>
                    
                    <p class="info-text">
                        Thông tin của bạn và mẹ bầu được bảo mật.<br>
                        Chỉ khi mẹ bầu đồng ý, bạn mới có thể xem thông tin thai kỳ.
                    </p>
                </div>
            `;
        }

        function showNotFoundScreen() {
            const container = document.querySelector('.search-pregnant-container');
            container.innerHTML = `
                <div class="not-found-screen">
                    <div class="error-icon">✕</div>
                    
                    <h2 class="page-title">Mẹ bầu chưa "có mặt" ở đây!</h2>
                    <p class="page-subtitle">Hãy kiểm tra lại số điện thoại hoặc rủ mẹ bầu đăng ký ứng dụng để cùng kết nối nhé.</p>
                    
                    <button class="btn btn-primary" onclick="location.reload()">
                        Nhập lại số điện thoại
                    </button>
                    
                    <button class="btn btn-secondary" onclick="window.location.href='index.php?action=family_dashboard'">
                        Để sau
                    </button>
                </div>
            `;
        }

        async function sendConnectionRequest(pregnantUserId) {
            const formData = new FormData();
            formData.append('pregnant_user_id', pregnantUserId);
            
            try {
                const response = await fetch('index.php?action=connect_pregnant', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    showSuccessScreen();
                } else {
                    alert(data.message);
                }
            } catch(error) {
                alert('Có lỗi xảy ra');
            }
        }

        function showSuccessScreen() {
            const container = document.querySelector('.search-pregnant-container');
            container.innerHTML = `
                <div class="success-screen">
                    <div class="success-icon">✓</div>
                    
                    <h2 class="page-title">Gửi yêu cầu thành công</h2>
                    <p class="page-subtitle">Chúng tôi đã gửi yêu cầu kết nối đến mẹ bầu.<br>
                    Bạn sẽ nhận được thông báo ngay khi mẹ bầu đồng ý.</p>
                    
                    <button class="btn btn-primary" onclick="window.location.href='index.php?action=family_dashboard'">
                        Vào trang chủ
                    </button>
                    
                    <button class="btn btn-secondary" onclick="location.reload()">
                        Quay lại đăng nhập
                    </button>
                    
                    <p class="auto-redirect">
                        Hệ thống tự động vào trang chủ sau <span id="countdown">9</span> giây
                    </p>
                </div>
            `;
            
            let seconds = 9;
            const countdownInterval = setInterval(() => {
                seconds--;
                const countdownEl = document.getElementById('countdown');
                if(countdownEl) {
                    countdownEl.textContent = seconds;
                }
                if(seconds <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'index.php?action=family_dashboard';
                }
            }, 1000);
        }
    </script>
</body>
</html>
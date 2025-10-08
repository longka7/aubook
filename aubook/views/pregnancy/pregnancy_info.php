<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Thai kỳ - Aubook</title>
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

        .pregnancy-info-container {
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
            line-height: 1.5;
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

        .form-group small {
            display: block;
            margin-top: 6px;
            color: #666;
            font-size: 0.85rem;
        }

        .calculate-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #FF7B9C;
            text-decoration: none;
            margin-top: 8px;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-block;
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

        .btn-copy {
            padding: 12px 20px;
            background: #FF7B9C;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .message .error {
            background: #FFEBEE;
            color: #F44336;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .success-screen {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            margin-bottom: 24px;
        }

        .heart-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            background: linear-gradient(135deg, #FFE8ED 0%, #FFD4D4 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .success-screen h2 {
            color: #FF7B9C;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .success-screen h3 {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .success-message {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .share-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .share-section h4 {
            margin-bottom: 12px;
            color: #333;
        }

        .share-link-box {
            display: flex;
            gap: 10px;
        }

        .share-link-box input {
            flex: 1;
            padding: 12px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pregnancy-info-container">
            <h2 class="page-title">Giúp Aubook đồng hành cùng mẹ</h2>
            <p class="page-subtitle">Bằng cách trả lời câu hỏi dưới đây, Aubook sẽ xây dựng nội dung phù hợp nhất cho mẹ!</p>

            <form id="pregnancyInfoForm">
                <div class="form-group">
                    <label>Ngày dự sinh</label>
                    <input type="date" id="due_date_display" readonly>
                    <a href="#" class="calculate-link" onclick="showConceptionInput(); return false;">
                        ℹ️ Tính ngày dự sinh
                    </a>
                </div>

                <div id="conceptionDateInput" class="form-group" style="display:none;">
                    <label>Ngày thụ thai</label>
                    <input type="date" id="conception_date" name="conception_date" required>
                    <small>Hệ thống sẽ tự động tính ngày dự sinh (sau 280 ngày)</small>
                </div>

                <button type="submit" class="btn btn-primary">Tiếp tục</button>
            </form>
            
            <div id="message" class="message"></div>
        </div>
    </div>

    <script>
        function showConceptionInput() {
            document.getElementById('conceptionDateInput').style.display = 'block';
        }

        document.getElementById('conception_date').addEventListener('change', function() {
            const conceptionDate = new Date(this.value);
            const dueDate = new Date(conceptionDate);
            dueDate.setDate(dueDate.getDate() + 280);
            
            const year = dueDate.getFullYear();
            const month = String(dueDate.getMonth() + 1).padStart(2, '0');
            const day = String(dueDate.getDate()).padStart(2, '0');
            
            document.getElementById('due_date_display').value = `${year}-${month}-${day}`;
        });

        document.getElementById('pregnancyInfoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const conceptionDate = document.getElementById('conception_date').value;
            
            if(!conceptionDate) {
                document.getElementById('message').innerHTML = 
                    '<p class="error">Vui lòng nhập ngày thụ thai</p>';
                return;
            }
            
            const formData = new FormData();
            formData.append('conception_date', conceptionDate);
            
            try {
                const response = await fetch('index.php?action=save_pregnancy_info', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    showSuccessScreen(data.due_date);
                } else {
                    document.getElementById('message').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('message').innerHTML = 
                    '<p class="error">Có lỗi xảy ra</p>';
            }
        });

        function showSuccessScreen(dueDate) {
            const container = document.querySelector('.pregnancy-info-container');
            container.innerHTML = `
                <div class="success-screen">
                    <div class="success-icon">
                        <div class="heart-icon">❤️</div>
                    </div>
                    
                    <h2>Thật tuyệt vời!</h2>
                    <h3>Bạn đã sẵn sàng cho hành trình này</h3>
                    
                    <p class="success-message">
                        Đừng đi một mình – hãy mời bố, người thân cùng bạn bé chăm sóc bé yêu và san sẻ khoảnh khắc yêu thương ngay từ hôm nay.
                    </p>
                    
                    <div class="share-section">
                        <h4>Link mời người thân:</h4>
                        <div class="share-link-box">
                            <input type="text" id="shareLink" value="tenapp.com/<?php echo $_SESSION['user_id'] ?? ''; ?>" readonly>
                            <button class="btn-copy" onclick="copyShareLink()">Sao chép</button>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary" onclick="shareLink()">
                        Chia sẻ tới người thân →
                    </button>
                    
                    <button class="btn btn-secondary" onclick="goToDashboard()">
                        Để sau
                    </button>
                </div>
            `;
        }

        function copyShareLink() {
            const linkInput = document.getElementById('shareLink');
            linkInput.select();
            document.execCommand('copy');
            alert('Đã sao chép link!');
        }

        function shareLink() {
            const shareLink = document.getElementById('shareLink').value;
            if (navigator.share) {
                navigator.share({
                    title: 'Theo dõi thai kỳ cùng tôi',
                    text: 'Hãy cùng tôi theo dõi hành trình mang thai!',
                    url: shareLink
                });
            } else {
                copyShareLink();
            }
        }

        function goToDashboard() {
            window.location.href = 'index.php?action=pregnancy_dashboard';
        }
    </script>
</body>
</html>
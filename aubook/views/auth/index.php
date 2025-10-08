<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aubook - Lắng Nghe trí thức khởi nguồn Trí Tuệ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #2C2C3E 0%, #1a1a2e 100%);
            color: white;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .welcome-content {
            text-align: center;
            padding: 40px 20px;
            width: 100%;
        }

        .title {
            font-size: 2.2rem;
            line-height: 1.3;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .highlight {
            color: #FF7B9C;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #B0B0C0;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 400px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 123, 156, 0.3);
        }

        .login-link {
            margin-top: 30px;
            color: #B0B0C0;
        }

        .login-link a {
            color: #FF7B9C;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            position: relative;
            color: #333;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
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

        .message .error {
            background: #FFEBEE;
            color: #F44336;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-content">
            <h1 class="title">
                <span class="highlight">Lắng Nghe</span> trí thức<br>
                khởi nguồn <span class="highlight">Trí Tuệ</span>
            </h1>
            
            <p class="subtitle">
                Mỗi cuốn sách mới là một thế giới mới,<br>
                bạn đã sẵn sàng khám phá chưa?
            </p>

            <div class="button-group">
                <a href="index.php?action=select_role" class="btn btn-primary">Bắt đầu!</a>
            </div>

            <p class="login-link">
                Bạn đã có tài khoản? <a onclick="showLoginModal()">Đăng nhập</a>
            </p>
        </div>
    </div>

    <!-- Modal Đăng nhập -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideLoginModal()">&times;</span>
            <h2>Đăng nhập</h2>
            <form id="loginForm">
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-primary">Đăng nhập</button>
            </form>
            <div id="loginMessage" class="message"></div>
        </div>
    </div>

    <script>
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function hideLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('index.php?action=login', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    if(data.role === 'me_bau') {
                        window.location.href = 'index.php?action=pregnancy_dashboard';
                    } else {
                        window.location.href = 'index.php?action=family_dashboard';
                    }
                } else {
                    document.getElementById('loginMessage').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('loginMessage').innerHTML = 
                    '<p class="error">Có lỗi xảy ra</p>';
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
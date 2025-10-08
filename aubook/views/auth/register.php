<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Aubook</title>
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

        .btn-back {
            background: none;
            border: none;
            color: #333;
            font-size: 1rem;
            cursor: pointer;
            padding: 10px;
            margin-bottom: 20px;
        }

        .register-container {
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

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
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
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #FF7B9C;
            box-shadow: 0 0 0 4px rgba(255, 123, 156, 0.1);
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

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
        }

        .otp-input:focus {
            border-color: #FF7B9C;
            box-shadow: 0 0 0 4px rgba(255, 123, 156, 0.1);
        }

        .message {
            margin-top: 20px;
        }

        .message .error {
            background: #FFEBEE;
            color: #F44336;
            padding: 12px;
            border-radius: 8px;
        }

        .message .success {
            background: #E8F5E9;
            color: #4CAF50;
            padding: 12px;
            border-radius: 8px;
        }

        .login-link {
            margin-top: 30px;
            text-align: center;
            color: #666;
        }

        .login-link a {
            color: #FF7B9C;
            text-decoration: none;
            font-weight: 600;
        }

        .resend-text {
            text-align: center;
            color: #666;
            margin: 20px 0;
        }

        .resend-text a {
            color: #FF7B9C;
            text-decoration: none;
            font-weight: 600;
        }

        .terms-text {
            margin-top: 20px;
            color: #666;
            font-size: 0.85rem;
            line-height: 1.6;
            text-align: center;
        }

        .terms-text a {
            color: #FF7B9C;
            text-decoration: none;
        }

        #displayPhone {
            color: #FF7B9C;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn-back" onclick="history.back()">← Quay lại</button>
        
        <div class="register-container">
            <h2 class="page-title">Chào mừng bạn đến với Aubook</h2>
            <p class="page-subtitle">Hãy tạo tài khoản để theo dõi thai kỳ, nhận cảm nang chăm sóc mẹ & bé và san sẻ yêu thương!</p>

            <!-- Bước 1: Nhập thông tin đăng ký -->
            <div id="step1" class="form-step active">
                <form id="registerInfoForm">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                    
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="full_name" placeholder="Nhập họ và tên" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nhập lại mật khẩu</label>
                        <input type="password" id="confirm_password" placeholder="Nhập lại mật khẩu" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                </form>
                <div id="registerInfoMessage" class="message"></div>
                
                <p class="terms-text">
                    Bằng việc tiếp tục, bạn đồng ý với <a href="#">Điều khoản sử dụng</a> và <a href="#">Chính sách bảo mật</a> của Aubook
                </p>
            </div>

            <!-- Bước 2: Xác thực OTP -->
            <div id="step2" class="form-step">
                <h3>Xác thực mã OTP</h3>
                <p>Bạn hãy nhập mã OTP được gửi về SDT <span id="displayPhone"></span></p>
                
                <form id="otpForm">
                    <div class="otp-input-group">
                        <input type="text" maxlength="1" class="otp-input" data-index="0">
                        <input type="text" maxlength="1" class="otp-input" data-index="1">
                        <input type="text" maxlength="1" class="otp-input" data-index="2">
                        <input type="text" maxlength="1" class="otp-input" data-index="3">
                        <input type="text" maxlength="1" class="otp-input" data-index="4">
                        <input type="text" maxlength="1" class="otp-input" data-index="5">
                    </div>
                    
                    <p class="resend-text">
                        Bạn không nhận được mã OTP? <a href="#" onclick="resendOTP(); return false;">Gửi lại</a>
                    </p>
                    
                    <button type="submit" class="btn btn-primary">Xác thực</button>
                </form>
                <div id="otpMessage" class="message"></div>
            </div>

            <p class="login-link">
                Bạn đã có tài khoản? <a href="index.php">Đăng nhập</a>
            </p>
        </div>
    </div>

    <script>
        let phoneNumber = '';
        let registerData = {};

        // Xử lý form nhập thông tin đăng ký
        document.getElementById('registerInfoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if(password !== confirmPassword) {
                document.getElementById('registerInfoMessage').innerHTML = 
                    '<p class="error">Mật khẩu không khớp</p>';
                return;
            }
            
            // Lưu thông tin vào biến tạm
            const formData = new FormData(this);
            registerData = {
                phone: formData.get('phone'),
                full_name: formData.get('full_name'),
                password: formData.get('password'),
                role: formData.get('role')
            };
            phoneNumber = registerData.phone;
            
            // Gửi OTP
            const otpFormData = new FormData();
            otpFormData.append('phone', phoneNumber);
            
            try {
                const response = await fetch('index.php?action=send_otp', {
                    method: 'POST',
                    body: otpFormData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    document.getElementById('displayPhone').textContent = phoneNumber;
                    showStep(2);
                    // Demo: Hiển thị OTP
                    if(data.otp) {
                        alert('OTP của bạn là: ' + data.otp);
                    }
                } else {
                    document.getElementById('registerInfoMessage').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('registerInfoMessage').innerHTML = 
                    '<p class="error">Có lỗi xảy ra</p>';
            }
        });

        // Xử lý OTP input
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if(this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                if(e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        // Xử lý form OTP
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });
            
            if(otp.length !== 6) {
                document.getElementById('otpMessage').innerHTML = 
                    '<p class="error">Vui lòng nhập đầy đủ mã OTP</p>';
                return;
            }
            
            // Xác thực OTP
            const otpFormData = new FormData();
            otpFormData.append('otp', otp);
            
            try {
                const response = await fetch('index.php?action=verify_otp', {
                    method: 'POST',
                    body: otpFormData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    // OTP đúng, tiến hành đăng ký
                    completeRegistration();
                } else {
                    document.getElementById('otpMessage').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('otpMessage').innerHTML = 
                    '<p class="error">Có lỗi xảy ra</p>';
            }
        });

        async function completeRegistration() {
            const formData = new FormData();
            formData.append('phone', registerData.phone);
            formData.append('full_name', registerData.full_name);
            formData.append('password', registerData.password);
            formData.append('role', registerData.role);
            
            try {
                const response = await fetch('index.php?action=register', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    // Chuyển hướng dựa trên role
                    if(data.role === 'me_bau') {
                        window.location.href = 'index.php?action=pregnancy_info';
                    } else {
                        window.location.href = 'index.php?action=search_pregnant';
                    }
                } else {
                    document.getElementById('otpMessage').innerHTML = 
                        '<p class="error">' + data.message + '</p>';
                }
            } catch(error) {
                document.getElementById('otpMessage').innerHTML = 
                    '<p class="error">Có lỗi xảy ra khi đăng ký</p>';
            }
        }

        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(el => {
                el.classList.remove('active');
            });
            document.getElementById('step' + step).classList.add('active');
        }

        function resendOTP() {
            const formData = new FormData();
            formData.append('phone', phoneNumber);
            
            fetch('index.php?action=send_otp', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Mã OTP mới đã được gửi!');
                    if(data.otp) {
                        alert('OTP của bạn là: ' + data.otp);
                    }
                }
            });
        }
    </script>
</body>
</html>
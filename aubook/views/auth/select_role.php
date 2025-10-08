<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Vai Trò - Aubook</title>
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

        .role-selection {
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

        .role-cards {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }

        .role-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            text-align: center;
        }

        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .role-card.selected {
            border-color: #FF7B9C;
            background: linear-gradient(135deg, #FFF5F7 0%, #FFE8ED 100%);
        }

        .role-card.family.selected {
            border-color: #D4AF37;
            background: linear-gradient(135deg, #FFFAF0 0%, #FFF8E7 100%);
        }

        .role-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            background: #FFD4D4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .role-card h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #333;
        }

        .role-card p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
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

        .btn-secondary {
            background: #FF7B9C;
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 123, 156, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn-back" onclick="history.back()">← Quay lại</button>
        
        <div class="role-selection">
            <h2 class="page-title">Bạn là ai trong hành trình này?</h2>
            <p class="page-subtitle">Chọn 1 trong 2 để tiếp tục</p>

            <div class="role-cards">
                <div class="role-card pregnant" onclick="selectRole('me_bau')">
                    <div class="role-icon">👶</div>
                    <h3>Mẹ bầu</h3>
                    <p>Dành cho mẹ bầu muốn theo dõi thai kỳ, nhận cảm nang chăm sóc sức khỏe hàng tuần.</p>
                </div>

                <div class="role-card family" onclick="selectRole('gia_dinh')">
                    <div class="role-icon">👨‍👩‍👧</div>
                    <h3>Gia đình</h3>
                    <p>Dành cho bố, người thân muốn đồng hành, nhắc nhở lịch khám và hỗ trợ mẹ bầu.</p>
                </div>
            </div>

            <button class="btn btn-secondary" onclick="continueWithRole()">Chọn vai trò</button>
        </div>
    </div>

    <script>
        let selectedRole = '';

        function selectRole(role) {
            selectedRole = role;
            
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            event.currentTarget.classList.add('selected');
        }

        function continueWithRole() {
            if(!selectedRole) {
                alert('Vui lòng chọn vai trò của bạn');
                return;
            }
            
            window.location.href = 'index.php?action=register_form&role=' + selectedRole;
        }
    </script>
</body>
</html>
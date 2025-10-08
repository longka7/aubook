<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi Thai kỳ - Aubook</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(180deg, #FFE8ED 0%, #FFF5F7 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .back-btn {
            position: absolute;
            left: 0;
            top: 0;
            background: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* Phần hình ảnh thai nhi */
        .embryo-section {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .embryo-image {
            width: 220px;
            height: 220px;
            margin: 0 auto 20px;
            position: relative;
        }

        .embryo-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 182, 193, 0.4) 0%, rgba(255, 182, 193, 0.1) 40%, transparent 70%);
            position: relative;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .embryo-core {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, #FFB6C1 0%, #FF8FA3 100%);
            box-shadow: 0 0 30px rgba(255, 123, 156, 0.4);
            transition: all 0.3s ease;
        }

        .embryo-inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, #FF7B9C 0%, #FF6B8A 100%);
            transition: all 0.3s ease;
        }

        /* Ẩn animation tinh trùng cũ */
        .sperm {
            display: none;
        }

        /* Hình ảnh thai nhi theo tuần */
        .embryo-week-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            z-index: 5;
        }

        .embryo-info {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            color: #FF7B9C;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Timeline tuần thai */
        .timeline-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .timeline-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .timeline-date {
            color: #FF7B9C;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .week-circles {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            position: relative;
            overflow-x: auto;
            padding: 10px 0;
        }

        .week-circles::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 5%;
            right: 5%;
            height: 3px;
            background: #FFE8ED;
            z-index: 0;
        }

        .week-circle {
            min-width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            border: 3px solid #FFE8ED;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #FFB6C1;
            position: relative;
            z-index: 1;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .week-circle:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(255, 123, 156, 0.2);
        }

        .week-circle.active {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            border-color: #FF7B9C;
            box-shadow: 0 4px 15px rgba(255, 123, 156, 0.4);
        }

        .week-circle.past {
            background: #FFE8ED;
            color: #FF7B9C;
            border-color: #FFB6C1;
        }

        .week-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            color: #666;
            white-space: nowrap;
        }

        /* Navigation arrows */
        .week-nav {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .nav-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #FF7B9C 0%, #FFA8B8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Thông tin chi tiết */
        .detail-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #F5F5F5;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #FF7B9C;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .detail-value.highlight {
            color: #FF7B9C;
            font-size: 1.3rem;
        }

        .progress-section {
            margin: 20px 0;
        }

        .progress-label {
            color: #FF7B9C;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .progress-bar-container {
            width: 100%;
            height: 12px;
            background: #FFE8ED;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF7B9C 0%, #FFA8B8 100%);
            border-radius: 6px;
            transition: width 0.5s ease;
        }

        .progress-percentage {
            text-align: center;
            color: #666;
            margin-top: 8px;
            font-size: 0.9rem;
        }

        .development-info {
            background: #FFF5F7;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .development-info h4 {
            color: #FF7B9C;
            margin-bottom: 10px;
        }

        .development-info p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button class="back-btn" onclick="window.location.href='index.php?action=pregnancy_dashboard'">← Quay lại</button>
        </div>

        <?php
        // Dữ liệu thai nhi đầy đủ 41 tuần
        $embryo_data = [
            1 => ['weight' => '< 1g', 'length' => '< 0.1cm', 'name' => 'Phôi nang', 'size_core' => 60, 'size_inner' => 30, 'desc' => 'Thai nhi bắt đầu hình thành từ phôi nang, rất nhỏ bé'],
            2 => ['weight' => '1-2g', 'length' => '< 1cm', 'name' => 'Phôi thai', 'size_core' => 70, 'size_inner' => 35, 'desc' => 'Phôi thai đang phát triển, bắt đầu hình thành các cơ quan'],
            3 => ['weight' => '2-3g', 'length' => '1-2cm', 'name' => 'Thai nhi', 'size_core' => 75, 'size_inner' => 38, 'desc' => 'Tim thai bắt đầu đập, hệ thần kinh hình thành'],
            4 => ['weight' => '5g', 'length' => '2cm', 'name' => 'Hạt đậu', 'size_core' => 80, 'size_inner' => 40, 'desc' => 'Các cơ quan chính bắt đầu hình thành rõ ràng hơn'],
            5 => ['weight' => '10g', 'length' => '3cm', 'name' => 'Hạt cam', 'size_core' => 85, 'size_inner' => 43, 'desc' => 'Tay chân bắt đầu xuất hiện, mắt và tai hình thành'],
            6 => ['weight' => '14g', 'length' => '4cm', 'name' => 'Quả mận', 'size_core' => 90, 'size_inner' => 45, 'desc' => 'Ngón tay ngón chân bắt đầu tách rời, não phát triển nhanh'],
            7 => ['weight' => '23g', 'length' => '5cm', 'name' => 'Quả cherry', 'size_core' => 95, 'size_inner' => 48, 'desc' => 'Tất cả các cơ quan chính đã hình thành'],
            8 => ['weight' => '30g', 'length' => '6cm', 'name' => 'Quả dâu', 'size_core' => 100, 'size_inner' => 50, 'desc' => 'Bé bắt đầu cử động nhẹ, xương bắt đầu cứng lại'],
            9 => ['weight' => '40g', 'length' => '7cm', 'name' => 'Quả nho', 'size_core' => 105, 'size_inner' => 53, 'desc' => 'Các ngón tay ngón chân đã hoàn chỉnh, móng bắt đầu mọc'],
            10 => ['weight' => '50g', 'length' => '8cm', 'name' => 'Quả chanh', 'size_core' => 110, 'size_inner' => 55, 'desc' => 'Bé có thể nuốt và đá nhẹ'],
            11 => ['weight' => '60g', 'length' => '9cm', 'name' => 'Quả sung', 'size_core' => 115, 'size_inner' => 58, 'desc' => 'Các cơ quan sinh dục bắt đầu phát triển'],
            12 => ['weight' => '70g', 'length' => '10cm', 'name' => 'Quả mơ', 'size_core' => 118, 'size_inner' => 60, 'desc' => 'Bé có thể mở miệng, hệ tiêu hóa hoạt động'],
            13 => ['weight' => '85g', 'length' => '11cm', 'name' => 'Quả đào', 'size_core' => 120, 'size_inner' => 62, 'desc' => 'Đầu chiếm 1/3 cơ thể, dây thanh quản hình thành'],
            14 => ['weight' => '100g', 'length' => '12cm', 'name' => 'Quả táo nhỏ', 'size_core' => 122, 'size_inner' => 64, 'desc' => 'Bé có thể nhăn mặt, tóc mọc mịn'],
            15 => ['weight' => '120g', 'length' => '13cm', 'name' => 'Quả cam', 'size_core' => 124, 'size_inner' => 66, 'desc' => 'Hệ xương phát triển mạnh, bé cử động nhiều hơn'],
            16 => ['weight' => '140g', 'length' => '14cm', 'name' => 'Quả bơ', 'size_core' => 126, 'size_inner' => 68, 'desc' => 'Có thể nghe được giọng mẹ, mắt nhạy cảm với ánh sáng'],
            17 => ['weight' => '160g', 'length' => '15cm', 'name' => 'Củ cải', 'size_core' => 128, 'size_inner' => 70, 'desc' => 'Lớp mỡ bắt đầu hình thành, giúp điều hòa thân nhiệt'],
            18 => ['weight' => '190g', 'length' => '16cm', 'name' => 'Ớt chuông', 'size_core' => 130, 'size_inner' => 72, 'desc' => 'Mẹ có thể cảm nhận được cử động của bé'],
            19 => ['weight' => '220g', 'length' => '17cm', 'name' => 'Cà chua lớn', 'size_core' => 132, 'size_inner' => 74, 'desc' => 'Giác quan phát triển mạnh, nhận biết âm thanh'],
            20 => ['weight' => '260g', 'length' => '18cm', 'name' => 'Chuối', 'size_core' => 134, 'size_inner' => 76, 'desc' => 'Bé ngủ và thức theo chu kỳ đều đặn'],
            21 => ['weight' => '300g', 'length' => '19cm', 'name' => 'Cà rốt', 'size_core' => 136, 'size_inner' => 78, 'desc' => 'Hệ tiêu hóa hoạt động tốt hơn'],
            22 => ['weight' => '350g', 'length' => '20cm', 'name' => 'Quả đu đủ', 'size_core' => 138, 'size_inner' => 80, 'desc' => 'Môi và lông mày rõ nét, da còn nhăn nheo'],
            23 => ['weight' => '400g', 'length' => '21cm', 'name' => 'Quả xoài', 'size_core' => 140, 'size_inner' => 82, 'desc' => 'Phổi đang phát triển, chuẩn bị cho việc thở'],
            24 => ['weight' => '450g', 'length' => '22cm', 'name' => 'Bắp ngô', 'size_core' => 142, 'size_inner' => 84, 'desc' => 'Bé nhạy cảm với âm thanh bên ngoài'],
            25 => ['weight' => '500g', 'length' => '23cm', 'name' => 'Súp lơ', 'size_core' => 144, 'size_inner' => 86, 'desc' => 'Não phát triển nhanh, mạch máu trong phổi hình thành'],
            26 => ['weight' => '600g', 'length' => '24cm', 'name' => 'Bông cải xanh', 'size_core' => 145, 'size_inner' => 88, 'desc' => 'Mắt bắt đầu mở, phản xạ tốt hơn'],
            27 => ['weight' => '700g', 'length' => '25cm', 'name' => 'Bắp cải', 'size_core' => 146, 'size_inner' => 90, 'desc' => 'Phổi tiếp tục trưởng thành'],
            28 => ['weight' => '800g', 'length' => '26cm', 'name' => 'Cà tím', 'size_core' => 147, 'size_inner' => 92, 'desc' => 'Tỷ lệ sống cao nếu sinh non, não phát triển mạnh'],
            29 => ['weight' => '900g', 'length' => '27cm', 'name' => 'Bí đỏ nhỏ', 'size_core' => 148, 'size_inner' => 94, 'desc' => 'Xương cứng hơn, cơ bắp mạnh mẽ'],
            30 => ['weight' => '1kg', 'length' => '28cm', 'name' => 'Dưa hấu nhỏ', 'size_core' => 149, 'size_inner' => 96, 'desc' => 'Não tiếp tục phát triển, điều hòa thân nhiệt tốt hơn'],
            31 => ['weight' => '1.2kg', 'length' => '29cm', 'name' => 'Dừa', 'size_core' => 150, 'size_inner' => 98, 'desc' => 'Cử động mạnh mẽ hơn, không gian tử cung chật hẹp'],
            32 => ['weight' => '1.4kg', 'length' => '30cm', 'name' => 'Củ cải đường', 'size_core' => 151, 'size_inner' => 100, 'desc' => 'Da mịn màng hơn, lớp mỡ dưới da dày lên'],
            33 => ['weight' => '1.6kg', 'length' => '31cm', 'name' => 'Dứa', 'size_core' => 152, 'size_inner' => 102, 'desc' => 'Hệ miễn dịch phát triển, nhận kháng thể từ mẹ'],
            34 => ['weight' => '1.8kg', 'length' => '32cm', 'name' => 'Dưa lê', 'size_core' => 153, 'size_inner' => 104, 'desc' => 'Hệ thần kinh trung ương hoàn thiện'],
            35 => ['weight' => '2kg', 'length' => '33cm', 'name' => 'Dưa hấu', 'size_core' => 154, 'size_inner' => 106, 'desc' => 'Phổi gần như hoàn thiện, sẵn sàng thở không khí'],
            36 => ['weight' => '2.2kg', 'length' => '34cm', 'name' => 'Bí đỏ', 'size_core' => 155, 'size_inner' => 108, 'desc' => 'Bé đã ở tư thế chờ sinh, đầu hướng xuống'],
            37 => ['weight' => '2.4kg', 'length' => '35cm', 'name' => 'Bí ngô', 'size_core' => 156, 'size_inner' => 110, 'desc' => 'Thai nhi đủ tháng, có thể sinh bất cứ lúc nào'],
            38 => ['weight' => '2.6kg', 'length' => '36cm', 'name' => 'Dưa lưới', 'size_core' => 157, 'size_inner' => 112, 'desc' => 'Cơ thể đầy đặn, sẵn sàng chào đời'],
            39 => ['weight' => '2.8kg', 'length' => '37cm', 'name' => 'Bí đao', 'size_core' => 158, 'size_inner' => 114, 'desc' => 'Các cơ quan hoàn chỉnh, chờ thời điểm sinh'],
            40 => ['weight' => '3kg', 'length' => '38cm', 'name' => 'Em bé', 'size_core' => 159, 'size_inner' => 116, 'desc' => 'Đã đến ngày dự sinh, mọi thứ hoàn hảo'],
            41 => ['weight' => '3.2kg', 'length' => '39cm', 'name' => 'Em bé', 'size_core' => 160, 'size_inner' => 118, 'desc' => 'Quá ngày dự sinh, cần theo dõi sát'],
        ];

        if(isset($pregnancy_info) && $pregnancy_info):
            $conception = new DateTime($pregnancy_info['conception_date']);
            $now = new DateTime();
            $due = new DateTime($pregnancy_info['due_date']);
            
            $diff = $conception->diff($now);
            $total_days = $diff->days;
            $current_week_actual = min(41, floor($total_days / 7) + 1);
            
            // Lấy tuần được chọn từ URL hoặc dùng tuần hiện tại
            $selected_week = isset($_GET['week']) ? (int)$_GET['week'] : $current_week_actual;
            $selected_week = max(1, min(41, $selected_week));
            
            $current_day = ($total_days % 7) + 1;
            $remaining = $now->diff($due);
            $percentage = min(100, ($total_days / 280) * 100);
            
            $current_data = $embryo_data[$selected_week];
        ?>

        <!-- Phần hình ảnh thai nhi -->
        <div class="embryo-section">
            <h3 style="color: #FF7B9C; margin-bottom: 15px;">Tuần <?php echo $selected_week; ?> • <?php echo $current_data['name']; ?></h3>
            
            <div class="embryo-image">
                <div class="embryo-circle">
                    <?php 
                    // Kiểm tra xem có file ảnh PNG cho tuần này không
                    $image_path = "assets/images/embryo_week_" . $selected_week . ".png";
                    if(file_exists($image_path)): 
                    ?>
                        <!-- Hiển thị ảnh PNG cho tuần này -->
                        <img src="<?php echo $image_path; ?>" class="embryo-week-image" alt="Thai nhi tuần <?php echo $selected_week; ?>">
                    <?php else: ?>
                        <!-- Nếu không có ảnh PNG, hiển thị animation tinh trùng mặc định -->
                        <span class="sperm"></span>
                        <span class="sperm"></span>
                        <span class="sperm"></span>
                        <span class="sperm"></span>
                        <span class="sperm"></span>
                        
                        <div class="embryo-core" style="width: <?php echo $current_data['size_core']; ?>px; height: <?php echo $current_data['size_core']; ?>px;">
                            <div class="embryo-inner" style="width: <?php echo $current_data['size_inner']; ?>px; height: <?php echo $current_data['size_inner']; ?>px;"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="embryo-info">
                <div class="info-item">
                    <div class="info-label">Cân nặng</div>
                    <div class="info-value"><?php echo $current_data['weight']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Chiều dài</div>
                    <div class="info-value"><?php echo $current_data['length']; ?></div>
                </div>
            </div>

            <div class="development-info" style="margin-top: 20px;">
                <h4>Sự phát triển tuần <?php echo $selected_week; ?></h4>
                <p><?php echo $current_data['desc']; ?></p>
            </div>
        </div>

        <!-- Timeline tuần thai -->
        <div class="timeline-section">
            <div class="timeline-header">
                <div class="timeline-date">
                    <?php 
                        $week_start = clone $conception;
                        $week_start->add(new DateInterval('P' . (($selected_week - 1) * 7) . 'D'));
                        $week_end = clone $week_start;
                        $week_end->add(new DateInterval('P6D'));
                        echo $week_start->format('d') . ' thg ' . $week_start->format('n') . ' - ' . $week_end->format('d') . ' thg ' . $week_end->format('n');
                    ?>
                </div>
            </div>

            <div class="week-circles">
                <?php for($i = max(1, $selected_week - 2); $i <= min(41, $selected_week + 2); $i++): 
                    $class = '';
                    if($i == $selected_week) $class = 'active';
                    elseif($i < $current_week_actual) $class = 'past';
                ?>
                <div class="week-circle <?php echo $class; ?>" onclick="changeWeek(<?php echo $i; ?>)">
                    <?php echo $i; ?>
                    <div class="week-label">
                        <?php 
                            if($i == $current_week_actual) echo 'hiện tại';
                            elseif($i == $selected_week) echo 'tuần';
                            else echo '';
                        ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Thông tin chi tiết -->
        <div class="detail-section">
            <div class="detail-row">
                <span class="detail-label">Tuần thai đang xem</span>
                <span class="detail-value">Tuần <?php echo $selected_week; ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tuần thai hiện tại</span>
                <span class="detail-value">Tuần <?php echo $current_week_actual; ?> • Ngày <?php echo $current_day; ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Ngày dự sinh</span>
                <span class="detail-value highlight"><?php echo $due->format('d/m/Y'); ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tiến độ</span>
                <span class="detail-value"><?php echo round($percentage); ?>% • Còn <?php echo $remaining->days; ?> ngày</span>
            </div>
        </div>

        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <p>Chưa có thông tin thai kỳ</p>
            <a href="index.php?action=pregnancy_info" style="color: #FF7B9C;">Nhập thông tin</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function changeWeek(week) {
            window.location.href = 'index.php?action=weekly_tracker&week=' + week;
        }
    </script>
</body>
</html><!DOCTYPE html>

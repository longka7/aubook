-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 08, 2025 at 06:12 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aubook_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `avatar`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@aubook.com', '$2y$10$F/qVjN7rKfdUFSE0gKjRNemlWyjAhS749iI6lIg/NUeuJuNvZ5TQm', 'Super Admin', NULL, 'super_admin', 'active', '2025-10-08 09:24:42', '2025-10-08 09:22:21', '2025-10-08 09:24:42');

-- --------------------------------------------------------

--
-- Stand-in structure for view `admin_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `admin_dashboard_stats` (
`total_users` bigint(21)
,`new_users_today` bigint(21)
,`new_users_month` bigint(21)
,`total_me_bau` bigint(21)
,`total_gia_dinh` bigint(21)
,`total_posts` bigint(21)
,`posts_today` bigint(21)
,`posts_pending` bigint(21)
,`total_connections` bigint(21)
,`connections_pending` bigint(21)
,`reports_pending` bigint(21)
,`articles_published` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `table_name`, `record_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', NULL, NULL, 'Admin đăng nhập thành công', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Safari/605.1.15', '2025-10-08 09:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('draft','published','unpublished') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `author_id`, `status`, `published_at`, `view_count`, `is_featured`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, '10 Điều Mẹ Bầu Cần Biết Trong 3 Tháng Đầu Thai Kỳ', '10-dieu-me-bau-can-biet-trong-3-thang-dau-thai-ky', 'Ba tháng đầu thai kỳ là giai đoạn quan trọng nhất. Hãy cùng tìm hiểu 10 điều cần lưu ý để mẹ và bé khỏe mạnh.', '<h2>1. Uống đủ nước</h2>\r\n<p>Mẹ bầu cần uống ít nhất 2 lít nước mỗi ngày để đảm bảo cơ thể không bị mất nước và giúp thai nhi phát triển tốt.</p>\r\n\r\n<h2>2. Bổ sung axit folic</h2>\r\n<p>Axit folic rất quan trọng trong 3 tháng đầu, giúp ngăn ngừa dị tật ống thần kinh ở thai nhi. Liều khuyến nghị là 400-800 mcg/ngày.</p>\r\n\r\n<h2>3. Nghỉ ngơi đầy đủ</h2>\r\n<p>Cơ thể đang thay đổi nhiều, mẹ cần ngủ đủ 8 tiếng mỗi ngày và nghỉ ngơi khi cảm thấy mệt.</p>\r\n\r\n<h2>4. Ăn nhiều bữa nhỏ</h2>\r\n<p>Thay vì 3 bữa lớn, hãy chia thành 5-6 bữa nhỏ để tránh ói mửa và duy trì năng lượng.</p>\r\n\r\n<h2>5. Tránh stress</h2>\r\n<p>Căng thẳng có thể ảnh hưởng đến sức khỏe mẹ và bé. Hãy tìm cách thư giãn như nghe nhạc, yoga nhẹ nhàng.</p>\r\n\r\n<h2>6. Khám thai định kỳ</h2>\r\n<p>Đây là thời điểm quan trọng để theo dõi sự phát triển của thai nhi và phát hiện sớm các vấn đề.</p>\r\n\r\n<h2>7. Tránh các chất độc hại</h2>\r\n<p>Không hút thuốc, uống rượu, tránh xa khói thuốc và các hóa chất độc hại.</p>\r\n\r\n<h2>8. Tập thể dục nhẹ nhàng</h2>\r\n<p>Đi bộ, yoga cho bà bầu giúp cơ thể khỏe mạnh và giảm stress.</p>\r\n\r\n<h2>9. Chăm sóc tinh thần</h2>\r\n<p>Tâm trạng vui vẻ rất quan trọng. Hãy chia sẻ cảm xúc với người thân.</p>\r\n\r\n<h2>10. Lắng nghe cơ thể</h2>\r\n<p>Mỗi cơ thể khác nhau, hãy lắng nghe và điều chỉnh cho phù hợp với bản thân.</p>', NULL, 1, 'published', '2025-10-08 11:08:30', 247, 1, NULL, NULL, '2025-10-08 11:08:30', '2025-10-08 11:10:05'),
(2, 2, 'Thực Đơn Dinh Dưỡng Cho Mẹ Bầu 3 Tháng Đầu', 'thuc-don-dinh-duong-cho-me-bau-3-thang-dau', 'Chế độ ăn uống khoa học giúp mẹ khỏe mạnh, bé phát triển toàn diện trong 3 tháng đầu thai kỳ.', '<h2>Bữa sáng (7:00 - 8:00)</h2>\r\n<p><strong>Option 1:</strong> Phở gà + 1 quả chuối + 1 ly sữa</p>\r\n<p><strong>Option 2:</strong> Bánh mì trứng + salad rau + nước ép cam</p>\r\n\r\n<h2>Bữa phụ sáng (10:00)</h2>\r\n<p>1 hộp sữa chua không đường + 1 nắm hạt</p>\r\n\r\n<h2>Bữa trưa (12:00 - 13:00)</h2>\r\n<ul>\r\n<li>Cơm gạo lứt</li>\r\n<li>Cá hồi/cá thu nướng</li>\r\n<li>Rau xào (cải ngồng, súp lơ)</li>\r\n<li>Canh rau củ</li>\r\n</ul>\r\n\r\n<h2>Bữa phụ chiều (15:00)</h2>\r\n<p>Trái cây theo mùa: táo, lê, cam, bưởi</p>\r\n\r\n<h2>Bữa tối (18:00 - 19:00)</h2>\r\n<ul>\r\n<li>Cơm trắng</li>\r\n<li>Thịt bò xào rau</li>\r\n<li>Đậu hũ sốt cà chua</li>\r\n<li>Canh rong biển</li>\r\n</ul>\r\n\r\n<h2>Bữa phụ tối (21:00)</h2>\r\n<p>1 ly sữa ấm + vài miếng bánh quy nguyên cám</p>\r\n\r\n<h2>Lưu ý quan trọng:</h2>\r\n<ul>\r\n<li>Ăn chín, uống sôi</li>\r\n<li>Tránh đồ ăn sống, chưa nấu kỹ</li>\r\n<li>Không ăn gan động vật (nhiều vitamin A)</li>\r\n<li>Hạn chế caffeine</li>\r\n<li>Bổ sung vitamin tổng hợp theo chỉ định bác sĩ</li>\r\n</ul>', NULL, 1, 'published', '2025-10-08 11:08:30', 189, 1, NULL, NULL, '2025-10-08 11:08:30', '2025-10-08 11:08:30'),
(3, 3, 'Cách Xử Lý Ốm Nghén Hiệu Quả Cho Mẹ Bầu', 'cach-xu-ly-om-nghen-hieu-qua-cho-me-bau', 'Ốm nghén là triệu chứng phổ biến ở 3 tháng đầu. Dưới đây là những cách giúp giảm khó chịu.', '<h2>Ốm nghén là gì?</h2>\r\n<p>Ốm nghén là tình trạng buồn nôn và nôn thường xảy ra trong 3 tháng đầu thai kỳ do thay đổi hormone.</p>\r\n\r\n<h2>Các cách giảm ốm nghén:</h2>\r\n\r\n<h3>1. Ăn gừng</h3>\r\n<p>Gừng có tác dụng giảm buồn nôn rất tốt. Có thể dùng:</p>\r\n<ul>\r\n<li>Nước gừng ấm</li>\r\n<li>Trà gừng mật ong</li>\r\n<li>Kẹo gừng</li>\r\n</ul>\r\n\r\n<h3>2. Ăn nhiều bữa nhỏ</h3>\r\n<p>Dạ dày trống làm tăng buồn nôn. Hãy ăn 5-6 bữa nhỏ mỗi ngày.</p>\r\n\r\n<h3>3. Tránh mùi khó chịu</h3>\r\n<p>Mùi tanh, mùi mạnh có thể kích thích buồn nôn. Hãy tránh xa.</p>\r\n\r\n<h3>4. Uống nước chanh</h3>\r\n<p>Nước chanh pha loãng giúp giảm buồn nôn và bổ sung vitamin C.</p>\r\n\r\n<h3>5. Ngủ đủ giấc</h3>\r\n<p>Thiếu ngủ làm ốm nghén nặng hơn. Nghỉ ngơi đầy đủ rất quan trọng.</p>\r\n\r\n<h3>6. Bấm huyệt</h3>\r\n<p>Bấm huyệt Nội Quan (3 ngón tay từ cổ tay) giúp giảm buồn nôn.</p>\r\n\r\n<h2>Khi nào cần gặp bác sĩ?</h2>\r\n<p>Nếu:</p>\r\n<ul>\r\n<li>Nôn quá nhiều, không giữ được thức ăn</li>\r\n<li>Sụt cân nhanh</li>\r\n<li>Nước tiểu sẫm màu</li>\r\n<li>Chóng mặt, ngất xỉu</li>\r\n</ul>\r\n\r\n<p><strong>Hãy đến gặp bác sĩ ngay!</strong></p>', NULL, 1, 'published', '2025-10-08 11:08:30', 312, 1, NULL, NULL, '2025-10-08 11:08:30', '2025-10-08 11:08:30'),
(4, 4, 'Sự Phát Triển Của Thai Nhi Qua Từng Tuần', 'su-phat-trien-cua-thai-nhi-qua-tung-tuan', 'Theo dõi hành trình phát triển kỳ diệu của bé yêu từng tuần thai kỳ.', '<h2>Tuần 4-5: Khởi đầu</h2>\r\n<p>Thai nhi chỉ bằng hạt gạo, tim bắt đầu đập.</p>\r\n\r\n<h2>Tuần 6-7: Hình thành cơ bản</h2>\r\n<p>Bé đã có đầu, thân và đuôi. Não và hệ thần kinh phát triển.</p>\r\n\r\n<h2>Tuần 8-9: Nhỏ như nho</h2>\r\n<p>Các ngón tay, ngón chân bắt đầu hình thành. Bé đã biết cử động!</p>\r\n\r\n<h2>Tuần 10-12: Ra dáng người</h2>\r\n<p>Bé đã có khuôn mặt, có thể ngậm ngón tay. Các cơ quan nội tạng hoàn thiện.</p>\r\n\r\n<h2>Tuần 13-16: Giới tính rõ ràng</h2>\r\n<p>Có thể xác định giới tính qua siêu âm. Bé nghe được âm thanh từ bên ngoài.</p>\r\n\r\n<h2>Tuần 17-20: Cử động mạnh mẽ</h2>\r\n<p>Mẹ cảm nhận được bé đạp. Bé có thể ngáp, nuốt.</p>\r\n\r\n<h2>Tuần 21-24: Phát triển não bộ</h2>\r\n<p>Não phát triển nhanh. Bé có chu kỳ ngủ - thức.</p>\r\n\r\n<h2>Tuần 25-28: Mở mắt</h2>\r\n<p>Bé có thể mở mắt, phản ứng với ánh sáng.</p>\r\n\r\n<h2>Tuần 29-32: Tích trữ chất béo</h2>\r\n<p>Bé tăng cân nhanh, da mịn màng hơn.</p>\r\n\r\n<h2>Tuần 33-36: Chuẩn bị chào đời</h2>\r\n<p>Bé xoay đầu xuống dưới, phổi hoàn thiện.</p>\r\n\r\n<h2>Tuần 37-40: Sẵn sàng!</h2>\r\n<p>Bé đủ tháng, sẵn sàng chào đời bất cứ lúc nào!</p>', NULL, 1, 'published', '2025-10-08 11:08:30', 156, 0, NULL, NULL, '2025-10-08 11:08:30', '2025-10-08 11:08:30'),
(5, 5, 'Chuẩn Bị Đồ Vào Viện Sinh Đầy Đủ Nhất', 'chuan-bi-do-vao-vien-sinh-day-du-nhat', 'Checklist chi tiết giúp mẹ chuẩn bị đầy đủ đồ đạc khi vào viện sinh.', '<h2>Đồ cho mẹ</h2>\r\n\r\n<h3>Giấy tờ:</h3>\r\n<ul>\r\n<li>CMND/CCCD</li>\r\n<li>Sổ khám thai</li>\r\n<li>Kết quả xét nghiệm</li>\r\n<li>Sổ bảo hiểm y tế</li>\r\n</ul>\r\n\r\n<h3>Đồ mặc:</h3>\r\n<ul>\r\n<li>3-4 bộ đồ sau sinh</li>\r\n<li>Áo choàng tắm</li>\r\n<li>Dép đi trong nhà</li>\r\n<li>Quần lót dùng 1 lần (5-7 chiếc)</li>\r\n</ul>\r\n\r\n<h3>Đồ vệ sinh:</h3>\r\n<ul>\r\n<li>Bàn chải, kem đánh răng</li>\r\n<li>Khăn tắm, khăn mặt</li>\r\n<li>Dầu gội, sữa tắm</li>\r\n<li>Băng vệ sinh sau sinh</li>\r\n<li>Miếng lót thấm sữa</li>\r\n</ul>\r\n\r\n<h3>Đồ dùng khác:</h3>\r\n<ul>\r\n<li>Bình nước</li>\r\n<li>Ống hút</li>\r\n<li>Giấy ăn, khăn ướt</li>\r\n<li>Điện thoại + sạc</li>\r\n</ul>\r\n\r\n<h2>Đồ cho bé</h2>\r\n\r\n<h3>Quần áo:</h3>\r\n<ul>\r\n<li>3-4 bộ body suit</li>\r\n<li>2-3 bộ áo dài tay, quần dài</li>\r\n<li>Mũ, bao tay, tất</li>\r\n<li>Khăn tắm cho bé</li>\r\n</ul>\r\n\r\n<h3>Tã & vệ sinh:</h3>\r\n<ul>\r\n<li>Tã sơ sinh (1 gói)</li>\r\n<li>Khăn ướt cho bé</li>\r\n<li>Kem chống hăm</li>\r\n</ul>\r\n\r\n<h3>Bú sữa:</h3>\r\n<ul>\r\n<li>Bình sữa (2 cái)</li>\r\n<li>Núm vú silicone</li>\r\n<li>Máy hút sữa (nếu cần)</li>\r\n</ul>\r\n\r\n<h2>Lưu ý:</h2>\r\n<p>✅ Chuẩn bị trước 2-3 tuần<br>\r\n✅ Để túi ở vị trí dễ lấy<br>\r\n✅ Thông báo người thân biết vị trí<br>\r\n✅ Kiểm tra lại trước khi đi</p>', NULL, 1, 'published', '2025-10-08 11:08:30', 423, 0, NULL, NULL, '2025-10-08 11:08:30', '2025-10-08 11:08:30');

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE `article_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_categories`
--

INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `icon`, `color`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Thai kỳ', 'thai-ky', 'Kiến thức về các giai đoạn mang thai', '🤰', '#FF7B9C', 1, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21'),
(2, 'Dinh dưỡng', 'dinh-duong', 'Chế độ ăn uống cho mẹ bầu', '🥗', '#4CAF50', 2, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21'),
(3, 'Sức khỏe', 'suc-khoe', 'Chăm sóc sức khỏe bà bầu', '💊', '#2196F3', 3, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21'),
(4, 'Thai nhi', 'thai-nhi', 'Sự phát triển của thai nhi', '👶', '#FFC107', 4, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21'),
(5, 'Sinh đẻ', 'sinh-de', 'Chuẩn bị và quy trình sinh đẻ', '🏥', '#E91E63', 5, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21'),
(6, 'Sau sinh', 'sau-sinh', 'Chăm sóc mẹ sau sinh', '🤱', '#9C27B0', 6, 1, '2025-10-08 04:28:21', '2025-10-08 04:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `unread_count` int(11) DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_connections`
--

CREATE TABLE `family_connections` (
  `id` int(11) NOT NULL,
  `family_user_id` int(11) NOT NULL,
  `pregnant_user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_connections`
--

INSERT INTO `family_connections` (`id`, `family_user_id`, `pregnant_user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'approved', '2025-10-03 03:53:17', '2025-10-03 04:52:32'),
(2, 3, 1, 'approved', '2025-10-03 04:57:15', '2025-10-03 04:57:36'),
(3, 5, 4, 'approved', '2025-10-03 05:40:46', '2025-10-03 05:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `message_type` enum('text','image','file') DEFAULT 'text',
  `attachment_url` varchar(500) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_read_status`
--

CREATE TABLE `message_read_status` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('connection_request','connection_approved','connection_rejected','post_like','post_comment','post_share') NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `from_user_id`, `connection_id`, `is_read`, `created_at`, `post_id`) VALUES
(1, 1, 'connection_request', 3, 2, 1, '2025-10-03 04:57:15', NULL),
(2, 3, 'connection_approved', 1, 2, 0, '2025-10-03 04:57:36', NULL),
(3, 4, 'connection_request', 5, 3, 1, '2025-10-03 05:40:46', NULL),
(4, 5, 'connection_approved', 4, 3, 0, '2025-10-03 05:41:20', NULL),
(5, 1, 'post_like', 3, NULL, 0, '2025-10-03 08:40:58', NULL),
(6, 1, 'post_comment', 3, NULL, 0, '2025-10-03 08:41:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `phone`, `otp_code`, `expires_at`, `is_verified`, `created_at`) VALUES
(1, '0911517296', '552040', '2025-10-03 03:45:03', 1, '2025-10-03 03:44:55'),
(2, '0911517988', '748724', '2025-10-03 03:47:57', 1, '2025-10-03 03:47:51'),
(3, '0911517926', '476385', '2025-10-03 04:57:06', 1, '2025-10-03 04:56:57'),
(4, '0911517999', '749474', '2025-10-03 05:36:49', 1, '2025-10-03 05:35:42'),
(5, '0911517911', '330813', '2025-10-03 05:40:07', 1, '2025-10-03 05:39:50');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','hidden') DEFAULT 'approved',
  `moderated_by` int(11) DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `report_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `image_url`, `status`, `moderated_by`, `moderated_at`, `rejection_reason`, `report_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'alo 123', NULL, 'approved', NULL, NULL, NULL, 0, '2025-10-03 08:34:59', '2025-10-03 08:34:59'),
(2, 1, 'dep qua', NULL, 'approved', NULL, NULL, NULL, 0, '2025-10-03 08:45:40', '2025-10-03 08:45:40'),
(4, 1, 'ngin qa', 'uploads/posts/post_1_1759595155.jpg', 'approved', NULL, NULL, NULL, 0, '2025-10-04 16:25:55', '2025-10-04 16:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 3, 'hay', '2025-10-03 08:41:04');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, '2025-10-03 08:40:09'),
(2, 1, 3, '2025-10-03 08:40:58'),
(3, 2, 1, '2025-10-03 09:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `post_reports`
--

CREATE TABLE `post_reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `reason` enum('spam','harassment','inappropriate','false_info','other') NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_shares`
--

CREATE TABLE `post_shares` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pregnancy_info`
--

CREATE TABLE `pregnancy_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `conception_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pregnancy_info`
--

INSERT INTO `pregnancy_info` (`id`, `user_id`, `conception_date`, `due_date`, `created_at`, `updated_at`) VALUES
(10, 1, '2025-10-01', '2026-07-08', '2025-10-03 03:45:09', '2025-10-03 03:45:09'),
(11, 4, '2025-10-03', '2026-07-10', '2025-10-03 05:37:19', '2025-10-03 05:37:19');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `target_type` enum('all','me_bau','gia_dinh','specific') DEFAULT 'all',
  `target_users` text DEFAULT NULL,
  `notification_type` enum('info','warning','success','error') DEFAULT 'info',
  `status` enum('draft','scheduled','sent','cancelled') DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'Aubook', 'text', 'Tên website', NULL, '2025-10-08 04:28:21'),
(2, 'site_description', 'Ứng dụng theo dõi thai kỳ', 'text', 'Mô tả website', NULL, '2025-10-08 04:28:21'),
(3, 'admin_email', 'admin@aubook.com', 'text', 'Email liên hệ admin', NULL, '2025-10-08 04:28:21'),
(4, 'posts_per_page', '20', 'number', 'Số bài đăng mỗi trang', NULL, '2025-10-08 04:28:21'),
(5, 'enable_registration', 'true', 'boolean', 'Cho phép đăng ký tài khoản mới', NULL, '2025-10-08 04:28:21'),
(6, 'enable_post_moderation', 'false', 'boolean', 'Kiểm duyệt bài đăng trước khi hiển thị', NULL, '2025-10-08 04:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('me_bau','gia_dinh') NOT NULL,
  `status` enum('active','locked','suspended','deleted') DEFAULT 'active',
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` int(11) DEFAULT NULL,
  `locked_reason` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone`, `full_name`, `password`, `role`, `status`, `locked_at`, `locked_by`, `locked_reason`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '0911517296', 'long', '$2y$10$OVwXjgbM6jrOtk813wtMw.yi1CVmQLcQVjeBpZuUv8LeKVlhGHIMe', 'me_bau', 'active', NULL, NULL, NULL, NULL, '2025-10-03 03:45:03', '2025-10-03 03:45:03'),
(2, '0911517988', 'long', '$2y$10$3JmhWj8PQ8AXKpfbSDY7/erksksqkaz4zhu/BvX38msZNIT742W6C', 'gia_dinh', 'active', NULL, NULL, NULL, NULL, '2025-10-03 03:47:58', '2025-10-03 03:47:58'),
(3, '0911517926', 'ba', '$2y$10$dCE74bR9vqMHwajPnsIHGOM6Cbb4s46PIKRWTtdHk4VPxPPvtX5Ly', 'gia_dinh', 'active', NULL, NULL, NULL, NULL, '2025-10-03 04:57:06', '2025-10-03 04:57:06'),
(4, '0911517999', 'me', '$2y$10$rPIfJ3leGKaqLI.5JBLOsuxmCm60mDOFrr6NLpkdol/zQKMrW4/52', 'me_bau', 'active', NULL, NULL, NULL, NULL, '2025-10-03 05:36:49', '2025-10-03 05:36:49'),
(5, '0911517911', 'gdinh', '$2y$10$Vl/5NfVlln80gN7gNqa3iu7xUdiWCINpD2YSOxG82dvfen6EIqBUK', 'gia_dinh', 'active', NULL, NULL, NULL, NULL, '2025-10-03 05:40:07', '2025-10-03 05:40:07');

-- --------------------------------------------------------

--
-- Structure for view `admin_dashboard_stats`
--
DROP TABLE IF EXISTS `admin_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `admin_dashboard_stats`  AS SELECT (select count(0) from `users` where `users`.`status` = 'active') AS `total_users`, (select count(0) from `users` where cast(`users`.`created_at` as date) = curdate()) AS `new_users_today`, (select count(0) from `users` where month(`users`.`created_at`) = month(curdate()) and year(`users`.`created_at`) = year(curdate())) AS `new_users_month`, (select count(0) from `users` where `users`.`role` = 'me_bau' and `users`.`status` = 'active') AS `total_me_bau`, (select count(0) from `users` where `users`.`role` = 'gia_dinh' and `users`.`status` = 'active') AS `total_gia_dinh`, (select count(0) from `posts`) AS `total_posts`, (select count(0) from `posts` where cast(`posts`.`created_at` as date) = curdate()) AS `posts_today`, (select count(0) from `posts` where `posts`.`status` = 'pending') AS `posts_pending`, (select count(0) from `family_connections` where `family_connections`.`status` = 'approved') AS `total_connections`, (select count(0) from `family_connections` where `family_connections`.`status` = 'pending') AS `connections_pending`, (select count(0) from `post_reports` where `post_reports`.`status` = 'pending') AS `reports_pending`, (select count(0) from `articles` where `articles`.`status` = 'published') AS `articles_published` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_updated` (`updated_at`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participant` (`conversation_id`,`user_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_conversation` (`conversation_id`);

--
-- Indexes for table `family_connections`
--
ALTER TABLE `family_connections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_connection` (`family_user_id`,`pregnant_user_id`),
  ADD KEY `pregnant_user_id` (`pregnant_user_id`),
  ADD KEY `idx_family_connections` (`family_user_id`,`pregnant_user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation` (`conversation_id`,`created_at`),
  ADD KEY `idx_sender` (`sender_id`);

--
-- Indexes for table `message_read_status`
--
ALTER TABLE `message_read_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_read` (`message_id`,`user_id`),
  ADD KEY `idx_message` (`message_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `connection_id` (`connection_id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`),
  ADD KEY `idx_notifications_created` (`created_at`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_posts_user` (`user_id`,`created_at`),
  ADD KEY `idx_post_status` (`status`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_post_comments` (`post_id`,`created_at`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_post_likes` (`post_id`,`user_id`);

--
-- Indexes for table `post_reports`
--
ALTER TABLE `post_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `post_shares`
--
ALTER TABLE `post_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_user_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `article_categories`
--
ALTER TABLE `article_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_connections`
--
ALTER TABLE `family_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_read_status`
--
ALTER TABLE `message_read_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `post_reports`
--
ALTER TABLE `post_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_shares`
--
ALTER TABLE `post_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_connections`
--
ALTER TABLE `family_connections`
  ADD CONSTRAINT `family_connections_ibfk_1` FOREIGN KEY (`family_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_connections_ibfk_2` FOREIGN KEY (`pregnant_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message_read_status`
--
ALTER TABLE `message_read_status`
  ADD CONSTRAINT `message_read_status_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_read_status_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`connection_id`) REFERENCES `family_connections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_shares`
--
ALTER TABLE `post_shares`
  ADD CONSTRAINT `post_shares_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  ADD CONSTRAINT `pregnancy_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

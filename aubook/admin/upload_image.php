<?php
session_start();

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Kiểm tra có file upload không
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit();
}

$file = $_FILES['file'];

// Kiểm tra lỗi upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload error: ' . $file['error']]);
    exit();
}

// Kiểm tra loại file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed']);
    exit();
}

// Kiểm tra kích thước file (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max 5MB']);
    exit();
}

// Tạo thư mục upload nếu chưa có
$upload_dir = '../uploads/articles/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Tạo tên file unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'img_' . time() . '_' . uniqid() . '.' . $extension;
$upload_path = $upload_dir . $filename;

// Upload file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Trả về URL của ảnh
    $location = '/aubook/uploads/articles/' . $filename;
    
    echo json_encode([
        'location' => $location,
        'success' => true
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
?>
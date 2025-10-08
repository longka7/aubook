<?php
// controllers/PostController.php

session_start();
require_once 'config/database.php';
require_once 'models/Post.php';
require_once 'models/Notification.php';

class PostController {
    private $db;
    private $post;
    private $notification;

    public function __construct() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->post = new Post($this->db);
        $this->notification = new Notification($this->db);
    }

    // Trang tin tức (newsfeed)
    public function newsfeed() {
        $posts = $this->post->getAllPosts($_SESSION['user_id']);
        require_once 'views/posts/newsfeed.php';
    }

    // Tạo bài đăng mới
    public function create() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $content = $_POST['content'] ?? '';
            $image_url = null;

            if(empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống']);
                exit;
            }

            // Xử lý upload ảnh
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $file_type = $_FILES['image']['type'];
                
                // Kiểm tra loại file
                if(!in_array($file_type, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file .png hoặc .jpg. File type: ' . $file_type]);
                    exit;
                }
                
                // Kiểm tra kích thước file (max 5MB)
                if($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    echo json_encode(['success' => false, 'message' => 'File ảnh không được vượt quá 5MB']);
                    exit;
                }

                $upload_dir = 'uploads/posts/';
                
                // Tạo thư mục nếu chưa có
                if(!file_exists($upload_dir)) {
                    if(!mkdir($upload_dir, 0777, true)) {
                        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục uploads']);
                        exit;
                    }
                }
                
                // Kiểm tra quyền ghi
                if(!is_writable($upload_dir)) {
                    echo json_encode(['success' => false, 'message' => 'Thư mục uploads không có quyền ghi']);
                    exit;
                }

                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                
                // Đảm bảo extension là png hoặc jpg
                if(!in_array(strtolower($file_extension), ['png', 'jpg', 'jpeg'])) {
                    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file .png hoặc .jpg']);
                    exit;
                }
                
                $new_filename = 'post_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = $upload_path;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể di chuyển file. Kiểm tra quyền thư mục uploads/posts/']);
                    exit;
                }
            }

            $this->post->user_id = $_SESSION['user_id'];
            $this->post->content = $content;
            $this->post->image_url = $image_url;

            if($this->post->create()) {
                echo json_encode(['success' => true, 'message' => 'Đã đăng bài']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể đăng bài']);
            }
        }
    }

    // Like bài đăng
    public function like() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';

            if(empty($post_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            if($this->post->likePost($post_id, $_SESSION['user_id'])) {
                // Tạo thông báo cho người đăng bài
                $post_owner = $this->post->getPostOwner($post_id);
                if($post_owner && $post_owner != $_SESSION['user_id']) {
                    $this->notification->user_id = $post_owner;
                    $this->notification->type = 'post_like';
                    $this->notification->from_user_id = $_SESSION['user_id'];
                    $this->notification->post_id = $post_id;
                    $this->notification->connection_id = null;
                    $this->notification->create();
                }

                echo json_encode(['success' => true, 'message' => 'Đã thích bài viết']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thích bài viết']);
            }
        }
    }

    // Unlike bài đăng
    public function unlike() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';

            if(empty($post_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            if($this->post->unlikePost($post_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Đã bỏ thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể bỏ thích']);
            }
        }
    }

    // Bình luận
    public function comment() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';
            $content = $_POST['content'] ?? '';

            if(empty($post_id) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            $comment_id = $this->post->addComment($post_id, $_SESSION['user_id'], $content);
            
            if($comment_id) {
                // Tạo thông báo cho người đăng bài
                $post_owner = $this->post->getPostOwner($post_id);
                if($post_owner && $post_owner != $_SESSION['user_id']) {
                    $this->notification->user_id = $post_owner;
                    $this->notification->type = 'post_comment';
                    $this->notification->from_user_id = $_SESSION['user_id'];
                    $this->notification->post_id = $post_id;
                    $this->notification->connection_id = null;
                    $this->notification->create();
                }

                echo json_encode(['success' => true, 'message' => 'Đã bình luận', 'comment_id' => $comment_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể bình luận']);
            }
        }
    }

    // Lấy bình luận
    public function getComments() {
        $post_id = $_GET['post_id'] ?? '';
        
        if(empty($post_id)) {
            echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
            exit;
        }

        $comments = $this->post->getComments($post_id);
        echo json_encode(['success' => true, 'comments' => $comments]);
    }

    // Chia sẻ
    public function share() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';

            if(empty($post_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            if($this->post->sharePost($post_id, $_SESSION['user_id'])) {
                // Tạo thông báo cho người đăng bài
                $post_owner = $this->post->getPostOwner($post_id);
                if($post_owner && $post_owner != $_SESSION['user_id']) {
                    $this->notification->user_id = $post_owner;
                    $this->notification->type = 'post_share';
                    $this->notification->from_user_id = $_SESSION['user_id'];
                    $this->notification->post_id = $post_id;
                    $this->notification->connection_id = null;
                    $this->notification->create();
                }

                echo json_encode(['success' => true, 'message' => 'Đã chia sẻ bài viết']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể chia sẻ']);
            }
        }
    }

    // Xóa bài đăng
    public function delete() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';

            if(empty($post_id)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            if($this->post->delete($post_id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa bài viết']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa bài viết']);
            }
        }
    }

    // Cập nhật bài đăng
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_id = $_POST['post_id'] ?? '';
            $content = $_POST['content'] ?? '';

            if(empty($post_id) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
                exit;
            }

            if($this->post->update($post_id, $_SESSION['user_id'], $content)) {
                echo json_encode(['success' => true, 'message' => 'Đã cập nhật bài viết']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật bài viết']);
            }
        }
    }
}
?>
<?php
// models/Post.php

class Post {
    private $conn;
    private $table = "posts";

    public $id;
    public $user_id;
    public $content;
    public $image_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo bài đăng mới
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, content=:content, image_url=:image_url";

        $stmt = $this->conn->prepare($query);

        $this->content = htmlspecialchars(strip_tags($this->content));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":image_url", $this->image_url);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Lấy tất cả bài đăng cho newsfeed
    public function getAllPosts($user_id, $limit = 20, $offset = 0) {
        // Lấy bài đăng từ user, các kết nối của user, và bài đăng công khai
        $query = "SELECT p.*, u.full_name, u.role, u.phone,
                  (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
                  (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count,
                  (SELECT COUNT(*) FROM post_shares WHERE post_id = p.id) as share_count,
                  (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = :user_id) as user_liked
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id
                  OR p.user_id IN (
                      SELECT pregnant_user_id FROM family_connections 
                      WHERE family_user_id = :user_id AND status = 'approved'
                  )
                  OR p.user_id IN (
                      SELECT family_user_id FROM family_connections 
                      WHERE pregnant_user_id = :user_id AND status = 'approved'
                  )
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bài đăng của một user cụ thể
    public function getPostsByUserId($user_id, $limit = 20) {
        $query = "SELECT p.*, u.full_name, u.role, u.phone,
                  (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
                  (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count,
                  (SELECT COUNT(*) FROM post_shares WHERE post_id = p.id) as share_count
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Like bài đăng
    public function likePost($post_id, $user_id) {
        $query = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Unlike bài đăng
    public function unlikePost($post_id, $user_id) {
        $query = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Thêm bình luận
    public function addComment($post_id, $user_id, $content) {
        $query = "INSERT INTO post_comments (post_id, user_id, content) 
                  VALUES (:post_id, :user_id, :content)";
        $stmt = $this->conn->prepare($query);
        $content = htmlspecialchars(strip_tags($content));
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":content", $content);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Lấy bình luận của bài đăng
    public function getComments($post_id) {
        $query = "SELECT c.*, u.full_name, u.role 
                  FROM post_comments c
                  INNER JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Chia sẻ bài đăng
    public function sharePost($post_id, $user_id) {
        $query = "INSERT INTO post_shares (post_id, user_id) VALUES (:post_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Lấy thông tin người đăng bài
    public function getPostOwner($post_id) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['user_id'] : null;
    }

    // Xóa bài đăng
    public function delete($post_id, $user_id) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Cập nhật bài đăng
    public function update($post_id, $user_id, $content) {
        $query = "UPDATE " . $this->table . " 
                  SET content = :content, updated_at = NOW() 
                  WHERE id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $content = htmlspecialchars(strip_tags($content));
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Lấy thông tin bài đăng
    public function getPostById($post_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
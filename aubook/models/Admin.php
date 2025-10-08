<?php
class Admin {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // ===== AUTHENTICATION =====
    
    public function login($username, $password) {
        $query = "SELECT * FROM admins WHERE (username = :username OR email = :username) AND status = 'active' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $admin['password'])) {
                // Cập nhật last_login
                $this->updateLastLogin($admin['id']);
                
                // Log hoạt động
                $this->logActivity($admin['id'], 'login', null, null, 'Admin đăng nhập thành công');
                
                return $admin;
            }
        }
        return false;
    }
    
    private function updateLastLogin($admin_id) {
        $query = "UPDATE admins SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $admin_id);
        $stmt->execute();
    }
    
    public function getAdminById($id) {
        $query = "SELECT * FROM admins WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ===== DASHBOARD STATISTICS =====
    
    public function getDashboardStats() {
        $query = "SELECT * FROM admin_dashboard_stats";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRecentUsers($limit = 10) {
        $query = "SELECT id, full_name, phone, role, status, created_at 
                  FROM users 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecentPosts($limit = 10) {
        $query = "SELECT p.*, u.full_name, u.phone 
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPendingReports($limit = 10) {
        $query = "SELECT pr.*, p.content as post_content, 
                         u.full_name as reporter_name, u.phone as reporter_phone
                  FROM post_reports pr
                  JOIN posts p ON pr.post_id = p.id
                  JOIN users u ON pr.reported_by = u.id
                  WHERE pr.status = 'pending'
                  ORDER BY pr.created_at DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== USER MANAGEMENT =====
    
    public function getAllUsers($search = '', $role = '', $status = '', $page = 1, $per_page = 20) {
        $offset = ($page - 1) * $per_page;
        
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(full_name LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($role)) {
            $where[] = "role = :role";
            $params[':role'] = $role;
        }
        
        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUsersCount($search = '', $role = '', $status = '') {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(full_name LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($role)) {
            $where[] = "role = :role";
            $params[':role'] = $role;
        }
        
        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT COUNT(*) as total FROM users WHERE $where_clause";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getUserById($user_id) {
        $query = "SELECT u.*, 
                         pi.conception_date, pi.due_date,
                         (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as total_posts,
                         (SELECT COUNT(*) FROM family_connections WHERE 
                          (family_user_id = u.id OR pregnant_user_id = u.id) 
                          AND status = 'approved') as total_connections
                  FROM users u
                  LEFT JOIN pregnancy_info pi ON u.id = pi.user_id
                  WHERE u.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function lockUser($user_id, $admin_id, $reason) {
        $query = "UPDATE users SET 
                  status = 'locked',
                  locked_at = NOW(),
                  locked_by = :admin_id,
                  locked_reason = :reason
                  WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':reason', $reason);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'lock_user', 'users', $user_id, "Khóa tài khoản: $reason");
            return true;
        }
        return false;
    }
    
    public function unlockUser($user_id, $admin_id) {
        $query = "UPDATE users SET 
                  status = 'active',
                  locked_at = NULL,
                  locked_by = NULL,
                  locked_reason = NULL
                  WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'unlock_user', 'users', $user_id, "Mở khóa tài khoản");
            return true;
        }
        return false;
    }
    
    public function deleteUser($user_id, $admin_id) {
        $query = "UPDATE users SET 
                  status = 'deleted',
                  deleted_at = NOW()
                  WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'delete_user', 'users', $user_id, "Xóa tài khoản");
            return true;
        }
        return false;
    }
    
    // ===== POST MANAGEMENT =====
    
    public function getAllPosts($search = '', $status = '', $page = 1, $per_page = 20) {
        $offset = ($page - 1) * $per_page;
        
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "p.content LIKE :search";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "p.status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT p.*, u.full_name, u.phone,
                         (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
                         (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE $where_clause
                  ORDER BY p.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPostsCount($search = '', $status = '') {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "content LIKE :search";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT COUNT(*) as total FROM posts WHERE $where_clause";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function approvePost($post_id, $admin_id) {
        $query = "UPDATE posts SET 
                  status = 'approved',
                  moderated_by = :admin_id,
                  moderated_at = NOW()
                  WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':admin_id', $admin_id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'approve_post', 'posts', $post_id, "Duyệt bài đăng");
            return true;
        }
        return false;
    }
    
    public function rejectPost($post_id, $admin_id, $reason) {
        $query = "UPDATE posts SET 
                  status = 'rejected',
                  moderated_by = :admin_id,
                  moderated_at = NOW(),
                  rejection_reason = :reason
                  WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':reason', $reason);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'reject_post', 'posts', $post_id, "Từ chối bài đăng: $reason");
            return true;
        }
        return false;
    }
    
    public function hidePost($post_id, $admin_id) {
        $query = "UPDATE posts SET status = 'hidden' WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'hide_post', 'posts', $post_id, "Ẩn bài đăng");
            return true;
        }
        return false;
    }
    
    public function deletePost($post_id, $admin_id) {
        $query = "DELETE FROM posts WHERE id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'delete_post', 'posts', $post_id, "Xóa bài đăng");
            return true;
        }
        return false;
    }
    
    // ===== ACTIVITY LOG =====
    
    public function logActivity($admin_id, $action, $table_name = null, $record_id = null, $description = null) {
        $query = "INSERT INTO admin_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent) 
                  VALUES (:admin_id, :action, :table_name, :record_id, :description, :ip_address, :user_agent)";
        $stmt = $this->conn->prepare($query);
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':table_name', $table_name);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':user_agent', $user_agent);
        
        return $stmt->execute();
    }
    
    public function getActivityLogs($limit = 50) {
        $query = "SELECT al.*, a.username, a.full_name
                  FROM admin_logs al
                  JOIN admins a ON al.admin_id = a.id
                  ORDER BY al.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== POST REPORTS MANAGEMENT =====
    
    public function getAllReports($status = '', $page = 1, $per_page = 20) {
        $offset = ($page - 1) * $per_page;
        
        $where = ["1=1"];
        $params = [];
        
        if (!empty($status)) {
            $where[] = "pr.status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT pr.*, 
                         p.content as post_content, p.image_url,
                         u.full_name as reporter_name, u.phone as reporter_phone,
                         pu.full_name as post_author_name, pu.phone as post_author_phone
                  FROM post_reports pr
                  JOIN posts p ON pr.post_id = p.id
                  LEFT JOIN users u ON pr.reported_by = u.id
                  JOIN users pu ON p.user_id = pu.id
                  WHERE $where_clause
                  ORDER BY pr.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getReportsCount($status = '') {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT COUNT(*) as total FROM post_reports WHERE $where_clause";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getReportById($report_id) {
        $query = "SELECT pr.*, 
                         p.content as post_content, p.image_url, p.status as post_status,
                         u.full_name as reporter_name, u.phone as reporter_phone,
                         pu.full_name as post_author_name, pu.phone as post_author_phone, pu.id as post_author_id
                  FROM post_reports pr
                  JOIN posts p ON pr.post_id = p.id
                  LEFT JOIN users u ON pr.reported_by = u.id
                  JOIN users pu ON p.user_id = pu.id
                  WHERE pr.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $report_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createReport($post_id, $reported_by, $reason, $description) {
        $query = "INSERT INTO post_reports (post_id, reported_by, reason, description, status, created_at)
                  VALUES (:post_id, :reported_by, :reason, :description, 'pending', NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':reported_by', $reported_by);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            // Tăng report_count của bài đăng
            $update_query = "UPDATE posts SET report_count = report_count + 1 WHERE id = :post_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':post_id', $post_id);
            $update_stmt->execute();
            
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    public function resolveReport($report_id, $admin_id, $status, $admin_note) {
        $query = "UPDATE post_reports SET 
                  status = :status,
                  reviewed_by = :admin_id,
                  reviewed_at = NOW(),
                  admin_note = :admin_note
                  WHERE id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $report_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':admin_note', $admin_note);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'resolve_report', 'post_reports', $report_id, "Xử lý báo cáo: $status");
            return true;
        }
        return false;
    }
    
    // ===== NOTIFICATION SYSTEM =====
    
    public function sendNotificationToUser($user_id, $type, $from_user_id, $connection_id = null, $post_id = null) {
        $query = "INSERT INTO notifications (user_id, type, from_user_id, connection_id, post_id, is_read, created_at)
                  VALUES (:user_id, :type, :from_user_id, :connection_id, :post_id, 0, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':from_user_id', $from_user_id);
        $stmt->bindParam(':connection_id', $connection_id);
        $stmt->bindParam(':post_id', $post_id);
        
        return $stmt->execute();
    }
    
    public function getPostById($post_id) {
        $query = "SELECT p.*, u.full_name, u.phone,
                         (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
                         (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count,
                         (SELECT COUNT(*) FROM post_reports WHERE post_id = p.id AND status = 'pending') as pending_reports
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ===== CATEGORY MANAGEMENT =====
    
    public function getAllCategories() {
        $query = "SELECT *, 
                         (SELECT COUNT(*) FROM articles WHERE category_id = article_categories.id) as article_count
                  FROM article_categories 
                  ORDER BY display_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryById($id) {
        $query = "SELECT * FROM article_categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createCategory($name, $slug, $description, $icon, $color, $display_order) {
        $query = "INSERT INTO article_categories (name, slug, description, icon, color, display_order, is_active)
                  VALUES (:name, :slug, :description, :icon, :color, :display_order, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':icon', $icon);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':display_order', $display_order);
        return $stmt->execute();
    }
    
    public function updateCategory($id, $name, $slug, $description, $icon, $color, $display_order, $is_active) {
        $query = "UPDATE article_categories SET 
                  name = :name,
                  slug = :slug,
                  description = :description,
                  icon = :icon,
                  color = :color,
                  display_order = :display_order,
                  is_active = :is_active
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':icon', $icon);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':display_order', $display_order);
        $stmt->bindParam(':is_active', $is_active);
        return $stmt->execute();
    }
    
    public function deleteCategory($id) {
        $query = "DELETE FROM article_categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // ===== ARTICLE MANAGEMENT =====
    
    public function getAllArticles($search = '', $category_id = '', $status = '', $page = 1, $per_page = 20) {
        $offset = ($page - 1) * $per_page;
        
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(a.title LIKE :search OR a.content LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($category_id)) {
            $where[] = "a.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        if (!empty($status)) {
            $where[] = "a.status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT a.*, c.name as category_name, c.color as category_color,
                         ad.full_name as author_name
                  FROM articles a
                  JOIN article_categories c ON a.category_id = c.id
                  JOIN admins ad ON a.author_id = ad.id
                  WHERE $where_clause
                  ORDER BY a.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getArticlesCount($search = '', $category_id = '', $status = '') {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(title LIKE :search OR content LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($category_id)) {
            $where[] = "category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT COUNT(*) as total FROM articles WHERE $where_clause";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getArticleById($id) {
        $query = "SELECT a.*, c.name as category_name
                  FROM articles a
                  JOIN article_categories c ON a.category_id = c.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createArticle($data) {
        $query = "INSERT INTO articles (category_id, title, slug, summary, content, thumbnail, author_id, status, published_at, is_featured, meta_title, meta_description)
                  VALUES (:category_id, :title, :slug, :summary, :content, :thumbnail, :author_id, :status, :published_at, :is_featured, :meta_title, :meta_description)";
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            $article_id = $this->conn->lastInsertId();
            $this->logActivity($data['author_id'], 'create_article', 'articles', $article_id, "Tạo bài viết: {$data['title']}");
            return $article_id;
        }
        return false;
    }
    
    public function updateArticle($id, $data) {
        $query = "UPDATE articles SET 
                  category_id = :category_id,
                  title = :title,
                  slug = :slug,
                  summary = :summary,
                  content = :content,
                  thumbnail = :thumbnail,
                  status = :status,
                  published_at = :published_at,
                  is_featured = :is_featured,
                  meta_title = :meta_title,
                  meta_description = :meta_description
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            $this->logActivity($data['author_id'], 'update_article', 'articles', $id, "Cập nhật bài viết: {$data['title']}");
            return true;
        }
        return false;
    }
    
    public function deleteArticle($id, $admin_id) {
        $query = "DELETE FROM articles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $this->logActivity($admin_id, 'delete_article', 'articles', $id, "Xóa bài viết");
            return true;
        }
        return false;
    }
    
    public function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return $slug;
    }
}
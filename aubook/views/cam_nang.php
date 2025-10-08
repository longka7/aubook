<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Lấy danh mục đang chọn
$category_slug = $_GET['category'] ?? '';

// Lấy tất cả danh mục
$query = "SELECT * FROM article_categories WHERE is_active = 1 ORDER BY display_order ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy bài viết
$where = "a.status = 'published'";
$params = [];

if ($category_slug) {
    $where .= " AND c.slug = :slug";
    $params[':slug'] = $category_slug;
}

$query = "SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color, c.icon as category_icon
          FROM articles a
          JOIN article_categories c ON a.category_id = c.id
          WHERE $where
          ORDER BY a.is_featured DESC, a.published_at DESC
          LIMIT 50";
$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy bài viết nổi bật
$query_featured = "SELECT a.*, c.name as category_name, c.color as category_color
                   FROM articles a
                   JOIN article_categories c ON a.category_id = c.id
                   WHERE a.status = 'published' AND a.is_featured = 1
                   ORDER BY a.published_at DESC
                   LIMIT 3";
$stmt_featured = $db->prepare($query_featured);
$stmt_featured->execute();
$featured_articles = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cẩm nang Mẹ Bầu - Aubook</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f9fafb;
            color: #1f2937;
        }
        
        .header {
            background: linear-gradient(135deg, #FF7B9C 0%, #FFC4D6 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .categories {
            display: flex;
            gap: 12px;
            padding: 30px 0;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .category-btn {
            padding: 12px 24px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .category-btn:hover {
            border-color: #FF7B9C;
            background: #fff1f5;
        }
        
        .category-btn.active {
            background: #FF7B9C;
            color: white;
            border-color: #FF7B9C;
        }
        
        .featured-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .article-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .article-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .article-thumbnail-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #FF7B9C, #FFC4D6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
        }
        
        .article-content {
            padding: 20px;
        }
        
        .article-category {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .article-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .article-summary {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: white;
            border-radius: 10px;
            text-decoration: none;
            color: #FF7B9C;
            font-weight: 500;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .back-link:hover {
            background: #fff1f5;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #9ca3af;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }
            
            .featured-grid,
            .articles-grid {
                grid-template-columns: 1fr;
            }
            
            .categories {
                padding: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Cẩm nang Mẹ Bầu</h1>
        <p>Kiến thức bổ ích cho hành trình làm mẹ</p>
    </div>
    
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="back-link">
                ← Quay về trang chủ
            </a>
        <?php endif; ?>
        
        <!-- Categories Filter -->
        <div class="categories">
            <a href="cam_nang.php" class="category-btn <?php echo !$category_slug ? 'active' : ''; ?>">
                📚 Tất cả
            </a>
            <?php foreach ($categories as $cat): ?>
                <a 
                    href="?category=<?php echo urlencode($cat['slug']); ?>" 
                    class="category-btn <?php echo $category_slug === $cat['slug'] ? 'active' : ''; ?>"
                >
                    <?php echo $cat['icon']; ?> <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Featured Articles -->
        <?php if (!$category_slug && count($featured_articles) > 0): ?>
            <div class="featured-section">
                <div class="section-title">
                    ⭐ Bài viết nổi bật
                </div>
                <div class="featured-grid">
                    <?php foreach ($featured_articles as $article): ?>
                        <a href="cam_nang_detail.php?id=<?php echo $article['id']; ?>" class="article-card">
                            <?php if ($article['thumbnail']): ?>
                                <img src="../<?php echo htmlspecialchars($article['thumbnail']); ?>" class="article-thumbnail" alt="<?php echo htmlspecialchars($article['title']); ?>">
                            <?php else: ?>
                                <div class="article-thumbnail-placeholder">
                                    <?php echo $article['category_icon'] ?? '📚'; ?>
                                </div>
                            <?php endif; ?>
                            <div class="article-content">
                                <span class="article-category" style="background: <?php echo $article['category_color']; ?>20; color: <?php echo $article['category_color']; ?>">
                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                </span>
                                <div class="article-title"><?php echo htmlspecialchars($article['title']); ?></div>
                                <?php if ($article['summary']): ?>
                                    <div class="article-summary"><?php echo htmlspecialchars($article['summary']); ?></div>
                                <?php endif; ?>
                                <div class="article-meta">
                                    <span>📅 <?php echo date('d/m/Y', strtotime($article['published_at'])); ?></span>
                                    <span>👁️ <?php echo number_format($article['view_count']); ?> lượt xem</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- All Articles -->
        <div class="section-title">
            <?php 
                if ($category_slug) {
                    $current_cat = array_filter($categories, fn($c) => $c['slug'] === $category_slug);
                    $current_cat = reset($current_cat);
                    echo $current_cat['icon'] . ' ' . htmlspecialchars($current_cat['name']);
                } else {
                    echo '📖 Tất cả bài viết';
                }
            ?>
        </div>
        
        <?php if (count($articles) > 0): ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                    <a href="cam_nang_detail.php?id=<?php echo $article['id']; ?>" class="article-card">
                        <?php if ($article['thumbnail']): ?>
                            <img src="../<?php echo htmlspecialchars($article['thumbnail']); ?>" class="article-thumbnail" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php else: ?>
                            <div class="article-thumbnail-placeholder">
                                <?php echo $article['category_icon'] ?? '📚'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="article-content">
                            <span class="article-category" style="background: <?php echo $article['category_color']; ?>20; color: <?php echo $article['category_color']; ?>">
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </span>
                            <div class="article-title"><?php echo htmlspecialchars($article['title']); ?></div>
                            <?php if ($article['summary']): ?>
                                <div class="article-summary"><?php echo htmlspecialchars(mb_substr($article['summary'], 0, 100)) . '...'; ?></div>
                            <?php endif; ?>
                            <div class="article-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($article['published_at'])); ?></span>
                                <span>👁️ <?php echo number_format($article['view_count']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3>Chưa có bài viết nào</h3>
                <p>Vui lòng quay lại sau</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
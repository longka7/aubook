<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($article_id === 0) {
    header('Location: cam_nang.php');
    exit();
}

// Lấy thông tin bài viết
$query = "SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color, c.icon as category_icon
          FROM articles a
          JOIN article_categories c ON a.category_id = c.id
          WHERE a.id = :id AND a.status = 'published'";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $article_id);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: cam_nang.php');
    exit();
}

// Tăng view count
$update_query = "UPDATE articles SET view_count = view_count + 1 WHERE id = :id";
$update_stmt = $db->prepare($update_query);
$update_stmt->bindParam(':id', $article_id);
$update_stmt->execute();

// Lấy bài viết liên quan
$related_query = "SELECT a.*, c.name as category_name, c.color as category_color
                  FROM articles a
                  JOIN article_categories c ON a.category_id = c.id
                  WHERE a.category_id = :category_id 
                  AND a.id != :id 
                  AND a.status = 'published'
                  ORDER BY a.published_at DESC
                  LIMIT 3";
$related_stmt = $db->prepare($related_query);
$related_stmt->bindParam(':category_id', $article['category_id']);
$related_stmt->bindParam(':id', $article_id);
$related_stmt->execute();
$related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['meta_title'] ?: $article['title']); ?> - Aubook</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['meta_description'] ?: $article['summary']); ?>">
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
            line-height: 1.7;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
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
            margin: 20px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .back-link:hover {
            background: #fff1f5;
        }
        
        .article-header {
            background: white;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .article-category {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .article-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #6b7280;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .article-thumbnail {
            width: 100%;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .article-content {
            background: white;
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .article-content h2 {
            font-size: 28px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #1f2937;
        }
        
        .article-content h3 {
            font-size: 22px;
            margin-top: 25px;
            margin-bottom: 12px;
            color: #374151;
        }
        
        .article-content p {
            margin-bottom: 16px;
            font-size: 16px;
        }
        
        .article-content ul,
        .article-content ol {
            margin-left: 20px;
            margin-bottom: 16px;
        }
        
        .article-content li {
            margin-bottom: 8px;
        }
        
        .article-content blockquote {
            border-left: 4px solid #FF7B9C;
            padding-left: 20px;
            margin: 20px 0;
            font-style: italic;
            color: #6b7280;
        }
        
        .related-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .related-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            display: block;
        }
        
        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .related-thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .related-content {
            padding: 15px;
        }
        
        .related-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .related-meta {
            font-size: 12px;
            color: #9ca3af;
        }
        
        @media (max-width: 768px) {
            .article-header {
                padding: 24px;
            }
            
            .article-title {
                font-size: 28px;
            }
            
            .article-content {
                padding: 24px;
            }
            
            .article-content h2 {
                font-size: 24px;
            }
            
            .article-content h3 {
                font-size: 20px;
            }
            
            .related-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="cam_nang.php?category=<?php echo urlencode($article['category_slug']); ?>" class="back-link">
            ← Quay lại <?php echo htmlspecialchars($article['category_name']); ?>
        </a>
        
        <!-- Article Header -->
        <div class="article-header">
            <span class="article-category" style="background: <?php echo $article['category_color']; ?>20; color: <?php echo $article['category_color']; ?>">
                <?php echo $article['category_icon']; ?> <?php echo htmlspecialchars($article['category_name']); ?>
            </span>
            
            <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            
            <?php if ($article['summary']): ?>
                <p style="font-size: 18px; color: #6b7280; margin-bottom: 0;">
                    <?php echo htmlspecialchars($article['summary']); ?>
                </p>
            <?php endif; ?>
            
            <div class="article-meta">
                <span>📅 <?php echo date('d/m/Y', strtotime($article['published_at'])); ?></span>
                <span>👁️ <?php echo number_format($article['view_count'] + 1); ?> lượt xem</span>
                <?php if ($article['is_featured']): ?>
                    <span>⭐ Nổi bật</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Article Thumbnail -->
        <?php if ($article['thumbnail']): ?>
            <img src="../<?php echo htmlspecialchars($article['thumbnail']); ?>" class="article-thumbnail" alt="<?php echo htmlspecialchars($article['title']); ?>">
        <?php endif; ?>
        
        <!-- Article Content -->
        <div class="article-content">
            <?php echo $article['content']; ?>
        </div>
        
        <!-- Related Articles -->
        <?php if (count($related_articles) > 0): ?>
            <div class="related-section">
                <div class="section-title">📚 Bài viết liên quan</div>
                <div class="related-grid">
                    <?php foreach ($related_articles as $related): ?>
                        <a href="cam_nang_detail.php?id=<?php echo $related['id']; ?>" class="related-card">
                            <?php if ($related['thumbnail']): ?>
                                <img src="../<?php echo htmlspecialchars($related['thumbnail']); ?>" class="related-thumbnail" alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <?php endif; ?>
                            <div class="related-content">
                                <div class="related-title"><?php echo htmlspecialchars($related['title']); ?></div>
                                <div class="related-meta">
                                    <?php echo date('d/m/Y', strtotime($related['published_at'])); ?> • 
                                    <?php echo number_format($related['view_count']); ?> lượt xem
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
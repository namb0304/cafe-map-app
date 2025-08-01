<?php
// ファイルパス: /cafe_details.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// DB接続ファイルを読み込む
include 'includes/db_connect.php';

// GETパラメータからカフェIDを取得し、数字であるか検証
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("エラー: 無効なカフェIDです。");
}
$cafe_id = $_GET['id'];

// カフェの基本情報を取得
$sql_cafe = 'SELECT * FROM tokyo_cafes WHERE id = $1';
pg_prepare($dbconn, "get_cafe_details", $sql_cafe);
$result_cafe = pg_execute($dbconn, "get_cafe_details", array($cafe_id));
$cafe = pg_fetch_assoc($result_cafe);
if (!$cafe) {
    die("エラー: 指定されたカフェが見つかりません。");
}

// このカフェのレビュー一覧をユーザー名とともに取得
$sql_reviews = 'SELECT r.rating, r.congestion_level, r.comment_text, r.created_at, u.username 
                FROM reviews r JOIN users u ON r.user_id = u.user_id 
                WHERE r.cafe_id = $1 ORDER BY r.created_at DESC';
pg_prepare($dbconn, "get_reviews", $sql_reviews);
$result_reviews = pg_execute($dbconn, "get_reviews", array($cafe_id));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($cafe['name']); ?> の詳細</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <a href="index.php" class="back-link">« マップに戻る</a>
        <div class="cafe-header">
            <h1><?php echo htmlspecialchars($cafe['name']); ?></h1>
            <p class="address"><?php echo htmlspecialchars($cafe['address']); ?></p>
            <?php if (!empty($cafe['url'])): ?>
                <a href="<?php echo htmlspecialchars($cafe['url']); ?>" class="official-site-btn" target="_blank" rel="noopener noreferrer">公式サイトを見る</a>
            <?php endif; ?>
        </div>
        <hr>
        
        <div class="review-form-section">
            <h2>レビューを投稿する</h2>
            <form action="process/add_review.php" method="POST" class="form-area">
                <input type="hidden" name="cafe_id" value="<?php echo $cafe_id; ?>">
                <div class="form-group"><label>評価（星）:</label>
                    <div class="rating-selector">
                        <input type="radio" id="star5" name="rating" value="5" /><label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1" required /><label for="star1">★</label>
                    </div>
                </div>
                <div class="form-group"><label>混雑度:</label>
                    <select name="congestion_level" required><option value="">選択</option><option value="1">空き</option><option value="2">やや空き</option><option value="3">普通</option><option value="4">混雑</option><option value="5">満席</option></select>
                </div>
                <div class="form-group">
                    <label for="comment_text">コメント:</label>
                    <textarea name="comment_text" id="comment_text" rows="4" placeholder="例：ここのチーズケーキは絶品！"></textarea>
                </div>
                <button type="submit">投稿する</button>
            </form>
        </div>
        <hr>
        
        <div class="reviews-section">
            <h2>みんなのレビュー</h2>
            <?php if (pg_num_rows($result_reviews) > 0): ?>
                <?php while ($review = pg_fetch_assoc($result_reviews)): ?>
                    <div class="review-card">
                        <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> <span class="rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span></p>
                        <p>混雑度: <?php echo ['','空き','やや空き','普通','混雑','満席'][$review['congestion_level']] ?? '未設定'; ?></p>
                        <p class="comment-text"><?php echo nl2br(htmlspecialchars($review['comment_text'])); ?></p>
                        <p class="review-date"><?php echo date('Y年m月d日 H:i', strtotime($review['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>このカフェにはまだレビューがありません。</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
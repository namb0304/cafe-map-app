<?php
// ファイルパス: /process/add_review.php

session_start();
// 1階層上の/includes/db_connect.phpを読み込む
include '../includes/db_connect.php';

// 未ログインまたはPOST以外のリクエストは弾く
if (!isset($_SESSION['user_id'])) { 
    http_response_code(403); // Forbidden
    die("ログインが必要です。");
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405); // Method Not Allowed
    die("無効なリクエストです。");
}

// POSTされたデータを取得
$cafe_id = $_POST['cafe_id'] ?? null;
$user_id = $_SESSION['user_id'];
$rating = $_POST['rating'] ?? null;
$congestion_level = $_POST['congestion_level'] ?? null;
$comment_text = $_POST['comment_text'] ?? '';

// 必須項目が空でないかチェック
if (empty($cafe_id) || empty($rating) || empty($congestion_level)) {
    die("必須項目が不足しています。");
}

try {
    // データベースにレビューを挿入
    $sql = 'INSERT INTO reviews (cafe_id, user_id, rating, congestion_level, comment_text, created_at) VALUES ($1, $2, $3, $4, $5, NOW())';
    pg_prepare($dbconn, "insert_review", $sql);
    $result = pg_execute($dbconn, "insert_review", array($cafe_id, $user_id, $rating, $congestion_level, $comment_text));

    if (!$result) {
        throw new Exception(pg_last_error($dbconn));
    }

    // 処理完了後、元の詳細ページにリダイレクト
    // 1階層上のcafe_details.phpにリダイレクトするため、'../' をつける
    header("Location: ../cafe_details.php?id=" . urlencode($cafe_id) . "&status=success");
    exit;

} catch (Exception $e) {
    // エラーが発生した場合はメッセージを表示
    die("データベースへの保存に失敗しました: " . $e->getMessage());
} finally {
    pg_close($dbconn);
}
?>
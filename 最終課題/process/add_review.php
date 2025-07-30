<?php
// --- 最終課題/process/add_review.php (新規作成) ---

session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) { die("ログインが必要です。"); }
if ($_SERVER["REQUEST_METHOD"] != "POST") { die("無効なリクエスト"); }

$cafe_id = $_POST['cafe_id'];
$user_id = $_SESSION['user_id'];
$rating = $_POST['rating'];
$congestion_level = $_POST['congestion_level'];
$comment_text = $_POST['comment_text'] ?? '';

if (empty($cafe_id) || empty($rating) || empty($congestion_level)) { die("必須項目が不足しています。"); }

$sql = 'INSERT INTO reviews (cafe_id, user_id, rating, congestion_level, comment_text) VALUES ($1, $2, $3, $4, $5)';
pg_prepare($dbconn, "insert_review", $sql);
$result = pg_execute($dbconn, "insert_review", array($cafe_id, $user_id, $rating, $congestion_level, $comment_text));

if (!$result) { die("データベースへの保存に失敗しました: " . pg_last_error()); }

pg_close($dbconn);
header("Location: ../cafe_details.php?id=" . urlencode($cafe_id));
exit;
?>
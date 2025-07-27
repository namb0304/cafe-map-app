<?php
// --- 最終課題/index.php (最終修正版) ---

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: welcome.html"); // ログインしていなければ概要ページへ
    exit;
}

// config.phpを読み込んでAPIキーを使えるようにする
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>周辺カフェ検索 (Google Maps版)</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<!-- bodyタグに no-scroll クラスを追加して、このページだけスクロールを禁止 -->
<body class="no-scroll">
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <div id="map-container">
            <div id="map"></div>
            <button id="search-this-area-btn" class="map-button" style="display: none;">このエリアで再検索</button>
        </div>
        <div id="list-container">
            <h1>カフェ一覧</h1>
            <div id="cafe-list-content" class="cafe-list">
                <p>地図を動かして「このエリアで再検索」ボタンを押してください。</p>
            </div>
        </div>
    </div>

    <script src="statics/js/main.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer></script>
</body>
</html>
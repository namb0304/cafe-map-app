<?php
// --- 最終課題/index.php (Google Maps版) ---

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
<body>
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
    <!-- Google Maps APIを、config.phpから取得したキーを使って安全に読み込む -->
    <!-- &callback=initMap は、APIの読み込み完了後にinitMapという関数を実行するおまじない -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer></script>
</body>
</html>
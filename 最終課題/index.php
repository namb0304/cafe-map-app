<?php
// --- 最終課題/index.php (AI検索窓を追加) ---

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: welcome.html");
    exit;
}
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>周辺カフェ検索 (AI版)</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body class="no-scroll">
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <div id="map-container">
            <div id="map"></div>
            <button id="search-this-area-btn" class="map-button" style="display: none;">このエリアで再検索</button>
        </div>
        <div id="list-container">
            <!-- ▼▼▼ AI検索フォームを追加 ▼▼▼ -->
            <div class="ai-search-box">
                <form id="ai-search-form">
                    <input type="text" id="ai-prompt" name="prompt" placeholder="例：静かで作業しやすいカフェは？" required>
                    <button type="submit">AIに聞く</button>
                </form>
            </div>
            <h1>カフェ一覧</h1>
            <div id="cafe-list-content" class="cafe-list">
                <p>地図を動かすか、AIに質問してカフェを探してください。</p>
            </div>
        </div>
    </div>

    <script src="statics/js/main.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer></script>
</body>
</html>
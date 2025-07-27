<?php
// --- 最終課題/index.php ---

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db_connect.php';

// tokyo_cafesテーブルから全件取得
$query = "SELECT id, name, address, latitude, longitude FROM tokyo_cafes ORDER BY name;";
$result = pg_query($dbconn, $query);
$cafes_data = pg_fetch_all($result);
if ($cafes_data === false) $cafes_data = [];

// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
// ★ ここに、あなたが取得したGoogle Maps APIキーを貼り付けてください ★
$google_maps_api_key = "YOUR_API_KEY_HERE";
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カフェマップ</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div id="map"></div>

    <script>
        // PHPからJavaScriptへカフェデータを渡す
        const cafes = <?php echo json_encode($cafes_data); ?>;
    </script>
    <script src="statics/js/main.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&callback=initMap" async defer></script>
</body>
</html>
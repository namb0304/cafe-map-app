<?php
// --- 最終課題/api/get_cafes.php (修正版) ---

header('Content-Type: application/json');
include '../includes/db_connect.php';

$sw_lat = $_GET['sw_lat'] ?? null;
$sw_lng = $_GET['sw_lng'] ?? null;
$ne_lat = $_GET['ne_lat'] ?? null;
$ne_lng = $_GET['ne_lng'] ?? null;

if (!$sw_lat || !$sw_lng || !$ne_lat || !$ne_lng) {
    http_response_code(400);
    echo json_encode(['error' => '必要なパラメータが不足しています。']);
    exit;
}

// urlカラムも取得するように修正
$sql = "
    SELECT id, name, address, latitude, longitude, url
    FROM tokyo_cafes
    WHERE geom && ST_MakeEnvelope($1, $2, $3, $4, 4326)
    ORDER BY name;
";

$result = pg_prepare($dbconn, "find_cafes_in_bounds", $sql);
$result = pg_execute($dbconn, "find_cafes_in_bounds", array($sw_lng, $sw_lat, $ne_lng, $ne_lat));

$cafes = pg_fetch_all($result);
if ($cafes === false) $cafes = [];

echo json_encode($cafes);
pg_close($dbconn);
?>
<?php
// ファイルパス: /api/get_cafes.php

// 1階層上の/includes/db_connect.phpを読み込む
require_once '../includes/db_connect.php'; 

// エラーレポートを有効化（開発中に問題を発見しやすくするため）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// このファイルが返すデータはJSON形式であることをブラウザに伝える
header('Content-Type: application/json; charset=utf-8');

// JavaScriptからのGETパラメータを検証
if (!isset($_GET['sw_lat'], $_GET['sw_lng'], $_GET['ne_lat'], $_GET['ne_lng'])) {
    // パラメータが足りない場合はエラーを返して処理を終了
    http_response_code(400); // Bad Request
    echo json_encode(['error' => '地図の範囲パラメータが不足しています。']);
    exit;
}

// パラメータを安全な数値（浮動小数点数）に変換
$sw_lat = (float)$_GET['sw_lat'];
$sw_lng = (float)$_GET['sw_lng'];
$ne_lat = (float)$_GET['ne_lat'];
$ne_lng = (float)$_GET['ne_lng'];

try {
    // SQLインジェクションを防ぐためにプリペアドステートメントを使用
    // マップの表示範囲（緯度・経度）に含まれるカフェを検索するSQL
    $sql = "SELECT id, name, url, address, latitude, longitude FROM tokyo_cafes 
            WHERE latitude BETWEEN $1 AND $2 AND longitude BETWEEN $3 AND $4";
    
    pg_prepare($dbconn, "get_cafes_in_bounds", $sql);
    $result = pg_execute($dbconn, "get_cafes_in_bounds", array($sw_lat, $ne_lat, $sw_lng, $ne_lng));

    if (!$result) {
        throw new Exception('データベースクエリの実行に失敗しました。');
    }

    // クエリ結果をすべて連想配列として取得
    $cafes = pg_fetch_all($result);
    
    // 結果が0件の場合、pg_fetch_allはfalseを返すので、空の配列に変換する
    if ($cafes === false) {
        $cafes = [];
    }

    // カフェの情報をJSON形式で出力する
    echo json_encode($cafes);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'サーバー内部でエラーが発生しました: ' . $e->getMessage()]);
} finally {
    // データベース接続を閉じる
    pg_close($dbconn);
}
?>
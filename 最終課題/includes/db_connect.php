<?php
// --- 最終課題/includes/db_connect.php (変更なし) ---

// まず、秘密情報が書かれた設定ファイルを読み込む
require_once 'config.php';

// config.phpで定義した定数を使って、安全に接続する
$dbconn = pg_connect("host=" . DB_HOST . " user=" . DB_USER . " password=" . DB_PASSWORD . " dbname=" . DB_NAME)
    or die('データベースに接続できませんでした: ' . pg_last_error());
?>
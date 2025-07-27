<?php
// --- 最終課題/includes/db_connect.php ---

// データベース接続情報
$host = "localhost";
$user = "s2422051";
$password = "MCB13eAR";
$dbname = "s2422051";

// PostgreSQLへの接続
$dbconn = pg_connect("host=$host user=$user password=$password dbname=$dbname")
    or die('データベースに接続できませんでした: ' . pg_last_error());
?>
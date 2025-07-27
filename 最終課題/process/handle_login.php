<?php
// --- 最終課題/process/handle_login.php ---

session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") { die("無効なリクエスト"); }

$username = $_POST['username'];
$password = $_POST['password'];

$sql = 'SELECT user_id, username, password_hash FROM users WHERE username = $1';
pg_prepare($dbconn, "find_user", $sql);
$result = pg_execute($dbconn, "find_user", array($username));

if (pg_num_rows($result) == 1) {
    $user_data = pg_fetch_assoc($result);
    if (password_verify($password, $user_data['password_hash'])) {
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['username'] = $user_data['username'];
        pg_close($dbconn);
        header("Location: ../index.php");
        exit;
    }
}

pg_close($dbconn);
header("Location: ../login.php?error=1");
exit;
?>
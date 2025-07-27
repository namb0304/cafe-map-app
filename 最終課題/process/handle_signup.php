<?php
// --- 最終課題/process/handle_signup.php ---

include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") { die("無効なリクエスト"); }

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

if (empty($username) || empty($email) || empty($password)) {
    header("Location: ../signup.php?error=empty");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = 'INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3)';
pg_prepare($dbconn, "insert_user", $sql);
$result = @pg_execute($dbconn, "insert_user", array($username, $email, $password_hash));

if (!$result) {
    $error_message = pg_last_error($dbconn);
    if (strpos($error_message, 'users_username_key') !== false) {
        header("Location: ../signup.php?error=username_taken");
    } elseif (strpos($error_message, 'users_email_key') !== false) {
        header("Location: ../signup.php?error=email_taken");
    } else {
        header("Location: ../signup.php?error=db_error");
    }
    exit;
}

pg_close($dbconn);
header("Location: ../login.php?status=success");
exit;
?>
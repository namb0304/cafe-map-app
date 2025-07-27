<?php
// --- 最終課題/process/logout.php ---

session_start();
$_SESSION = array();
session_destroy();
header("Location: ../login.php");
exit;
?>
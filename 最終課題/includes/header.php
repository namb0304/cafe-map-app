<?php
// --- 最終課題/includes/header.php ---

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="page-header">
    <div class="header-content">
        <a href="index.php" class="header-logo">Tokyo Cafe App</a>
        <nav>
            <?php if (isset($_SESSION['username'])): ?>
                <span>ようこそ、<?php echo htmlspecialchars($_SESSION['username']); ?> さん</span>
                <a href="process/logout.php">ログアウト</a>
            <?php else: ?>
                <a href="login.php">ログイン</a>
                <a href="signup.php">新規登録</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php
// --- 最終課題/signup.php ---

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body>
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <h1>ようこそ！</h1>
            <p class="auth-subheading">アカウントを作成して始めましょう</p>
            
            <?php if (isset($_GET['error'])): ?>
                <p class="message error">
                    <?php
                        if ($_GET['error'] === 'username_taken') {
                            echo 'そのユーザー名は既に使用されています。';
                        } elseif ($_GET['error'] === 'email_taken') {
                            echo 'そのメールアドレスは既に使用されています。';
                        } else {
                            echo '登録中にエラーが発生しました。';
                        }
                    ?>
                </p>
            <?php endif; ?>

            <form action="process/handle_signup.php" method="POST" class="form-area">
                <div class="form-group">
                    <label for="username">ユーザー名:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">登録する</button>
            </form>
            <p class="auth-link">すでにアカウントをお持ちですか？ <a href="login.php">ログインはこちら</a></p>
        </div>
    </div>
</body>
</html>
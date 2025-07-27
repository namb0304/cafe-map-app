<?php
// --- 最終課題/login.php ---

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
    <title>ログイン</title>
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body>
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <h1>おかえりなさい！</h1>
            <p class="auth-subheading">ログインしてあなたの気分にあったカフェを見つけましょう</p>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <p class="message success">ユーザー登録が完了しました。ログインしてください。</p>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <p class="message error">ユーザー名またはパスワードが間違っています。</p>
            <?php endif; ?>

            <form action="process/handle_login.php" method="POST" class="form-area">
                <div class="form-group">
                    <label for="username">ユーザー名:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">ログイン</button>
            </form>
            <p class="auth-link">アカウントをお持ちでないですか？ <a href="signup.php">新規登録はこちら</a></p>
        </div>
    </div>
</body>
</html>
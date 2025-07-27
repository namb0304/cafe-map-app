<?php
// --- 最終課題/signup.php (修正版) ---

session_start();
// ログイン済みの場合はメインページへリダイレクト
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// エラーメッセージを格納する変数を初期化
$error_display_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'username_taken':
            $error_display_message = 'そのユーザー名は既に使用されています。';
            break;
        case 'email_taken':
            $error_display_message = 'そのメールアドレスは既に使用されています。';
            break;
        case 'empty':
            $error_display_message = 'すべての項目を入力してください。';
            break;
        default:
            $error_display_message = '不明なエラーが発生しました。';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録</title>
    <!-- CSSファイルのパスを正しく指定 -->
    <link rel="stylesheet" href="statics/css/style.css">
</head>
<body>
    <div class="auth-page-wrapper">
        <div class="auth-container">
            <h1>ようこそ！</h1>
            <p class="auth-subheading">アカウントを作成して始めましょう</p>

            <?php if (!empty($error_display_message)): ?>
                <p class="message error"><?php echo htmlspecialchars($error_display_message); ?></p>
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
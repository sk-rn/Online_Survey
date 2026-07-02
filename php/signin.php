<?php
require_once 'db.php';
require_once 'auth.php';
require_once __DIR__ . '/error.php';
require_once 'security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 既に認証済みの場合は標準のページ（例: survey_form.php や index.php）へ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// トークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error_message = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (empty($posted_token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $posted_token)) {
        renderError('403 Forbidden: 不正なリクエストです。', 403, 'app', 'WARNING');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($username !== '' && $password !== '') {
        try {
            $user = get_user_by_name($username);
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['account_name'];
                $_SESSION['last_acc'] = time();

                $redirect_url = $_SESSION['return_to'] ?? 'index.php';
                unset($_SESSION['return_to']);
                header('Location: ' . $redirect_url);
                exit;
            }

            $error_message = 'ユーザー名またはパスワードが正しくありません。';
        } catch (Throwable $e) {
            renderError('ログイン処理中にエラーが発生しました。', 500, 'db', 'ERROR', $e, 'Signin Error');
        }
    } else {
        $error_message = 'ユーザー名とパスワードを入力してください。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1E2D5A] flex items-center justify-center min-h-screen">
    <div class="bg-[#24376F] p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold mb-8 text-center text-white">ログイン</h1>

        <?php if ($error_message !== ''): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="signin.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div>
                <label class="block text-white font-medium mb-2">ユーザー名</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border border-white/20 bg-[#1E2D5A] text-white placeholder-gray-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">パスワード</label>
                <input type="password" name="password" class="w-full rounded-lg border border-white/20 bg-[#1E2D5A] text-white placeholder-gray-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"required>
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-300 shadow-lg">
                ログイン
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                アカウントをお持ちでないですか？ 
                <a href="signup.php" class="text-blue-500 hover:underline">新規登録</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php
// signout.php
// サインアウト処理：POST + CSRF を検証してセッションを破棄しトップページへリダイレクト。

require_once 'auth.php';
require_once __DIR__ . '/error.php';

if (session_status() === PHP_SESSION_NONE) {
	start_sess();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	renderError('不正なリクエストです。', 405, 'app', 'WARNING');
}

$posted_token = $_POST['csrf_token'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';

if ($posted_token === '' || $session_token === '' || !hash_equals($session_token, $posted_token)) {
	renderError('不正なリクエストです。もう一度お試しください。', 403, 'app', 'WARNING');
}

// auth.php の del_sess() を呼んでセッションを完全に破棄する
del_sess();

// トップページへリダイレクト
header('Location: index.php');
exit;

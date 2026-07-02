<?php
require_once "db.php";
require_once "auth.php";
require_once __DIR__ . '/error.php';
start_sess();

function unsubscription(){
    if (empty($_SESSION['user_id'])) {
        renderError('ログインが必要です。', 403, 'auth', 'WARNING');
    }

    $deleted = delete_user((int)$_SESSION["user_id"]);
    if ($deleted !== true) {
        renderError('退会処理に失敗しました。', 500, 'db', 'ERROR');
    }

    del_sess();
    header("Location: index.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["back"])){
        header("Location: index.php");
    }elseif(isset($_POST["unsubscripte"])){
        unsubscription();
    } else {
        renderError('不正なリクエストです。', 400, 'app', 'WARNING');
    }
}
?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='../css/unsubscripte.css'>
    <link rel="stylesheet" href="../css/footer.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <script src='https://cdn.tailwindcss.com'></script>
    <title>退会</title>
</head>
<?php include "header.php"?>
<body>
    <main>
        <p>本当に退会しますか？</p>
        <form method="post" action="">
            <button type="submit" name="back" id="back">戻る</button>
            <button type="submit" name="unsubscripte" id="unsbscripte">退会</button>
        </form>
    </main>
    <?php require_once "footer.php" ?>
</body>
</html>
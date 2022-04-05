<?php

include('config.php');
include('header.php');

if (isset($_SESSION['account']) && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ? AND `type` = "admin"');
    $stat->bind_param('s', $_SESSION['account']);
    $stat->execute();
    $result = $stat->get_result()->fetch_assoc();

    if (!isset($result['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $stat = $link->prepare('SELECT * FROM settings WHERE `key` = "title"');
        $stat->execute();
        $title = $stat->get_result()->fetch_assoc()['value'];
    }
} elseif (isset($_SESSION['account']) && isset($_POST['title']) && $_SERVER['REQUEST_METHOD'] === 'POST') { // edit
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ? AND `type` = "admin"');
    $stat->bind_param('s', $_SESSION['account']);
    $stat->execute();
    $result = $stat->get_result()->fetch_assoc();

    if (!isset($result['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $stat = $link->prepare('UPDATE settings SET `value` = ? WHERE `key` = "title"');
        $stat->bind_param('s', $_POST['title']);
        $stat->execute();
    }
    $title = $_POST['title'];
} else {
    $message = REDIRECT_TO_INDEX;
}

?>
<?= @$message; ?>
<div class="container">
    <h2>更改網站標題</h2>
    <form action="" method="POST">
        <input type="text" name="title" value="<?= $title; ?>">
        <input type="submit" value="送出">
    </form>
</div>
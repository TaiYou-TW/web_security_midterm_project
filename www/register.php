<?php

include('config.php');
include('header.php');

if (isset($_SESSION['account'])) { // redirect
    $message = REDIRECT_TO_INDEX;
} elseif (isset($_POST['account']) && isset($_POST['password'])) { // register
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
    $stat->bind_param('s', $_POST['account']);
    $stat->execute();
    $result = $stat->get_result()->fetch_all();
    $number = count($result);
    if ($number > 0) { // block duplicate
        $message = "帳號已存在";
    } else {
        $stat = $link->prepare('INSERT INTO users (`account`, `password`) VALUES (?, ?)');
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stat->bind_param('ss', $_POST['account'], $password);
        $stat->execute();

        $_SESSION['account'] = $_POST['account'];
        $message = REDIRECT_TO_INDEX;
    }
}

?>

<div class="container">
    <h2>註冊</h2>
    <?= @$message; ?>
    <form action="" method="POST">
        <input type="text" name="account" id=""><br><br>
        <input type="password" name="password" id=""><br><br>
        <input type="submit" value="送出">
    </form>
</div>
<?php

include('header.php');

if (isLoggedIn()) { // redirect
    $message = REDIRECT_TO_INDEX;
} elseif (isset($_POST['account']) && isset($_POST['password'])) { // login
    if (login($_POST['account'], $_POST['password'])) {
        setAccountSession($_POST['account']);
        $message = REDIRECT_TO_INDEX;
    } else {
        $message = '登入失敗';
    }
}

?>

<div class="container">
    <h2>登入</h2>
    <?= @$message; ?>
    <form action="" method="POST">
        <input type="text" name="account" id=""><br><br>
        <input type="password" name="password" id=""><br><br>
        <input type="submit" value="送出">
    </form>
</div>
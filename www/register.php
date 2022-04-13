<?php

include('header.php');

if (isset($_SESSION['account'])) { // redirect
    $message = REDIRECT_TO_INDEX;
} elseif (isset($_POST['account']) && isset($_POST['password'])) { // register
    if (!register($_POST['account'], $_POST['password'])) {
        $message = "註冊失敗";
    } else {
        setAccountSession($_POST['account']);
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
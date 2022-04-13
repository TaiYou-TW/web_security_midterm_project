<?php

include('header.php');

if (isLoggedInAdmin() && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    $title = getTitle();
} elseif (isLoggedInAdmin() && isset($_POST['title']) && $_SERVER['REQUEST_METHOD'] === 'POST') { // edit
    updateTitle($_POST['title']);
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
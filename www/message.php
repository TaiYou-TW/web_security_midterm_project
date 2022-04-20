<?php

include('header.php');

if (isLoggedIn() && isset($_POST['content']) && $_SERVER['REQUEST_METHOD'] === 'POST') { // store
    if (isset($_FILES['file']) && $_FILES["file"]["tmp_name"] !== "") { // upload
        $uploadfile = generateUploadFileName($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            insertMessage($_POST['content'], $uploadfile);
        }
    } else {
        insertMessage($_POST['content']);
    }
    $message = REDIRECT_TO_INDEX;
} elseif (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    if (!isIdLegal($_GET['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $article = getMessage($_GET['id']);

        if (!$article['id']) {
            $message = REDIRECT_TO_INDEX;
        }
    }
} elseif (isLoggedIn() && isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_method'] === 'DELETE') { // delete
    if (isIdLegal($_GET['id']) && isMyMessage($_GET['id'])) {
        deleteMessage($_GET['id']);
    }
    $message = REDIRECT_TO_INDEX;
} else {
    $message = REDIRECT_TO_INDEX;
}
?>

<?= @$message; ?>

<?php if (isset($article)) : ?>
    <div class="container">
        <h2>留言</h2>
        <hr>
        <?php if (isMyMessage($article[0])) : ?>
            <form method="POST" action="message.php?id=<?= $article[0] ?>">
                <div class="mb-2 col-md-1">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="form-control me-2 btn btn-danger" type="submit">Delete</button>
                </div>
            </form>
        <?php endif; ?>

        <h3>
            作者：
            <?php if ($article['avatar_path']) : ?>
                <img class="avatar" src="<?= substr($article['avatar_path'], 14); ?>" alt="avatar">
            <?php else : ?>
                <img class="avatar" src="avatars/default.png" alt="avatar">
            <?php endif; ?>
            <?= $article['account']; ?>
        </h3>
        <h3>內容：</h3>
        <span id="content"><?= nl2br($article['content']); ?></span>
        <br><br>

        <?php if (isset($article['file_path'])) : ?>
            <h3><a href="file.php?id=<?= $article[0]; ?>">下載附件</a></h3>
        <?php endif; ?>

        <h3>留言時間：<?= $article[5]; ?></h3>
    </div>
<?php endif; ?>

<script src="/js/render.js"></script>
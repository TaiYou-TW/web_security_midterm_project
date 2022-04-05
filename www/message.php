<?php

include('config.php');
include('header.php');

if (isset($_SESSION['account']) && isset($_POST['content']) && $_SERVER['REQUEST_METHOD'] === 'POST') { // store
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
    $stat->bind_param('s', $_SESSION['account']);
    $stat->execute();
    $result = $stat->get_result()->fetch_assoc();

    if (!isset($result['id'])) {
        unset($_SESSION['account']);
    } else {
        // upload
        if (isset($_FILES['file']) && $_FILES["file"]["tmp_name"] !== "") {
            $randomName = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 5);
            $splitName = explode('.', $_FILES['file']['name']);
            $ext = end($splitName);
            $uploadfile = UPLOAD_DIR . time() . $randomName . '.' . $ext;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                // insert
                $stat = $link->prepare('INSERT INTO messages (`content`, `file_path`, `by_user_id`) VALUES (?, ?, ?)');
                $content = htmlspecialchars($_POST['content']);
                $authorId = $result['id'];
                $stat->bind_param('ssi', $content, $uploadfile, $authorId);
                $stat->execute();
            }
        } else {
            // insert
            $stat = $link->prepare('INSERT INTO messages (`content`, `by_user_id`) VALUES (?, ?)');
            $content = htmlspecialchars($_POST['content']);
            $authorId = $result['id'];
            $stat->bind_param('si', $content, $authorId);
            $stat->execute();
        }
    }

    // $message = REDIRECT_TO_INDEX;
} elseif (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    if (!preg_match('/^\d*$/', $_GET['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ?');
        $stat->bind_param('i', $_GET['id']);
        $stat->execute();
        $article = $stat->get_result()->fetch_all(MYSQLI_BOTH)[0];

        if (!isset($article['id'])) {
            $message = REDIRECT_TO_INDEX;
        }
    }
} elseif (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_method'] === 'DELETE') { // delete
    if (preg_match('/^\d*$/', $_GET['id'])) {
        $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ?');
        $stat->bind_param('i', $_GET['id']);
        $stat->execute();
        $article = $stat->get_result()->fetch_all(MYSQLI_BOTH)[0];

        if (isset($article['id']) && $article['account'] === $_SESSION['account']) {
            $stat = $link->prepare('DELETE FROM messages WHERE `id` = ?');
            $stat->bind_param('i', $_GET['id']);
            $stat->execute();
        }
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

        <?php if (isset($_SESSION['account']) && $article['account'] === $_SESSION['account']) : ?>
            <form method="POST" action="message.php?id=<?= $article[0] ?>">
                <div class="mb-2 col-md-1">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="form-control me-2 btn btn-danger" type="submit">Delete</button>
                </div>
            </form>
        <?php endif; ?>

        <h3>作者：<?= $article['account']; ?></h3>
        <h3>內容：</h3>
        <span>
            <?= $article['content']; ?>
        </span><br><br>

        <?php if (isset($article['file_path'])) : ?>
            <h3><a href="file.php?id=<?= $article[0]; ?>">下載附件</a></h3>
        <?php endif; ?>

        <h3>留言時間：<?= $article[4]; ?></h3>
    </div>
<?php endif; ?>
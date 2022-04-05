<?php

include('config.php');

if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    if (!preg_match('/^\d*$/', $_GET['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $stat = $link->prepare('SELECT * FROM messages WHERE id = ?');
        $stat->bind_param('i', $_GET['id']);
        $stat->execute();
        $article = $stat->get_result()->fetch_all(MYSQLI_BOTH)[0];

        if (!isset($article['id'])) {
            $message = REDIRECT_TO_INDEX;
        } else {
            $file = $article['file_path'];
            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
        }
    }
} else {
    $message = REDIRECT_TO_INDEX;
}

?>

<?= @$message; ?>

<?php

include('helper.php');

if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'GET') { // show
    if (!isIdLegal($_GET['id'])) {
        $message = REDIRECT_TO_INDEX;
    } else {
        $article = getMessage($_GET['id']);

        if ($article) {
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
        } else {
            $message = REDIRECT_TO_INDEX;
        }
    }
} else {
    $message = REDIRECT_TO_INDEX;
}

?>

<?= @$message; ?>

<?php

include('header.php');

if (!isLoggedIn()) {
    $message = REDIRECT_TO_INDEX;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['url']) && $_POST['url'] !== '') { // by url
        $ext = getFileExtension(basename($_POST['url']));
        if (!isExtensionLegal($ext)) {
            $message = '檔案格式不正確';
        } else {
            $filename = generateUploadFileName(basename($_POST['url']), AVATARS_DIR);

            if (file_put_contents($filename, file_get_contents($_POST['url'])) && isLegalPng($filename)) {
                updateAvatar($user['id'], $filename);
                $message = REFRESH;
            } else {
                $message = '上傳失敗';
            }
        }
    } elseif (isset($_FILES['file']) && $_FILES["file"]["tmp_name"] !== "") { // by file
        $ext = getFileExtension($_FILES['file']['name']);
        if (!isExtensionLegal($ext)) {
            $message = '檔案格式不正確';
        } else {
            $uploadfile = generateUploadFileName($_FILES['file']['name'], AVATARS_DIR);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile) && isLegalPng($uploadfile)) {
                updateAvatar($user['id'], $uploadfile);
                $message = REFRESH;
            } else {
                $message = '上傳失敗';
            }
        }
    }
}
?>

<div class="container">
    <h2>編輯個人資料</h2>
    <br>
    <h3>目前頭像</h3>
    <?php if ($user['avatar_path']) : ?>
        <img src="<?= substr($user['avatar_path'], 14); ?>" alt="avatar">
    <?php else : ?>
        無
    <?php endif; ?>
    <hr>
    <h3>編輯頭像</h3>
    僅限 png 格式<br>
    <span style="color:red"><?= @$message; ?></span>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="url" class="form-label">網址</label>
            <input class="form-control" name="url" type="url" id="url">
        </div>
        <div class="mb-3">
            <label for="formFile" class="form-label">或上傳圖片</label>
            <input class="form-control" name="file" type="file" id="formFile" accept="image/png">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">上傳</button>
        </div>
    </form>
</div>
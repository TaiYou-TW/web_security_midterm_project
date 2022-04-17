<?php

include('config.php');

function isLoggedIn(): bool
{
    $user = getNowUser();
    return !is_null($user);
}

function isLoggedInAdmin(): bool
{
    $user = getNowUser();
    return !is_null($user) && $user['type'] === ADMIN;
}

function getNowUser(): array|null
{
    global $link;
    if (isset($_SESSION['account'])) {
        $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
        $stat->bind_param('s', $_SESSION['account']);
        $stat->execute();
        $user = $stat->get_result()->fetch_assoc();

        if (!$user) {
            clearAccountSession();
        }
        return $user;
    }
    return null;
}

function getTitle(): string
{
    global $link;
    $stat = $link->prepare('SELECT * FROM settings WHERE `key` = "title"');
    $stat->execute();
    $result = $stat->get_result()->fetch_assoc();
    return $result['value'] ?? DEFAULT_TITLE;
}

function updateTitle(string $title): void
{
    global $link;
    $stat = $link->prepare('UPDATE settings SET `value` = ? WHERE `key` = "title"');
    $stat->bind_param('s', $title);
    $stat->execute();
}

function getMessage(int $id): array|null
{
    global $link;
    $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ?');
    $stat->bind_param('i', $id);
    $stat->execute();
    return $stat->get_result()->fetch_all(MYSQLI_BOTH)[0] ?? null;
}

function getMessages(): array|null
{
    global $link;
    $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id ORDER BY messages.created_at ASC');
    $stat->execute();
    return $stat->get_result()->fetch_all(MYSQLI_BOTH);
}

function isMyMessage(int $id): bool
{
    global $link;
    $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ?');
    $stat->bind_param('i', $id);
    $stat->execute();
    $result = $stat->get_result()->fetch_all(MYSQLI_BOTH);
    return isset($result[0]['account']) && $result[0]['account'] === $_SESSION['account'];
}

function deleteMessage(int $id): void
{
    global $link;
    $stat = $link->prepare('DELETE FROM messages WHERE `id` = ?');
    $stat->bind_param('i', $id);
    $stat->execute();
}

function insertMessage(string $content, string $filename = null): void
{
    global $link, $user;
    $authorId = $user['id'];
    $content = htmlspecialchars($content);

    if (is_null($filename)) {
        $stat = $link->prepare('INSERT INTO messages (`content`, `by_user_id`) VALUES (?, ?)');
        $stat->bind_param('si', $content, $authorId);
    } else {
        $stat = $link->prepare('INSERT INTO messages (`content`, `file_path`, `by_user_id`) VALUES (?, ?, ?)');
        $stat->bind_param('ssi', $content, $filename, $authorId);
    }
    $stat->execute();
}

function clearAccountSession(): void
{
    if (isset($_SESSION['account'])) {
        unset($_SESSION['account']);
    }
}

function setAccountSession(string $account): void
{
    $_SESSION['account'] = $account;
}

function login(string $account, string $password): bool
{
    global $link;
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
    $stat->bind_param('s', $account);
    $stat->execute();
    $result = $stat->get_result()->fetch_assoc();
    return $result && password_verify($password, $result['password']);
}

function register(string $account, string $password): bool
{
    global $link;
    if (isAccountExist($account)) {
        return false;
    }

    $stat = $link->prepare('INSERT INTO users (`account`, `password`) VALUES (?, ?)');
    $password = password_hash($password, PASSWORD_BCRYPT);
    $stat->bind_param('ss', $account, $password);
    $stat->execute();
    return true;
}

function isAccountExist(string $account): bool
{
    global $link;
    $stat = $link->prepare('SELECT COUNT(id) FROM users WHERE `account` = ?');
    $stat->bind_param('s', $_POST['account']);
    $stat->execute();
    $result = $stat->get_result()->fetch_all();
    return $result[0][0] > 0;
}

function generateRandomString(int $length = 5): string
{
    $template = 'abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($template), 0, $length);
}

function generateUploadFileName(string $filename, string $dir = UPLOAD_DIR): string
{
    $ext = getFileExtension($filename);
    $random = generateRandomString(5);
    $timestamp = time();
    return "{$dir}{$timestamp}-{$random}.{$ext}";
}

function getFileExtension(string $filename): string
{
    $splitName = explode('.', $filename);
    return end($splitName);
}

function isExtensionLegal(string $ext): bool
{
    $legalList = [
        'png',
    ];
    return in_array($ext, $legalList);
}

function isIdLegal(string $str): bool
{
    return preg_match('/^\d*$/', $str);
}

function updateAvatar(int $id, string $filename): void
{
    try {
        global $link;
        $stat = $link->prepare('UPDATE users SET `avatar_path` = ? WHERE `id` = ?');
        $stat->bind_param('si', $filename, $id);
        $stat->execute();
    } catch (\Throwable $th) {
        //throw $th;
    }
}

function isLegalPng(string $filename): bool
{
    try {
        list($width, $height) = getimagesize($filename);
        $source = imagecreatefrompng($filename);
        $tmpImg = imagecreatetruecolor($width, $height);
        return imagecopyresized($tmpImg, $source, 0, 0, 0, 0, $width, $height, $width, $height);
    } catch (\Throwable $th) {
        return false;
    }
}

function logError(string $msg): void
{
    if (!file_exists(LOG_FILE)) {
        fopen(LOG_FILE, 'w');
    }
    error_log($msg, 3, LOG_FILE);
}

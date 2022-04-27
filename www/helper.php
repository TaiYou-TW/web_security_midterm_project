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
    try {
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
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return null;
    }
}

function getTitle(): string
{
    try {
        global $link;
        $stat = $link->prepare('SELECT * FROM settings WHERE `key` = "title"');
        $stat->execute();
        $result = $stat->get_result()->fetch_assoc();
        return filterString($result['value'] ?? DEFAULT_TITLE);
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return filterString(DEFAULT_TITLE);
    }
}

function updateTitle(string $title): void
{
    try {
        global $link;
        $stat = $link->prepare('UPDATE settings SET `value` = ? WHERE `key` = "title"');
        $stat->bind_param('s', $title);
        $stat->execute();
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function getMessage(int $id): array|null
{
    try {
        global $link;
        $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ? AND messages.`deleted_at` IS NULL');
        $stat->bind_param('i', $id);
        $stat->execute();
        return $stat->get_result()->fetch_all(MYSQLI_BOTH)[0] ?? null;
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return null;
    }
}

function getMessages(): array|null
{
    try {
        global $link;
        $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`deleted_at` IS NULL ORDER BY messages.created_at ASC');
        $stat->execute();
        return $stat->get_result()->fetch_all(MYSQLI_BOTH);
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return null;
    }
}

function isMyMessage(int $id): bool
{
    try {
        global $link;
        $stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id WHERE messages.`id` = ? AND messages.`deleted_at` IS NULL');
        $stat->bind_param('i', $id);
        $stat->execute();
        $result = $stat->get_result()->fetch_all(MYSQLI_BOTH);
        return isset($result[0]['account']) && $result[0]['account'] === $_SESSION['account'];
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return false;
    }
}

function deleteMessage(int $id): void
{
    try {
        global $link;
        $now = date("Y-m-d H:i:s");
        $stat = $link->prepare('UPDATE messages SET `deleted_at` = ? WHERE `id` = ?');
        $stat->bind_param('si', $now, $id);
        $stat->execute();
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function insertMessage(string $content, string $filename = null): void
{
    try {
        global $link, $user;
        $authorId = $user['id'];
        $content = filterString($content);

        if (is_null($filename)) {
            $stat = $link->prepare('INSERT INTO messages (`content`, `by_user_id`) VALUES (?, ?)');
            $stat->bind_param('si', $content, $authorId);
        } else {
            $stat = $link->prepare('INSERT INTO messages (`content`, `file_path`, `by_user_id`) VALUES (?, ?, ?)');
            $stat->bind_param('ssi', $content, $filename, $authorId);
        }
        $stat->execute();
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function clearAccountSession(): void
{
    try {
        if (isset($_SESSION['account'])) {
            unset($_SESSION['account']);
        }
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function setAccountSession(string $account): void
{
    try {
        $_SESSION['account'] = $account;
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function login(string $account, string $password): bool
{
    try {
        global $link;
        $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
        $stat->bind_param('s', $account);
        $stat->execute();
        $result = $stat->get_result()->fetch_assoc();
        return $result && password_verify($password, $result['password']);
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return false;
    }
}

function register(string $account, string $password): bool
{
    try {
        global $link;
        if (isAccountExist($account)) {
            return false;
        }

        $stat = $link->prepare('INSERT INTO users (`account`, `password`) VALUES (?, ?)');
        $password = password_hash($password, PASSWORD_BCRYPT);
        $stat->bind_param('ss', $account, $password);
        $stat->execute();
        return true;
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return false;
    }
}

function isAccountExist(string $account): bool
{
    try {
        global $link;
        $stat = $link->prepare('SELECT COUNT(id) FROM users WHERE `account` = ?');
        $stat->bind_param('s', $account);
        $stat->execute();
        $result = $stat->get_result()->fetch_all();
        return $result[0][0] > 0;
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return false;
    }
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
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function isLegalPng(string $filename): bool
{
    try {
        list($width, $height) = getimagesize($filename);
        $source = imagecreatefrompng($filename);
        if (!$source) {
            return false;
        }
        $tmpImg = imagecreatetruecolor($width, $height);
        return imagecopyresized($tmpImg, $source, 0, 0, 0, 0, $width, $height, $width, $height);
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
        return false;
    }
}

function logRequest(): void
{
    try {
        global $link;
        $method = $_SERVER['REQUEST_METHOD'];
        $status = http_response_code();
        $url = $_SERVER['REQUEST_URI'];
        $request_header = json_encode(getallheaders());
        $reqeust_body = json_encode([
            'GET' => $_GET,
            'POST' => $_POST,
            'FILES' => $_FILES,
            'COOKIE' => $_COOKIE,
            'SESSION' => $_SESSION,
        ]);
        $response_header = json_encode(headers_list());
        $stat = $link->prepare('INSERT INTO logs (`method`, `status`, `url`, `request_header`, `request_body`, `response_header`) VALUES (?, ?, ?, ?, ?, ?)');
        $stat->bind_param('ssssss', $method, $status, $url, $request_header, $reqeust_body, $response_header);
        $stat->execute();
    } catch (\Throwable $th) {
        logError($th->getFile(), $th->getLine(), $th->getMessage(), $th->getTraceAsString());
    }
}

function logError(string $file, string $line, string $msg, string $trace): void
{
    if (!file_exists(LOG_FILE)) {
        fopen(LOG_FILE, 'w');
    }
    $time = date("Y-m-d H:i:s");
    error_log("[$time] [$file#$line] $msg\n$trace\n", 3, LOG_FILE);
}

function filterString(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES);
}

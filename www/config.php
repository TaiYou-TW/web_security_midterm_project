<?php
define('TIMEZONE', 'Asia/Taipei');
date_default_timezone_set(TIMEZONE);

ini_set('date.timezone', 'Asia/Taipei');

define('DB_SERVER', 'db');
define('DB_USERNAME', 'user');
define('DB_PASSWORD', '6c%^g5p3LfiR^');
define('DB_NAME', 'ws');
define('DEFAULT_TITLE', 'Bulletin Board');
define('REDIRECT_TO_INDEX', '<meta http-equiv="refresh" content="0;url=/">');
define('UPLOAD_DIR', '/var/www/html/uploads/');

ini_set('session.cookie_httponly', 1);

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$link->set_charset("utf8mb4");

if (!$link) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

session_start();

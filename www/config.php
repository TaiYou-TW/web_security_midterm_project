<?php
define('DB_SERVER', 'db');
define('DB_USERNAME', 'user');
define('DB_PASSWORD', '');
define('DB_NAME', 'ws');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$link->set_charset("utf8mb4");

if (!$link) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$stat = $link->prepare('SELECT * FROM settings WHERE `key`="title"');
$stat->execute();
$result = $stat->get_result()->fetch_all(MYSQLI_ASSOC);

session_start();

?>

<head>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <title><?= $result[0]['value'] ?></title>
</head>

<div class="container">
    <h1><?= $result[0]['value'] ?></h1>
    <hr>
</div>
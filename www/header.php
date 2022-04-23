<?php

include('helper.php');

logRequest();

$title = getTitle();
$user = getNowUser();

?>

<head>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/header.css" rel="stylesheet">
    <script src="/js/bootstrap.bundle.min.js"></script>
    <title><?= $title ?></title>
</head>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <div class="container-fluid">
        <a class="navbar-brand" href="/"> <?= $title ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            </ul>
            <?php if ($user && $user['type'] === 'user') : ?>
                <span class="me-2">
                    Hello, <?= htmlspecialchars($user['account']); ?>
                </span>
                <a href="profile.php" class="btn btn-success me-2" tabindex="-1" role="button">Edit Profile</a>
                <form class="d-flex my-auto" method="POST" action="logout.php">
                    <button class="form-control me-2 btn btn-danger" type="submit">Logout</button>
                </form>
            <?php elseif ($user && $user['type'] === 'admin') : ?>
                <span class="me-2">
                    Hello, <?= $user['account'] ?>
                </span>
                <a href="admin.php" class="btn btn-success me-2" tabindex="-1" role="button">Admin</a>
                <a href="profile.php" class="btn btn-success me-2" tabindex="-1" role="button">Edit Profile</a>
                <form class="d-flex my-auto" method="POST" action="logout.php">
                    <button class="form-control me-2 btn btn-danger" type="submit">Logout</button>
                </form>
            <?php else : ?>
                <a href="register.php" class="btn btn-primary me-2" tabindex="-1" role="button">Register</a>
                <a href="login.php" class="btn btn-success me-2" tabindex="-1" role="button">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<br>
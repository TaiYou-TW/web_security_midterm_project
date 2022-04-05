<?php

$stat = $link->prepare('SELECT * FROM settings WHERE `key` = "title"');
$stat->execute();
$result = $stat->get_result()->fetch_assoc();

if (isset($_SESSION['account'])) {
    $stat = $link->prepare('SELECT * FROM users WHERE `account` = ?');
    $stat->bind_param('s', $_SESSION['account']);
    $stat->execute();
    $user = $stat->get_result()->fetch_assoc();
}

?>

<head>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <title><?= $result['value'] ?? DEFAULT_TITLE ?></title>
</head>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <div class="container-fluid">
        <a class="navbar-brand" href="/"> <?= $result['value'] ?? DEFAULT_TITLE ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            </ul>
            <?php if (isset($_SESSION['account']) && $user['type'] === 'user') : ?>
                <span class="me-2">
                    Hello, <?= $_SESSION['account'] ?>
                </span>
                <a href="profile.php" class="btn btn-success me-2" tabindex="-1" role="button">Edit Profile</a>
                <form class="d-flex my-auto" method="POST" action="logout.php">
                    <button class="form-control me-2 btn btn-danger" type="submit">Logout</button>
                </form>
            <?php elseif (isset($_SESSION['account']) && $user['type'] === 'admin') : ?>
                <span class="me-2">
                    Hello, <?= $_SESSION['account'] ?>
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
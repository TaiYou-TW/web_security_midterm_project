<?php
include('config.php');

$stat = $link->prepare('SELECT * FROM messages LEFT JOIN users on messages.by_user_id=users.id');
$stat->execute();
$result = $stat->get_result()->fetch_all(MYSQLI_ASSOC);
// print_r($result[0]);
?>
<div class="container">
    <?php if (isset($_SESSION['account'])) : ?>
        <h3>Hello, <?= $_SESSION['account'] ?></h3>
    <?php endif; ?>
    <h2>首頁</h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">作者</th>
                <th scope="col">留言</th>
                <th scope="col">留言時間</th>
                <th scope="col">檔案</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $key => $value) : ?>
                <tr>
                    <td><?= $value['account']; ?></td>
                    <td><?= $value['content']; ?></td>
                    <td><?= $value['created_at']; ?></td>
                    <td><?= $value['file_path']; ?></td>
                    <!-- <td><?= $value['avatar_path']; ?></td> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
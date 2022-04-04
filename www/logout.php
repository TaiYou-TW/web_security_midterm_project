<?php
include('config.php');

if (isset($_SESSION['account'])) {
    unset($_SESSION['account']);
}

?>
<meta http-equiv="refresh" content="0;url=/">
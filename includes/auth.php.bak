<?php
function authOnly() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function adminOnly() {
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
}
?>
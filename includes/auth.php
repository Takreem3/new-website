<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function authOnly() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function adminOnly() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>

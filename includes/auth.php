<?php
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

function guestOnly() {
    if (isset($_SESSION['user_id'])) {
        header("Location: dashboard.php");
        exit();
    }
}
?>
<?php
function authOnly() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function adminOnly() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php");
        exit();
    }
}
?>
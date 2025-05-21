<?php
session_start();

function authOnly() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

function adminOnly() {
    if (!isset($_SESSION['admin_id'])) {
        $_SESSION['admin_redirect'] = $_SERVER['REQUEST_URI'];
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

// Secure database credentials
function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli('localhost', 'root', '', 'mlm_system');
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }
    }
    return $conn;
}
?>
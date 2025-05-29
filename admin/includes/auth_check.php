<?php
session_start();
require '../../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Verify admin status
$admin_id = $_SESSION['admin_id'];
$result = $conn->query("SELECT is_admin FROM users WHERE id = $admin_id");
$user = $result->fetch_assoc();

if (!$user || $user['is_admin'] != 1) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
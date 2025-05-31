<?php
session_start();

// Correct relative path for your structure
require dirname(__DIR__, 2) . '/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /new-website/login.php");
    exit;
}

// Check admin status
$admin_check = $conn->query("SELECT id FROM users WHERE id = {$_SESSION['user_id']} AND is_admin = 1");
if ($admin_check->num_rows == 0) {
    header("Location: /new-website/login.php");
    exit;
}
?>
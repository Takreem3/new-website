<?php
session_start();

// Correct path for your structure
require __DIR__ . '/../../database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /new-website/login.php");
    exit;
}

// Verify admin status
$check_admin = $conn->query("SELECT id FROM users WHERE id = {$_SESSION['user_id']} AND is_admin = 1");
if ($check_admin->num_rows == 0) {
    session_destroy();
    header("Location: /new-website/login.php?error=admin_required");
    exit;
}
?>

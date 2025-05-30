<?php
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: ../login.php?redirect=admin");
    exit;
}

// Verify admin status in database
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_admin = 1");
$stmt->execute([$_SESSION['user_id']]);
if($stmt->rowCount() === 0){
    session_destroy();
    header("Location: ../login.php?error=unauthorized");
    exit;
}

<?php
session_start();
require 'config.php';

// CSRF Validation
if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    die("Security error: Invalid token!");
}

// Validate logged-in user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Save post to database
$stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $_SESSION['user_id'], $_POST['title'], $_POST['content']);
$stmt->execute();

header("Location: dashboard.php"); // Redirect after saving
?>

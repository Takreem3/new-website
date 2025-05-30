<?php
// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
// Database configuration for XAMPP
$db_host = "localhost";
$db_user = "root";        // Default XAMPP username
$db_pass = "";            // Default XAMPP password (empty)
$db_name = "mlm_website"; // Create this in phpMyAdmin

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>

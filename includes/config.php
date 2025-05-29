<?php
// Database configuration - USE THESE DEFAULT XAMPP VALUES
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "mlm_website";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
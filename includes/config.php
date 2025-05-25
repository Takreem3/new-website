<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mlm_system');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
session_start();

// Base URL
define('BASE_URL', 'http://localhost/mlm_website/');

// Commission rates
define('DIRECT_COMMISSION_RATE', 0.10); // 10%
define('INDIRECT_COMMISSION_RATE', 0.05); // 5%
?>

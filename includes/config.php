<?php
// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants only if they don't exist
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');
defined('DB_NAME') || define('DB_NAME', 'mlm_system');
defined('BASE_URL') || define('BASE_URL', 'http://localhost/mlm_website/');
defined('DIRECT_COMMISSION_RATE') || define('DIRECT_COMMISSION_RATE', 0.10);
defined('INDIRECT_COMMISSION_RATE') || define('INDIRECT_COMMISSION_RATE', 0.05);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>
<?php
// Database configuration - UPDATE THESE VALUES
$db_host = "localhost";
$db_user = "your_actual_username"; // Usually 'root' for XAMPP
$db_pass = "your_actual_password"; // Usually empty for XAMPP
$db_name = "your_actual_database"; // Your database name

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = mysqli_connect("localhost", "your_username", "your_password", "your_database");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
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

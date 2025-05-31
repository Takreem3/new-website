<?php
require 'database.php';
require 'includes/header.php';

// Simple router
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

switch ($path) {
    case '/new-website/':
    case '/new-website/index.php':
        echo '<h1>Welcome to MLM System</h1>';
        if (isset($_SESSION['user_id'])) {
            echo '<p>Welcome, '.htmlspecialchars($_SESSION['username']).'</p>';
            echo '<a href="dashboard.php">Dashboard</a>';
        } else {
            echo '<a href="login.php">Login</a> | <a href="register.php">Register</a>';
        }
        break;
        
    default:
        http_response_code(404);
        echo '<h1>Page Not Found</h1>';
        break;
}

require 'includes/footer.php';
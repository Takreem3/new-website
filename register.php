<?php
// register.php - Clean Working Version
session_start();

// 1. Enable error reporting (remove after fixing)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Verify includes exist
if (!file_exists('includes/db_config.php')) {
    die("Missing database configuration");
}
require 'includes/db_config.php';
require 'includes/functions.php';

// 3. Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic validation
        $username = clean_input($_POST['username'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $sponsor = clean_input($_POST['sponsor'] ?? '');

        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }

        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Username or email already exists");
        }

        // Validate sponsor
        $sponsor_id = null;
        if (!empty($sponsor)) {
            $sponsor_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $sponsor_stmt->bind_param("s", $sponsor);
            $sponsor_stmt->execute();
            
            if ($sponsor_stmt->get_result()->num_rows === 0) {
                throw new Exception("Sponsor username does not exist");
            }
            $sponsor_id = $sponsor_stmt->get_result()->fetch_assoc()['id'];
        }

        // Insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password, sponsored_by) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sssi", $username, $email, $hashed_password, $sponsor_id);
        
        if ($insert->execute()) {
            header("Location: register_success.php");
            exit();
        } else {
            throw new Exception("Registration failed: " . $conn->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <?php if (isset($error)): ?>
        <div style="color:red"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="sponsor" placeholder="Sponsor (optional)">
        <button type="submit">Register</button>
    </form>
</body>
</html>
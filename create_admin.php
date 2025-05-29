<?php
// create_admin.php - Run this once then delete
require 'includes/config.php';

// Generate secure credentials
$username = 'admin';
$password = 'Admin@1234'; // Change this immediately after setup
$email = 'admin@yourdomain.com';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$secret_key = 'MLM-'.bin2hex(random_bytes(8));

try {
    // Create admin user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin, payment_status) 
                           VALUES (?, ?, ?, 1, 'verified')");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    $stmt->execute();

    echo "<h2>Admin Account Created Successfully</h2>";
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Secret Key:</strong> $secret_key</p>";
    echo "<p style='color:red;font-weight:bold;'>IMPORTANT: Delete this file immediately after use!</p>";

} catch(Exception $e) {
    echo "<h2>Error Creating Admin</h2>";
    echo "<p>".$e->getMessage()."</p>";
    echo "<pre>"; print_r($conn->error_list); echo "</pre>";
}
?>
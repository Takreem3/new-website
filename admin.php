<?php
// create_admin.php - Admin setup script
require 'includes/config.php';

// Check if columns exist or create them
try {
    $conn->query("ALTER TABLE users 
                 ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0");
    $conn->query("ALTER TABLE users 
                 ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) DEFAULT 'unpaid'");
} catch(Exception $e) {
    die("<h2>Database Error</h2><p>".$e->getMessage()."</p>");
}

// Admin credentials
$admin_data = [
    'username' => 'mlmadmin',
    'password' => 'Secure@1234', // Change after setup!
    'email' => 'admin@yourmlm.com',
    'secret_key' => 'MLM-'.bin2hex(random_bytes(8))
];

// Create admin
try {
    $stmt = $conn->prepare("INSERT INTO users 
                          (username, email, password, is_admin, payment_status, secret_key) 
                          VALUES (?, ?, ?, 1, 'verified', ?)");
    
    $hashed_password = password_hash($admin_data['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("ssss", 
        $admin_data['username'],
        $admin_data['email'],
        $hashed_password,
        $admin_data['secret_key']
    );
    
    if($stmt->execute()) {
        echo "<h2>MLM Admin Setup Complete</h2>";
        echo "<div style='background:#f8f9fa;padding:20px;border-radius:5px;'>";
        echo "<p><strong>Username:</strong> ".htmlspecialchars($admin_data['username'])."</p>";
        echo "<p><strong>Temp Password:</strong> ".htmlspecialchars($admin_data['password'])."</p>";
        echo "<p><strong>Secret Key:</strong> ".htmlspecialchars($admin_data['secret_key'])."</p>";
        echo "<p style='color:red;font-weight:bold;'>IMPORTANT: Delete this file immediately!</p>";
        echo "</div>";
    } else {
        throw new Exception("Insert failed: ".$stmt->error);
    }
} catch(Exception $e) {
    echo "<h2>Setup Failed</h2>";
    echo "<p>Error: ".$e->getMessage()."</p>";
    echo "<pre>Database error: "; print_r($conn->error_list); echo "</pre>";
}
?>

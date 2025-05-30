<?php
// setup_columns.php - Run this ONCE then DELETE
require 'includes/config.php';

// Your exact table structure from the error
$columns_to_add = [
    "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 COMMENT 'Admin flag'",
    "ALTER TABLE users ADD COLUMN payment_status VARCHAR(20) DEFAULT 'unpaid' COMMENT 'Payment status'",
    "ALTER TABLE users ADD COLUMN secret_key VARCHAR(32) COMMENT 'Security key'",
    "ALTER TABLE users MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

echo "<h2>Updating Database Structure</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px;'>";

foreach($columns_to_add as $sql) {
    echo "<p><strong>Executing:</strong> ".htmlspecialchars($sql)."</p>";
    
    try {
        if($conn->query($sql)) {
            echo "<p style='color:green'>Success!</p>";
        } else {
            throw new Exception($conn->error);
        }
    } catch(Exception $e) {
        echo "<p style='color:red'>Error: ".htmlspecialchars($e->getMessage())."</p>";
        // Continue with next query even if one fails
    }
}

echo "</div>";
echo "<h3 style='color:red'>IMPORTANT: Delete this file immediately after use!</h3>";

// Verify structure
echo "<h3>Updated Table Structure</h3>";
$result = $conn->query("DESCRIBE users");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['Field']."</td>";
    echo "<td>".$row['Type']."</td>";
    echo "<td>".$row['Null']."</td>";
    echo "<td>".$row['Key']."</td>";
    echo "</tr>";
}
echo "</table>";
?>

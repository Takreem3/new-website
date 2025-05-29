<?php
// Test script: test_db.php
$conn = mysqli_connect("localhost", "root", "", "mlm_website");
if ($conn) {
    echo "Connected successfully!";
    mysqli_close($conn);
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
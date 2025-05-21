<?php
include '../includes/config.php';
adminOnly(); // Secure page

// Stats
$users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$pendingWithdrawals = $conn->query("SELECT COUNT(*) FROM withdrawals WHERE status='pending'")->fetch_row()[0];
$pendingKYC = $conn->query("SELECT COUNT(*) FROM kyc_verifications WHERE status='pending'")->fetch_row()[0];
?>

<!-- Admin Dashboard HTML with:
- Summary Cards
- Quick Links
- Recent Activity Log -->
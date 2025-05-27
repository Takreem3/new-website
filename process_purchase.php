<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/commission.php';
require_once 'includes/functions.php';

// Validate authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate CSRF token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}

// Process purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->autocommit(FALSE); // Start transaction
        
        // 1. Record the purchase
        $stmt = $conn->prepare("INSERT INTO purchases (user_id, amount, product_id) VALUES (?, ?, ?)");
        $stmt->bind_param("idi", $_SESSION['user_id'], $_POST['amount'], $_POST['product_id']);
        $stmt->execute();
        $purchase_id = $stmt->insert_id;
        
        // 2. Calculate commissions
        $commissionSystem = new CommissionSystem($conn);
        $commissionSystem->calculateCommission($_SESSION['user_id'], $_POST['amount']);
        
        $conn->commit();
        $_SESSION['success'] = "Purchase completed successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Purchase failed: " . $e->getMessage();
    }
    
    header("Location: purchase_success.php");
    exit();
}
?>
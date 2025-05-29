<?php
require 'includes/auth_check.php';

$id = (int)$_GET['id'];
$action = $_GET['action'];

if (in_array($action, ['approve', 'reject'])) {
    $status = $action == 'approve' ? 'completed' : 'rejected';
    
    $conn->query("UPDATE withdrawals SET 
        status = '$status',
        processed_at = NOW() 
        WHERE id = $id");
        
    $_SESSION['success'] = "Withdrawal $action successful!";
}

header("Location: withdrawals.php");
exit();
?>
<?php
require __DIR__ . '/../includes/config.php';

// Process pending withdrawals
$withdrawals = $conn->query("
    SELECT * FROM withdrawals 
    WHERE status = 'pending'
");

while($withdrawal = $withdrawals->fetch_assoc()) {
    // Process payment via Stripe/PayPal API here
    $paymentSuccess = true; // Replace with actual API call
    
    if($paymentSuccess) {
        $conn->query("
            UPDATE withdrawals 
            SET status = 'paid', 
                processed_at = NOW() 
            WHERE id = {$withdrawal['id']}
        ");
    }
}

// Log execution
file_put_contents(__DIR__ . '/payout_log.txt', date('Y-m-d H:i:s') . " Processed\n", FILE_APPEND);
?>

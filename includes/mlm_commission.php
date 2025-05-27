<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
require __DIR__.'/includes/mlm_functions.php';
adminOnly();

// Process daily commissions
$today = date('Y-m-d');
$transactions = $conn->query("
    SELECT t.user_id, t.amount, u.sponsor_id 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE DATE(t.created_at) = '$today'
    AND t.commission_processed = 0
");

while ($tx = $transactions->fetch_assoc()) {
    // Calculate direct commission (10%)
    $direct_commission = $tx['amount'] * 0.10;
    $conn->query("
        INSERT INTO commissions (user_id, amount, level, type, created_at)
        VALUES ({$tx['sponsor_id']}, $direct_commission, 1, 'direct', NOW())
    ");
    
    // Calculate upline commissions (levels 2-5)
    $current_level = $tx['sponsor_id'];
    $levels = [
        2 => 0.05, 
        3 => 0.03, 
        4 => 0.02, 
        5 => 0.01
    ];
    
    foreach ($levels as $level => $percentage) {
        $current_level = $conn->query("
            SELECT sponsor_id FROM users WHERE id = $current_level
        ")->fetch_assoc()['sponsor_id'];
        
        if ($current_level) {
            $commission = $tx['amount'] * $percentage;
            $conn->query("
                INSERT INTO commissions (user_id, amount, level, type, created_at)
                VALUES ($current_level, $commission, $level, 'indirect', NOW())
            ");
        }
    }
    
    // Mark as processed
    $conn->query("
        UPDATE transactions 
        SET commission_processed = 1 
        WHERE user_id = {$tx['user_id']} 
        AND DATE(created_at) = '$today'
    ");
}

$_SESSION['message'] = "Commissions processed successfully!";
header("Location: admin/dashboard.php");
exit();
?>
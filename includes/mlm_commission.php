<?php
function calculateBinaryCommissions($user_id) {
    global $conn;
    
    // Get user's binary tree positions
    $userNodes = $conn->query("
        SELECT id, position FROM users 
        WHERE sponsor_id = $user_id 
        AND position IN ('left', 'right')
    ");

    $leftTeam = [];
    $rightTeam = [];

    while($node = $userNodes->fetch_assoc()) {
        if($node['position'] == 'left') {
            $leftTeam[] = $node['id'];
        } else {
            $rightTeam[] = $node['id'];
        }
    }

    // Calculate pairing
    $pairs = min(count($leftTeam), count($rightTeam));
    if($pairs > 0) {
        $commission = $pairs * 10; // $10 per pair
        $conn->query("
            INSERT INTO commissions 
            (user_id, amount, type, status) 
            VALUES 
            ($user_id, $commission, 'binary', 'pending')
        ");
    }
}

function calculateUnilevelCommissions($user_id, $amount, $level=1) {
    global $conn;
    
    if($level > 5) return; // Limit to 5 levels
    
    $commissionRates = [0.10, 0.05, 0.03, 0.02, 0.01]; // 10%, 5%, etc.
    $commission = $amount * $commissionRates[$level-1];
    
    $conn->query("
        INSERT INTO commissions 
        (user_id, amount, level, type, status) 
        VALUES 
        ($user_id, $commission, $level, 'unilevel', 'pending')
    ");
    
    // Get sponsor for next level
    $sponsor = $conn->query("
        SELECT sponsor_id FROM users WHERE id = $user_id
    ")->fetch_assoc();
    
    if($sponsor['sponsor_id']) {
        calculateUnilevelCommissions($sponsor['sponsor_id'], $amount, $level+1);
    }
}
?>


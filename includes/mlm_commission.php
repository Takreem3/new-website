<?php
function processMLMCommission($conn, $new_user_id, $sponsor_id) {
    $package_amount = 1000; // Set your package price
    $levels = [
        1 => 0.10, // Level 1: 10%
        2 => 0.05, // Level 2: 5%
        3 => 0.03, // Level 3: 3%
        4 => 0.02, // Level 4: 2%
        5 => 0.01  // Level 5: 1%
    ];
    
    $current_level = 1;
    $current_sponsor = $sponsor_id;
    
    while ($current_sponsor && $current_level <= 5) {
        $amount = $package_amount * $levels[$current_level];
        
        $conn->query("
            INSERT INTO commissions 
            (user_id, amount, type, level, from_user_id, status) 
            VALUES 
            ($current_sponsor, $amount, 'unilevel', $current_level, $new_user_id, 'pending')
        ");
        
        // Move up the sponsorship tree
        $current_sponsor = $conn->query("
            SELECT sponsor_id FROM users WHERE id = $current_sponsor
        ")->fetch_assoc()['sponsor_id'];
        
        $current_level++;
    }
}
?>
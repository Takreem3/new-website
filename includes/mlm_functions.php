<?php
function calculateCommissions($conn, $new_user_id, $sponsor_id) {
    // Get user's plan bonus amount
    $bonus = $conn->query("
        SELECT p.signup_bonus 
        FROM plans p
        JOIN users u ON p.id = u.plan_id
        WHERE u.id = $new_user_id
    ")->fetch_assoc()['signup_bonus'];
    
    // Direct commission (10%)
    $conn->query("
        INSERT INTO commissions (user_id, amount, level, type)
        VALUES ($sponsor_id, $bonus * 0.1, 1, 'direct')
    ");
    
    // Upline commissions
    $levels = [2 => 0.05, 3 => 0.03, 4 => 0.02, 5 => 0.01];
    $current_level = $sponsor_id;
    
    foreach ($levels as $level => $percentage) {
        $current_level = $conn->query("
            SELECT sponsor_id FROM users WHERE id = $current_level
        ")->fetch_assoc()['sponsor_id'];
        
        if ($current_level) {
            $conn->query("
                INSERT INTO commissions (user_id, amount, level, type)
                VALUES ($current_level, $bonus * $percentage, $level, 'indirect')
            ");
        }
    }
}
?>
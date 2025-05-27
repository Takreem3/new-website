<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
adminOnly();

// Calculate daily commissions
$conn->query("
    INSERT INTO commissions (user_id, amount, level, type)
    SELECT 
        u.id,
        SUM(o.total * CASE 
            WHEN c.level = 1 THEN ".DIRECT_COMMISSION_RATE." 
            ELSE ".INDIRECT_COMMISSION_RATE." 
        END) as commission,
        c.level,
        'daily'
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN genealogy c ON c.downline_id = u.id
    WHERE o.status = 'completed'
      AND o.date = CURDATE() - INTERVAL 1 DAY
    GROUP BY u.id, c.level
");

// Log execution
file_put_contents('commission_log.txt', date('Y-m-d H:i:s')." - Commissions calculated\n", FILE_APPEND);
echo "Commissions processed!";
?>
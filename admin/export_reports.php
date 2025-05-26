<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';
adminOnly();

// Set Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=mlm_report_".date('Y-m-d').".xls");

// Fetch data with commission calculations
$result = $conn->query("
    SELECT 
        u.id,
        u.username,
        u.email,
        COUNT(d.id) as downline_count,
        SUM(CASE WHEN c.level = 1 THEN c.amount ELSE 0 END) as direct_commissions,
        SUM(CASE WHEN c.level > 1 THEN c.amount ELSE 0 END) as indirect_commissions
    FROM users u
    LEFT JOIN users d ON d.sponsor_id = u.id
    LEFT JOIN commissions c ON c.user_id = u.id
    GROUP BY u.id
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        td { padding: 5px; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Downline</th>
            <th>Direct Commissions</th>
            <th>Indirect Commissions</th>
            <th>Total Earnings</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['downline_count'] ?></td>
            <td>$<?= number_format($row['direct_commissions'], 2) ?></td>
            <td>$<?= number_format($row['indirect_commissions'], 2) ?></td>
            <td>$<?= number_format($row['direct_commissions'] + $row['indirect_commissions'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

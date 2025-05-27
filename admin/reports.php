<?php
require_once '../includes/auth_check.php'; // Ensure admin access
require_once '../includes/db_config.php';

// Date filtering
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get commission reports
$stmt = $conn->prepare("
    SELECT 
        u.username as sponsor,
        COUNT(c.id) as referrals,
        SUM(c.amount) as total_commission,
        u2.username as referred_user,
        c.level,
        c.created_at
    FROM commissions c
    JOIN users u ON c.sponsor_id = u.id
    JOIN users u2 ON c.user_id = u2.id
    WHERE c.created_at BETWEEN ? AND ?
    GROUP BY c.sponsor_id, c.level
    ORDER BY total_commission DESC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$commissions = $result->fetch_all(MYSQLI_ASSOC);

// Get top performers
$top_performers = $conn->query("
    SELECT u.username, COUNT(c.id) as referrals, SUM(c.amount) as total
    FROM commissions c
    JOIN users u ON c.sponsor_id = u.id
    GROUP BY c.sponsor_id
    ORDER BY total DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        .chart { height: 300px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Commission Reports</h1>
    
    <!-- Date Filter -->
    <form method="get">
        <label>From: <input type="date" name="start_date" value="<?= $start_date ?>"></label>
        <label>To: <input type="date" name="end_date" value="<?= $end_date ?>"></label>
        <button type="submit">Filter</button>
    </form>
    
    <!-- Summary Cards -->
    <div style="display: flex; gap: 20px; margin: 20px 0;">
        <div style="border: 1px solid #ccc; padding: 15px; flex: 1;">
            <h3>Total Commissions</h3>
            <p><?= number_format(array_sum(array_column($commissions, 'total_commission')), 2) ?></p>
        </div>
        <div style="border: 1px solid #ccc; padding: 15px; flex: 1;">
            <h3>Top Performer</h3>
            <p><?= $top_performers[0]['username'] ?? 'N/A' ?> (<?= number_format($top_performers[0]['total'] ?? 0, 2) ?>)</p>
        </div>
    </div>
    
    <!-- Commission Breakdown -->
    <h2>Commission Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Sponsor</th>
                <th>Referred User</th>
                <th>Level</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commissions as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['sponsor']) ?></td>
                <td><?= htmlspecialchars($row['referred_user']) ?></td>
                <td><?= $row['level'] ?></td>
                <td><?= number_format($row['total_commission'], 2) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Top Performers -->
    <h2>Top 10 Performers</h2>
    <table>
        <thead>
            <tr>
                <th>Sponsor</th>
                <th>Referrals</th>
                <th>Total Commission</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($top_performers as $performer): ?>
            <tr>
                <td><?= htmlspecialchars($performer['username']) ?></td>
                <td><?= $performer['referrals'] ?></td>
                <td><?= number_format($performer['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
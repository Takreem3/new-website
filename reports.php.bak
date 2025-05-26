<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';

// Get commissions
$commissions = $conn->query("
    SELECT c.*, u.username as from_user
    FROM commissions c
    LEFT JOIN users u ON c.from_user_id = u.id
    WHERE c.user_id = {$_SESSION['user_id']}
    ORDER BY c.created_at DESC
");

require __DIR__.'/includes/header.php';
?>

<div class="container">
    <h2>Your Commissions</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>From User</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($c = $commissions->fetch_assoc()): ?>
            <tr>
                <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                <td>$<?= number_format($c['amount'], 2) ?></td>
                <td><?= ucfirst($c['type']) ?></td>
                <td><?= $c['from_user'] ?? 'System' ?></td>
                <td><?= ucfirst($c['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>
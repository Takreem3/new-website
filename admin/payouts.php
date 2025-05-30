<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';
adminOnly();

$payouts = $conn->query("
    SELECT p.*, u.username 
    FROM payouts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.processed_at DESC
");

require __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">
    <h2>Payout Logs</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Processed Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $payouts->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td>
                    <span class="badge bg-<?= 
                        $row['status'] == 'paid' ? 'success' : 
                        ($row['status'] == 'failed' ? 'danger' : 'warning') 
                    ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </td>
                <td><?= $row['processed_at'] ? date('M j, Y', strtotime($row['processed_at'])) : 'Pending' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>

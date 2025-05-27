<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';
adminOnly();

$commissions = $conn->query("
    SELECT c.*, u.username 
    FROM commissions c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
");

require __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">
    <h2>Commission Reports</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Level</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $commissions->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td><?= $row['level'] ?></td>
                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>
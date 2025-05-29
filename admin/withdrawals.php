<?php
require 'includes/auth_check.php';
$withdrawals = $conn->query("
    SELECT w.*, u.username 
    FROM withdrawals w
    JOIN users u ON w.user_id = u.id
    ORDER BY w.requested_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Withdrawals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-4">
        <h2>Withdrawal Requests</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($withdrawal = $withdrawals->fetch_assoc()): ?>
                <tr>
                    <td><?= $withdrawal['id'] ?></td>
                    <td><?= $withdrawal['username'] ?></td>
                    <td>$<?= number_format($withdrawal['amount'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $withdrawal['status'] == 'completed' ? 'success' : 
                            ($withdrawal['status'] == 'pending' ? 'warning' : 'danger')
                        ?>">
                            <?= ucfirst($withdrawal['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($withdrawal['requested_at'])) ?></td>
                    <td>
                        <?php if ($withdrawal['status'] == 'pending'): ?>
                            <a href="process_withdrawal.php?id=<?= $withdrawal['id'] ?>&action=approve" class="btn btn-sm btn-success">Approve</a>
                            <a href="process_withdrawal.php?id=<?= $withdrawal['id'] ?>&action=reject" class="btn btn-sm btn-danger">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
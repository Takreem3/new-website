<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';

// Process actions
if(isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if($_GET['action'] == 'approve') {
        $conn->query("UPDATE withdrawals SET status='approved', processed_at=NOW() WHERE id=$id");
    } elseif($_GET['action'] == 'reject') {
        $conn->query("UPDATE withdrawals SET status='rejected', processed_at=NOW() WHERE id=$id");
    }
    header("Location: withdrawals.php");
    exit();
}

// Get pending requests
$requests = $conn->query("
    SELECT w.*, u.username 
    FROM withdrawals w
    JOIN users u ON w.user_id = u.id
    WHERE w.status = 'pending'
    ORDER BY w.requested_at DESC
");

require __DIR__.'/../includes/header.php';
?>

<div class="container-fluid">
    <h2>Pending Withdrawals</h2>
    
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Details</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($request = $requests->fetch_assoc()): ?>
            <tr>
                <td><?= $request['id'] ?></td>
                <td><?= $request['username'] ?></td>
                <td>$<?= number_format($request['amount'], 2) ?></td>
                <td><?= ucfirst($request['payment_method']) ?></td>
                <td><?= nl2br(htmlspecialchars($request['account_details'])) ?></td>
                <td><?= date('M d, Y', strtotime($request['requested_at'])) ?></td>
                <td>
                    <a href="withdrawals.php?action=approve&id=<?= $request['id'] ?>" 
                       class="btn btn-sm btn-success">Approve</a>
                    <a href="withdrawals.php?action=reject&id=<?= $request['id'] ?>" 
                       class="btn btn-sm btn-danger">Reject</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>
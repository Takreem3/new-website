<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';

// Secure admin access
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Process actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if(in_array($action, ['approve', 'reject'])) {
        $conn->query("
            UPDATE withdrawals 
            SET status = '$action',
                processed_at = NOW() 
            WHERE id = $id
        ");
$conn->query("UPDATE withdrawals SET status='approved' WHERE id=$withdrawal_id");
        header("Location: withdrawals.php");
        exit();
    }
}

// Get withdrawals
$withdrawals = $conn->query("
    SELECT w.*, u.username 
    FROM withdrawals w
    JOIN users u ON w.user_id = u.id
    ORDER BY w.requested_at DESC
");

require __DIR__.'/../includes/header.php';
?>

<div class="container-fluid">
    <h2 class="mt-4">Withdrawal Management</h2>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($w = $withdrawals->fetch_assoc()): ?>
                    <tr>
                        <td><?= $w['id'] ?></td>
                        <td><?= $w['username'] ?></td>
                        <td>$<?= number_format($w['amount'], 2) ?></td>
                        <td><?= ucfirst($w['payment_method']) ?></td>
                        <td><?= nl2br(htmlspecialchars($w['account_details'])) ?></td>
                        <td><?= date('M d, Y', strtotime($w['requested_at'])) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $w['status'] == 'approved' ? 'success' : 
                                ($w['status'] == 'rejected' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($w['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if($w['status'] == 'pending'): ?>
                                <a href="?action=approve&id=<?= $w['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="?action=reject&id=<?= $w['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>
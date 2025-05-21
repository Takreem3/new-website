<?php
include 'includes/config.php';
include 'includes/auth.php';

$minWithdrawal = 50; // Minimum $50 to withdraw

// Get user balance
$balance = $conn->query("
    SELECT SUM(amount) 
    FROM commissions 
    WHERE user_id = {$_SESSION['user_id']} 
    AND status = 'approved'
")->fetch_row()[0];

// Handle withdrawal request
if($_SERVER['REQUEST_METHOD'] == 'POST' && $balance >= $minWithdrawal) {
    $conn->query("
        INSERT INTO withdrawals 
        (user_id, amount, status) 
        VALUES 
        ({$_SESSION['user_id']}, $balance, 'pending')
    ");
    $_SESSION['message'] = "Withdrawal request submitted!";
    header("Location: payouts.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container">
    <h2>Your Earnings</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5>Available Balance: $<?= number_format($balance, 2) ?></h5>
            <?php if($balance >= $minWithdrawal): ?>
                <form method="POST">
                    <button type="submit" class="btn btn-primary">
                        Request Withdrawal
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">
                    Minimum $<?= $minWithdrawal ?> required to withdraw
                </div>
            <?php endif; ?>
        </div>
    </div>

    <h4>Transaction History</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $transactions = $conn->query("
                SELECT * FROM commissions 
                WHERE user_id = {$_SESSION['user_id']}
                UNION ALL
                SELECT id, user_id, amount, 'withdrawal' as type, status, created_at 
                FROM withdrawals 
                WHERE user_id = {$_SESSION['user_id']}
                ORDER BY created_at DESC
            ");
            while($tx = $transactions->fetch_assoc()): ?>
            <tr>
                <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                <td>$<?= number_format($tx['amount'], 2) ?></td>
                <td><?= ucfirst($tx['type']) ?></td>
                <td><?= ucfirst($tx['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
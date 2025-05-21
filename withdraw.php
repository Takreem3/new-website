<?php
include 'includes/config.php';
include 'includes/auth.php'; // Ensures logged-in users only

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float)$_POST['amount'];
    $method = $conn->real_escape_string($_POST['method']);
    $details = $conn->real_escape_string($_POST['details']);
    
    // Check available balance
    $balance = getUserBalance($_SESSION['user_id']);
    
    if ($amount >= 10 && $amount <= $balance) { // Minimum $10 withdrawal
        $conn->query("INSERT INTO withdrawals SET 
            user_id = {$_SESSION['user_id']},
            amount = $amount,
            payment_method = '$method',
            details = '$details',
            status = 'pending'
        ");
        $_SESSION['success'] = "Withdrawal request submitted!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $amount < 10 ? "Minimum withdrawal is $10" : "Insufficient balance";
    }
}

function getUserBalance($user_id) {
    global $conn;
    return $conn->query("
        SELECT SUM(amount) 
        FROM commissions 
        WHERE user_id = $user_id 
        AND status = 'approved'
    ")->fetch_row()[0] ?? 0;
}
include 'includes/header.php';
?>

<div class="container">
    <h2>Withdraw Funds</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Amount (Available: $<?= number_format(getUserBalance($_SESSION['user_id']), 2) ?>)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="10" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="method" class="form-control" required>
                        <option value="">Select Method</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Payment Details</label>
                    <textarea name="details" class="form-control" rows="4" required 
                        placeholder="Bank: Account Name, Account Number, IBAN...
Crypto: Wallet Address
PayPal: Email"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
require 'includes/config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check available balance
$balance_result = $conn->query("
    SELECT SUM(amount) as total 
    FROM payments 
    WHERE user_id = $user_id 
    AND status = 'approved'
");
$balance = $balance_result->fetch_assoc()['total'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    
    if ($amount <= 0) {
        $error = "Invalid amount";
    } elseif ($amount > $balance) {
        $error = "Insufficient balance";
    } else {
        $insert = $conn->query("
            INSERT INTO withdrawals (user_id, amount, status)
            VALUES ($user_id, $amount, 'pending')
        ");
        
        if ($insert) {
            $success = "Withdrawal request submitted!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Withdraw Funds</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Withdraw Funds</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <p>Available Balance: $<?= number_format($balance, 2) ?></p>
                
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Amount to Withdraw</label>
                        <input type="number" name="amount" class="form-control" 
                               step="0.01" min="10" max="<?= $balance ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

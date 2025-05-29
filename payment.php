<?php
session_start();
require 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    $method = $conn->real_escape_string($_POST['method']);
    
    // Simple proof handling
    $proof = '';
    if ($_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $proof = 'proof_'.time().'.jpg';
        move_uploaded_file($_FILES['proof']['tmp_name'], 'uploads/'.$proof);
    }
    
    $sql = "INSERT INTO payments (user_id, amount, method, proof) 
            VALUES ($user_id, $amount, '$method', '$proof')";
    
    if ($conn->query($sql)) {
        $success = "Payment submitted for verification";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Make Payment</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" name="amount" class="form-control" min="10" step="0.01" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select" required>
                                    <option value="">Select method</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Crypto">Cryptocurrency</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Proof</label>
                                <input type="file" name="proof" class="form-control" accept="image/*" required>
                                <div class="form-text">Upload screenshot of payment confirmation</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Submit Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
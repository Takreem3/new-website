<?php
require_once '../includes/auth_check.php';

// Debugging setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle payment verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $payment_id = (int)$_POST['payment_id'];
    $action = $_POST['action'];
    
    // Validate action
    if (!in_array($action, ['verify', 'reject'])) {
        die("Invalid action specified");
    }
    
    // Get user_id from payment
    $user_query = mysqli_query($conn, "SELECT user_id FROM payments WHERE id = $payment_id");
    if (!$user_query || mysqli_num_rows($user_query) === 0) {
        die("Payment record not found");
    }
    
    $user_data = mysqli_fetch_assoc($user_query);
    $user_id = $user_data['user_id'];
    
    // Update payment status
    $status = ($action === 'verify') ? 'verified' : 'rejected';
    $update_payment = mysqli_query($conn, "UPDATE payments SET status = '$status', verified_at = NOW() WHERE id = $payment_id");
    
    if (!$update_payment) {
        die("Payment update failed: " . mysqli_error($conn));
    }
    
    // Update user status
    $update_user = mysqli_query($conn, "UPDATE users SET payment_status = '$status' WHERE id = $user_id");
    
    if (!$update_user) {
        die("User update failed: " . mysqli_error($conn));
    }
    
    $_SESSION['success'] = "Payment $status successfully!";
}

// Get all payment requests
$payments = mysqli_query($conn, "
    SELECT p.*, u.username, u.email 
    FROM payments p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");

if (!$payments) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<!-- Rest of your HTML remains the same -->
<head>
    <title>Payment Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .proof-img { max-width: 300px; cursor: zoom-in; }
        .modal-img { max-width: 100%; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Payment Verification</h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($payment = mysqli_fetch_assoc($payments)): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['username']) ?></td>
                    <td>$<?= number_format($payment['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                    <td><?= htmlspecialchars($payment['transaction_id']) ?></td>
                    <td>
                        <?php if ($payment['payment_proof']): ?>
                            <img src="../uploads/payments/<?= $payment['payment_proof'] ?>" 
                                 class="proof-img" data-bs-toggle="modal" 
                                 data-bs-target="#proofModal"
                                 onclick="document.getElementById('modalImage').src=this.src">
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $payment['status'] == 'verified' ? 'success' : 
                            ($payment['status'] == 'pending' ? 'warning' : 'danger')
                        ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
                    <td>
                        <?php if ($payment['status'] == 'pending'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                <button type="submit" name="action" value="verify" 
                                        class="btn btn-sm btn-success">Verify</button>
                                <button type="submit" name="action" value="reject" 
                                        class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Proof Modal -->
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" class="modal-img">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

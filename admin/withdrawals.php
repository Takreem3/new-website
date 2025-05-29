<?php
require_once '../includes/auth_check.php';

// Debugging setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if requested_at column exists
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM withdrawals LIKE 'requested_at'");
$has_requested_at = ($column_check && mysqli_num_rows($column_check) > 0);

// Get withdrawal requests
$query = "SELECT w.*, u.username 
          FROM withdrawals w
          JOIN users u ON w.user_id = u.id
          ORDER BY " . ($has_requested_at ? "w.requested_at" : "w.created_at") . " DESC";
          
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<!-- Rest of your HTML remains the same -->
<head>
    <title>Withdrawal Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Withdrawal Requests</h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>$<?= number_format($row['amount'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $row['status'] == 'completed' ? 'success' : 
                            ($row['status'] == 'pending' ? 'warning' : 'danger')
                        ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?= date('M d, Y', strtotime($has_requested_at ? $row['requested_at'] : $row['created_at'])) ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="process_withdrawal.php?id=<?= $row['id'] ?>&action=approve" 
                               class="btn btn-sm btn-success">Approve</a>
                            <a href="process_withdrawal.php?id=<?= $row['id'] ?>&action=reject" 
                               class="btn btn-sm btn-danger">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
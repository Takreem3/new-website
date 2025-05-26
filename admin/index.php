<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';

// Secure admin access
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get stats
$users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$pending_withdrawals = $conn->query("SELECT COUNT(*) FROM withdrawals WHERE status='pending'")->fetch_row()[0];
$total_commissions = $conn->query("SELECT SUM(amount) FROM commissions")->fetch_row()[0] ?? 0;

require __DIR__.'/../includes/header.php';
?>

<div class="container-fluid">
    <h2 class="mt-4">Admin Dashboard</h2>
    
    <div class="row mt-4">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h2><?= $users ?></h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h5>Pending Withdrawals</h5>
                    <h2><?= $pending_withdrawals ?></h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5>Total Commissions</h5>
                    <h2>$<?= number_format($total_commissions, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-3">
            <a href="withdrawals.php" class="btn btn-info w-100 mb-2">Withdrawals</a>
        </div>
        <div class="col-md-3">
            <a href="users.php" class="btn btn-secondary w-100 mb-2">User Management</a>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>

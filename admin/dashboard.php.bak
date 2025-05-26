<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';

// Verify admin access
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Dashboard statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$active_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE active=1")->fetch_assoc()['count'];
$today_signups = $conn->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$pending_withdrawals = $conn->query("SELECT COUNT(*) as count FROM withdrawals WHERE status='pending'")->fetch_assoc()['count'];

require __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?= $total_users ?></h2>
                    <a href="users.php" class="text-white">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <h2><?= $active_users ?></h2>
                    <a href="users.php?filter=active" class="text-white">View Active</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Signups</h5>
                    <h2><?= $today_signups ?></h2>
                    <a href="users.php?filter=today" class="text-white">View New</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Withdrawals</h5>
                    <h2><?= $pending_withdrawals ?></h2>
                    <a href="../withdrawals.php?status=pending" class="text-dark">Process</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Recent Signups</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Sponsor</th>
                            <th>Join Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_users = $conn->query("
                            SELECT u.id, u.username, u.email, u.created_at, 
                                   s.username as sponsor_name
                            FROM users u
                            LEFT JOIN users s ON u.sponsor_id = s.id
                            ORDER BY u.id DESC LIMIT 5
                        ");
                        
                        while($user = $recent_users->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['sponsor_name'] ? htmlspecialchars($user['sponsor_name']) : 'None' ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h5>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="users.php?action=add" class="btn btn-primary">Add New User</a>
                <a href="../withdrawals.php" class="btn btn-secondary">Manage Withdrawals</a>
                <a href="reports.php" class="btn btn-info">View Reports</a>
                <a href="settings.php" class="btn btn-warning">System Settings</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>
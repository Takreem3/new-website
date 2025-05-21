<?php 
include 'includes/mlm_commission.php';

// Call this daily via cron job or when purchases occur
calculateBinaryCommissions($_SESSION['user_id']);
calculateUnilevelCommissions($sponsor_id, 100); // $100 purchase
?>
include 'includes/config.php';
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT u.*, ud.full_name FROM users u 
                     LEFT JOIN user_details ud ON u.id = ud.user_id 
                     WHERE u.id = $user_id")->fetch_assoc();

// Get commission summary
$commissions = $conn->query("SELECT SUM(amount) as total, type 
                            FROM commissions 
                            WHERE user_id = $user_id 
                            GROUP BY type");
?>
<div class="card mt-4">
    <div class="card-header">
        <h5>Your Network</h5>
    </div>
    <div class="card-body">
        <?php include 'includes/network_tree.php'; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                User Profile
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                <p><strong>Name:</strong> <?php echo $user['full_name'] ?? 'Not set'; ?></p>
                <p><strong>Sponsor ID:</strong> <?php echo $user['sponsor_id'] ?? 'None'; ?></p>
                <a href="profile.php" class="btn btn-sm btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Commission Summary
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $commissions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo ucfirst($row['type']); ?></td>
                            <td>$<?php echo number_format($row['total'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<div class="card mt-4">
    <div class="card-header">
        <h5>Your Earnings</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Level</th>
                    <th>Amount</th>
                    <th>From User</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $earnings = $conn->query("
                    SELECT c.*, u.username as from_user 
                    FROM commissions c 
                    LEFT JOIN users u ON c.from_user_id = u.id 
                    WHERE c.user_id = $user_id
                ");
                while($row = $earnings->fetch_assoc()): ?>
                    <tr>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td><?= $row['level'] ?></td>
                        <td>$<?= number_format($row['amount'], 2) ?></td>
                        <td><?= $row['from_user'] ?? 'System' ?></td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mb-3">
    <h6>Your Referral Link:</h6>
    <div class="input-group">
        <input type="text" id="referralLink" 
               value="<?= BASE_URL ?>register.php?ref=<?= $user['username'] ?>" 
               class="form-control" readonly>
        <button class="btn btn-outline-secondary" onclick="copyReferralLink()">
            Copy
        </button>
    </div>
</div>

<script>
function copyReferralLink() {
    const link = document.getElementById("referralLink");
    link.select();
    document.execCommand("copy");
    alert("Copied to clipboard!");
}
</script>
<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

require __DIR__.'/includes/header.php';
?>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?></h2>

    <!-- Account Summary Card -->
    <div class="card mt-4">
        <div class="card-header">
            Your Account Summary
        </div>
        <div class="card-body">
            <!-- Add your summary content here -->
        </div>
    </div>

    <!-- ✅ Network Tree Visualization -->
    <div class="row mt-4">
        <div class="col-md-12">
            <?php include __DIR__.'/includes/network_tree.php'; ?>
        </div>
    </div>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>

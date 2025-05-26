<?php
require __DIR__.'/../includes/config.php';

// Check admin authentication
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__.'/../includes/header.php';
?>

<div class="container-fluid">
    <h2>Admin Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <?php
                    $users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                    ?>
                    <h3><?= $users ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>

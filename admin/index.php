<?php
require 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="payments.php">Payments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="withdrawals.php">Withdrawals</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="kyc.php">KYC</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2>Dashboard Overview</h2>
                <div class="row my-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as total FROM users");
                                $total = $result->fetch_assoc()['total'];
                                ?>
                                <p class="card-text display-4"><?= $total ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Active Users</h5>
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE payment_status = 'verified'");
                                $total = $result->fetch_assoc()['total'];
                                ?>
                                <p class="card-text display-4"><?= $total ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Pending Actions</h5>
                                <?php
                                $result = $conn->query("SELECT (SELECT COUNT(*) FROM payments WHERE status = 'pending') as total");
                                $total = $result->fetch_assoc()['total'];
                                ?>
                                <p class="card-text display-4"><?= $total ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
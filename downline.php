<?php
session_start();
require __DIR__.'/includes/config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
$downline = [];

// Get downline members
if (!empty($user_data['tree_path'])) {
    $path = $user_data['tree_path'];
    $result = mysqli_query($conn, "SELECT id, username, tree_path, level FROM users 
        WHERE tree_path LIKE '".$path."%' AND id != $user_id
        ORDER BY tree_path");
    
    while ($row = mysqli_fetch_assoc($result)) {
        $downline_level = substr_count($row['tree_path'], '/') - substr_count($path, '/');
        $downline[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'level' => $downline_level
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Downline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tree-node { margin-left: 30px; }
        .tree-connector { 
            position: relative;
            margin-left: 20px;
        }
        .tree-connector::before {
            content: "";
            position: absolute;
            top: -18px;
            left: -20px;
            width: 1px;
            height: 100%;
            background: #6c757d;
        }
        .tree-connector::after {
            content: "";
            position: absolute;
            top: 18px;
            left: -20px;
            width: 20px;
            height: 1px;
            background: #6c757d;
        }
        .user-badge {
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 8px 0;
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }
        .user-badge:hover {
            background: #d8e2dc;
            transform: translateX(5px);
        }
        .level-0 { background: #cfe2ff; border-color: #9ec5fe; }
        .level-1 { background: #d1e7dd; border-color: #a3cfbb; }
        .level-2 { background: #fff3cd; border-color: #ffda6a; }
        .level-3 { background: #f8d7da; border-color: #f1aeb5; }
        .tree-header {
            background: #0d6efd;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">My Downline</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="downline.php">Downline</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="tree-header">
            <h4 class="mb-0">Downline Structure for <?= $user_data['username'] ?></h4>
            <p class="mb-0">Total Members: <?= count($downline) ?></p>
        </div>
        
        <?php if (empty($downline)): ?>
            <div class="alert alert-info">
                You don't have any downline members yet. Share your referral link to grow your team!
            </div>
        <?php else: ?>
            <div class="tree-structure">
                <div class="user-badge level-0">
                    <strong><?= $user_data['username'] ?></strong> (You - Level 0)
                </div>
                <?= buildDownlineTree($downline) ?>
            </div>
            
            <div class="mt-5">
                <h4>Downline List</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Join Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($downline as $member): ?>
                        <tr>
                            <td><?= $member['username'] ?></td>
                            <td><?= $member['level'] ?></td>
                            <td>
                                <?php 
                                    $join_date = mysqli_fetch_assoc(mysqli_query($conn, 
                                        "SELECT created_at FROM users WHERE id = ".$member['id']))['created_at'];
                                    echo date('M d, Y', strtotime($join_date));
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Recursive function to build downline tree
function buildDownlineTree($members, $level = 1) {
    $currentLevel = [];
    $nextLevel = [];
    
    foreach ($members as $member) {
        if ($member['level'] == $level) {
            $currentLevel[] = $member;
        } else {
            $nextLevel[] = $member;
        }
    }
    
    if (empty($currentLevel)) return '';
    
    $html = '<div class="tree-node">';
    foreach ($currentLevel as $member) {
        $html .= '<div class="tree-connector">';
        $html .= '<div class="user-badge level-'.$level.'">';
        $html .= $member['username'] . ' (Level ' . $level . ')';
        $html .= '</div>';
        $html .= buildDownlineTree($nextLevel, $level + 1);
        $html .= '</div>';
    }
    $html .= '</div>';
    
    return $html;
}
?>
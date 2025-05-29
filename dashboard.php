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
    
    if (empty($currentLevel)) {
        return '';
    }
    
    $html = '<div class="tree-node">';
    foreach ($currentLevel as $member) {
        $html .= '<div class="tree-connector">';
        $html .= '<div class="user-badge level-'.$level.'">';
        $html .= htmlspecialchars($member['username']) . ' (Level ' . $level . ')';
        $html .= '</div>';
        $html .= buildDownlineTree($nextLevel, $level + 1);
        $html .= '</div>';
    }
    $html .= '</div>';
    
    return $html;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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
        }
        .level-0 { background: #cfe2ff; }
        .level-1 { background: #d1e7dd; }
        .level-2 { background: #fff3cd; }
        .level-3 { background: #f8d7da; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Account Summary</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Username:</strong> <?= htmlspecialchars($user_data['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
                        <p><strong>Downline Count:</strong> <?= count($downline) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>My Downline Tree</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($downline)): ?>
                            <p>No downline members found.</p>
                        <?php else: ?>
                            <div class="tree-structure">
                                <div class="user-badge level-0">
                                    <?= htmlspecialchars($user_data['username']) ?> (You)
                                </div>
                                <?= buildDownlineTree($downline) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
authOnly();

$user_id = $_SESSION['user_id'];

// Get user's network tree
$tree = $conn->query("
    WITH RECURSIVE downline AS (
        SELECT id, username, sponsor_id, 1 AS level 
        FROM users WHERE id = $user_id
        
        UNION ALL
        
        SELECT u.id, u.username, u.sponsor_id, d.level + 1
        FROM users u
        JOIN downline d ON u.sponsor_id = d.id
    )
    SELECT * FROM downline ORDER BY level, id
");

require __DIR__.'/includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Your Network Tree</h2>
    
    <?php if ($tree->num_rows > 0): ?>
        <div class="tree-container">
            <?php while ($member = $tree->fetch_assoc()): ?>
                <div class="tree-node level-<?= $member['level'] ?>">
                    <div class="node-card">
                        <h5><?= htmlspecialchars($member['username']) ?></h5>
                        <p>Level: <?= $member['level'] ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No downline members found</div>
    <?php endif; ?>
</div>

<style>
    .tree-container {
        margin-left: 20px;
    }
    .tree-node {
        margin: 10px 0;
        padding-left: 20px;
        border-left: 2px solid #ddd;
    }
    .level-1 { margin-left: 0; border-left: none; }
    .node-card {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
</style>

<?php require __DIR__.'/includes/footer.php'; ?>
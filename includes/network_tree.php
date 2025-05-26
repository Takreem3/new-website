<?php
// Security check
if(!defined('IN_MLM')) {
    die("Direct access denied");
}

function buildNetworkTree($conn, $user_id, $level = 0, $max_level = 3) {
    if($level >= $max_level) return '';
    
    $html = '<ul class="tree-level-'.$level.'">';
    
    $stmt = $conn->prepare("SELECT id, username, position FROM users WHERE sponsor_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $downline = $stmt->get_result();
    
    while($member = $downline->fetch_assoc()) {
        // Get member stats
        $stats = $conn->query("
            SELECT COUNT(*) as team_size,
                   (SELECT SUM(amount) FROM commissions WHERE user_id = ".$member['id'].") as earnings
            FROM users WHERE sponsor_id = ".$member['id']."
        ")->fetch_assoc();
        
        $html .= '<li>';
        $html .= '<div class="node '.$member['position'].'">';
        $html .= '<span class="username">'.$member['username'].'</span>';
        $html .= '<div class="stats">';
        $html .= '<span>Team: '.$stats['team_size'].'</span>';
        $html .= '<span>Earnings: $'.number_format($stats['earnings'] ?? 0, 2).'</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= buildNetworkTree($conn, $member['id'], $level + 1, $max_level);
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    return $html;
}
?>

<div class="network-tree">
    <div class="tree-controls mb-3">
        <button class="btn btn-sm btn-outline-primary expand-btn">Expand All</button>
        <button class="btn btn-sm btn-outline-secondary collapse-btn">Collapse All</button>
    </div>
    
    <div class="tree-wrapper">
        <?= buildNetworkTree($conn, $_SESSION['user_id'], 0, 3) ?>
    </div>
</div>

<style>
.network-tree ul {
    list-style-type: none;
    padding-left: 30px;
    position: relative;
}

.network-tree li {
    margin: 15px 0;
    position: relative;
}

.node {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px 15px;
    display: inline-block;
    min-width: 220px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.node.left { border-left: 4px solid #0d6efd; }
.node.right { border-left: 4px solid #dc3545; }

.username {
    font-weight: 600;
    color: #212529;
}

.stats {
    font-size: 12px;
    margin-top: 5px;
    color: #6c757d;
}

.stats span {
    margin-right: 10px;
}

.tree-controls {
    display: flex;
    gap: 10px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize with level 1 visible
    $('.tree-level-1, .tree-level-2').hide();
    
    $('.expand-btn').click(function() {
        $('.network-tree ul').show();
    });
    
    $('.collapse-btn').click(function() {
        $('.tree-level-1, .tree-level-2').hide();
    });
});
</script>

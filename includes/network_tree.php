<?php
$user_id = $_SESSION['user_id'];
$levels = 3; // Display 3 levels deep

function buildTree($conn, $user_id, $currentLevel = 0, $maxLevel = 3) {
    if($currentLevel >= $maxLevel) return '';
    
    $html = '<ul>';
    $children = $conn->query("
        SELECT id, username, position 
        FROM users 
        WHERE sponsor_id = $user_id
    ");
    
    while($child = $children->fetch_assoc()) {
        $html .= '<li>';
        $html .= $child['username'] . ' (' . $child['position'] . ')';
        $html .= buildTree($conn, $child['id'], $currentLevel+1, $maxLevel);
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    return $html;
}
?>

<div class="network-tree">
    <h5>Your Network</h5>
    <?= buildTree($conn, $user_id, 0, $levels) ?>
</div>

<style>
.network-tree ul {
    list-style-type: none;
    padding-left: 20px;
}
.network-tree li {
    margin: 5px 0;
    position: relative;
}
</style>
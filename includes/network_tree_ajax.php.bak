<?php
require __DIR__.'/config.php';
require __DIR__.'/auth.php';

if(!isset($_SESSION['user_id'])) die('Unauthorized');

header('Content-Type: text/html');
$levels = isset($_POST['levels']) ? (int)$_POST['levels'] : 3;

function buildTreePartial($conn, $user_id, $current_level, $max_level) {
    // Same function as in network_tree.php
    // ...
}

echo buildTreePartial($conn, $_SESSION['user_id'], 0, $levels);
?>
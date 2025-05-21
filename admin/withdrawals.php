<?php
include '../includes/config.php';
adminOnly();

// Process actions
if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'approve') {
        $conn->query("UPDATE withdrawals SET status='approved', processed_at=NOW() WHERE id=$id");
    } elseif ($_GET['action'] == 'reject') {
        $conn->query("UPDATE withdrawals SET status='rejected', processed_at=NOW() WHERE id=$id");
    }
}

$withdrawals = $conn->query("SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY created_at DESC");
?>

<!-- Table with:
- Filter options
- Bulk actions
- Detailed view modal -->
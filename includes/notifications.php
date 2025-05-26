<?php
require __DIR__.'/config.php';
require __DIR__.'/auth.php';

// Mark notification as read if ID provided
if(isset($_GET['mark_as_read'])) {
    $id = (int)$_GET['mark_as_read'];
    $conn->query("UPDATE notifications SET is_read=1 WHERE id=$id AND user_id={$_SESSION['user_id']}");
}

// Get user notifications
$notifications = $conn->query("
    SELECT * FROM notifications 
    WHERE user_id = {$_SESSION['user_id']}
    ORDER BY created_at DESC
");

require 'header.php';
?>

<div class="container mt-4">
    <h2>Your Notifications</h2>
    
    <div class="list-group">
        <?php while($note = $notifications->fetch_assoc()): ?>
        <a href="notifications.php?mark_as_read=<?= $note['id'] ?>" 
           class="list-group-item list-group-item-action <?= $note['is_read'] ? '' : 'list-group-item-primary' ?>">
            <div class="d-flex justify-content-between">
                <h5><?= htmlspecialchars($note['title']) ?></h5>
                <small><?= date('M j, Y g:i a', strtotime($note['created_at'])) ?></small>
            </div>
            <p><?= htmlspecialchars($note['message']) ?></p>
        </a>
        <?php endwhile; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
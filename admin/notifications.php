<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';
require __DIR__.'/../includes/notification_functions.php';

// Admin check
if($_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_POST['user_id'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    
    if(!empty($title) && !empty($message)) {
        if(sendNotification($conn, $user_id, $title, $message)) {
            $_SESSION['success'] = "Notification sent successfully!";
        } else {
            $_SESSION['error'] = "Failed to send notification";
        }
    }
}

// Get recent notifications
$recent_notifications = [];
$result = $conn->query("
    SELECT n.*, u.username 
    FROM notifications n
    JOIN users u ON n.user_id = u.id
    ORDER BY n.id DESC LIMIT 10
");
if($result) {
    while($row = $result->fetch_assoc()) {
        $recent_notifications[] = $row;
    }
}

require __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">
    <h2>Notification Management</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Send Notification</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">User ID</label>
                            <input type="number" name="user_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Notifications</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_notifications as $note): ?>
                                <tr>
                                    <td><?= htmlspecialchars($note['username']) ?></td>
                                    <td><?= htmlspecialchars($note['title']) ?></td>
                                    <td><?= date('M j, Y g:i a', strtotime($note['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $note['is_read'] ? 'success' : 'warning' ?>">
                                            <?= $note['is_read'] ? 'Read' : 'Unread' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>

<?php
// Prevent direct access
defined('BASEPATH') || exit;

function sendNotification($conn, $user_id, $title, $message) {
    $title = $conn->real_escape_string($title);
    $message = $conn->real_escape_string($message);
    
    $query = $conn->query("
        INSERT INTO notifications (user_id, title, message)
        VALUES ($user_id, '$title', '$message')
    ");
    
    if(!$query) {
        error_log("Notification Error: " . $conn->error);
        return false;
    }
    return $conn->insert_id;
}

function getUnreadCount($conn, $user_id) {
    $result = $conn->query("
        SELECT COUNT(*) as count 
        FROM notifications 
        WHERE user_id = $user_id AND is_read = 0
    ");
    
    if(!$result) {
        error_log("Unread Count Error: " . $conn->error);
        return 0;
    }
    return $result->fetch_assoc()['count'];
}

// For admin - get all user notifications
function getUserNotifications($conn, $user_id, $limit = 10) {
    $result = $conn->query("
        SELECT * FROM notifications 
        WHERE user_id = $user_id
        ORDER BY created_at DESC
        LIMIT $limit
    ");
    
    if(!$result) {
        error_log("Notifications Error: " . $conn->error);
        return [];
    }
    
    $notifications = [];
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}
?>
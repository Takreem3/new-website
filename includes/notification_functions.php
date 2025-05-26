<?php
if (!function_exists('getUnreadCount')) {
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
}

if (!function_exists('sendNotification')) {
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
}
?>

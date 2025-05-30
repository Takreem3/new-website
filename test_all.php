<?php
require __DIR__.'/includes/config.php';

// Explicit session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tests = [
    'Database Connection' => fn() => $conn->ping(),
    'Session Working' => fn() => isset($_SESSION['user_id']) || session_status() === PHP_SESSION_ACTIVE,
    'Email Setup' => function() {
        require 'includes/mail_config.php';
        try {
            return sendMLMEmail('test@test.com', 'Test', 'Test body');
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
];

header('Content-Type: text/html');
foreach ($tests as $name => $test) {
    echo "<p><strong>$name:</strong> " . ($test() ? "✅ PASSED" : "❌ FAILED") . "</p>";
}
?>

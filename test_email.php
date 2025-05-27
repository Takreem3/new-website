<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/mail_config.php';

if (sendMLMEmail('recipient@email.com', 'Test Email', '<h1>It works!</h1><p>System emails are configured correctly.</p>')) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}
?>
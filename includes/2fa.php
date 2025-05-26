<?php
require_once 'vendor/autoload.php'; // Install with: composer require pragmarx/google2fa

function setup2FA($user_id) {
    $google2fa = new \PragmaRX\Google2FA\Google2FA();
    $secret = $google2fa->generateSecretKey();
    
    // Store in database
    global $conn;
    $conn->query("
        UPDATE admin 
        SET twofa_secret = '$secret' 
        WHERE id = $user_id
    ");
    
    return $google2fa->getQRCodeUrl(
        'Your MLM System',
        'admin@yourdomain.com',
        $secret
    );
}

function verify2FA($user_id, $code) {
    $google2fa = new \PragmaRX\Google2FA\Google2FA();
    
    // Get secret from DB
    global $conn;
    $secret = $conn->query("
        SELECT twofa_secret FROM admin WHERE id = $user_id
    ")->fetch_row()[0];
    
    return $google2fa->verifyKey($secret, $code);
}
?>


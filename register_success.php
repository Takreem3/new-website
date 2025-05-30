<?php
session_start();
if (empty($_SESSION['registration_success'])) {
    header("Location: register.php");
    exit();
}
unset($_SESSION['registration_success']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
</head>
<body>
    <h1>Registration Successful!</h1>
    <p>Your account has been created. <a href="login.php">Login here</a></p>
</body>
</html>

<?php
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_password($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters";
    }
    return null;
}
?>

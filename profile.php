<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';

// Get user data
$stmt = $conn->prepare("SELECT u.*, ud.* 
                       FROM users u 
                       LEFT JOIN user_details ud ON u.id = ud.user_id 
                       WHERE u.id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    $conn->query("
        INSERT INTO user_details (user_id, full_name, phone, address)
        VALUES ({$_SESSION['user_id']}, '$fullname', '$phone', '$address')
        ON DUPLICATE KEY UPDATE
        full_name = '$fullname',
        phone = '$phone',
        address = '$address'
    ");
    
    $_SESSION['success'] = "Profile updated!";
    header("Location: profile.php");
    exit();
}

require __DIR__.'/includes/header.php';
?>

<div class="container">
    <h2>Your Profile</h2>
    
    <form method="POST">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control" 
                   value="<?= $user['full_name'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" 
                   value="<?= $user['email'] ?>" readonly>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" 
                   value="<?= $user['phone'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" required><?= $user['address'] ?? '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>

<?php
session_start();
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current user data
$stmt = $conn->prepare("SELECT email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <!-- CSRF Token (Add this to ALL forms) -->
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>">

            <!-- Email -->
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control">
            </div>

            <!-- Profile Picture -->
            <div class="mb-3">
                <label>Profile Picture:</label>
                <input type="file" name="profile_pic" class="form-control">
                <?php if ($user['profile_pic']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>


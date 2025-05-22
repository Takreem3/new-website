<?php
// ======================
// SECURITY & SESSION CHECKS
// ======================
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

// ======================
// SECURE DATA FETCHING
// ======================
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? false; // Admin check

// Fetch user data (prepared statement)
$user_query = "SELECT username, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if (!$user_result || $user_result->num_rows === 0) {
    die("User not found!");
}
$user = $user_result->fetch_assoc();

// Fetch additional data (e.g., user posts)
$posts_query = "SELECT id, title, content FROM posts WHERE user_id = ?";
$stmt_posts = $conn->prepare($posts_query);
$stmt_posts->bind_param("i", $user_id);
$stmt_posts->execute();
$posts_result = $stmt_posts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo htmlspecialchars($user['username']); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ======================
         YOUR EXISTING SIDEBAR 
         ====================== -->
    <?php include('includes/sidebar.php'); ?>

    <div class="main-content">
        <!-- ======================
             ENHANCED USER PROFILE SECTION  
             ====================== -->
        <div class="container py-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</h2>
                    <?php if ($is_admin): ?>
                        <span class="badge bg-danger">Admin</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="edit_profile.php" class="btn btn-warning">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================
                 YOUR POSTS SECTION (EXAMPLE)
                 ====================== -->
            <div class="card mt-4 shadow">
                <div class="card-header">
                    <h3>Your Recent Posts</h3>
                </div>
                <div class="card-body">
                    <?php if ($posts_result->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while ($post = $posts_result->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <h5><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    <small class="text-muted">
                                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </small>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No posts yet. <a href="new_post.php">Create one?</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================
         FOOTER & SCRIPTS 
         ====================== -->
    <?php include('includes/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
// Close connections
$stmt->close();
$stmt_posts->close();
$conn->close();
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <h2>Create New Post</h2>
        <form action="save_post.php" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>">

            <!-- Post Title -->
            <div class="mb-3">
                <label>Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <!-- Post Content -->
            <div class="mb-3">
                <label>Content:</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-success">Publish</button>
        </form>
    </div>
</body>
</html>
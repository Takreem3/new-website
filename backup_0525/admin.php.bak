<?php
// ======================
// SECURITY & AUTHENTICATION
// ======================
session_start();
require '../config.php'; // Adjust path if needed

// 1. Check if user is logged in AND admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

// 2. Generate CSRF token for forms
$_SESSION['admin_token'] = bin2hex(random_bytes(32));

// 3. Handle user deletion (if requested)
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    // Validate CSRF token for actions
    if (!isset($_GET['token']) || !hash_equals($_SESSION['admin_token'], $_GET['token'])) {
        die("Security error: Invalid token!");
    }

    $user_id_to_delete = (int)$_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0"); // Prevent deleting admins
    $stmt->bind_param("i", $user_id_to_delete);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Error: Cannot delete admin users or invalid ID.";
    }
}

// 4. Fetch all users
$users = $conn->query("SELECT id, username, email, is_admin, created_at FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admin-badge { background-color: #dc3545; }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-5">
        <h1 class="mb-4">Admin Dashboard</h1>

        <!-- Status Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- User Management Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h3>User Management</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge admin-badge">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if (!$user['is_admin']): ?>
                                        <a href="?delete_user=<?php echo $user['id']; ?>&token=<?php echo $_SESSION['admin_token']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Permanently delete this user?')">
                                            Delete
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Protected</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admin Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <?php
                        $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                        echo "<h2>$total_users</h2>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5>Regular Users</h5>
                        <?php
                        $regular_users = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetch_row()[0];
                        echo "<h2>$regular_users</h2>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5>Admins</h5>
                        <?php
                        $admins = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetch_row()[0];
                        echo "<h2>$admins</h2>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm before deletion
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this user?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

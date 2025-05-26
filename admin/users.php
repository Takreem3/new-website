<?php
require __DIR__.'/../includes/config.php';
require __DIR__.'/../includes/auth.php';

// Search functionality
$search = $_GET['search'] ?? '';
$where = $search ? "WHERE username LIKE '%$search%' OR email LIKE '%$search%'" : "";

$users = $conn->query("
    SELECT u.*, 
           (SELECT COUNT(*) FROM users WHERE sponsor_id = u.id) as downline_count
    FROM users u
    $where
    ORDER BY u.id DESC
");

require __DIR__.'/../includes/header.php';
?>

<div class="container-fluid">
    <h2>User Management</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Downline</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['downline_count'] ?></td>
                        <td>
                            <span class="badge bg-<?= $user['active'] ? 'success' : 'danger' ?>">
                                <?= $user['active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__.'/../includes/footer.php'; ?>

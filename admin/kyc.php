<?php
include '../includes/config.php';
include '../includes/auth.php';
adminOnly();

// Handle approvals/rejections
if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'approve') {
        $conn->query("UPDATE kyc_verifications SET status='approved', reviewed_at=NOW() WHERE id=$id");
    } elseif ($_GET['action'] == 'reject') {
        $conn->query("UPDATE kyc_verifications SET status='rejected', reviewed_at=NOW() WHERE id=$id");
    }
    header("Location: kyc.php");
    exit();
}

$kycRequests = $conn->query("
    SELECT k.*, u.username 
    FROM kyc_verifications k
    JOIN users u ON k.user_id = u.id
    WHERE k.status='pending'
");
include '../includes/header.php';
?>

<div class="container-fluid">
    <h2>Pending KYC Verifications</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Document Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($request = $kycRequests->fetch_assoc()): ?>
                <tr>
                    <td><?= $request['id'] ?></td>
                    <td><?= $request['username'] ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $request['document_type'])) ?></td>
                    <td>
                        <a href="view_kyc.php?id=<?= $request['id'] ?>" class="btn btn-sm btn-info">View</a>
                        <a href="kyc.php?action=approve&id=<?= $request['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                        <a href="kyc.php?action=reject&id=<?= $request['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
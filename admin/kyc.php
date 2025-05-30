<?php
require 'includes/auth_check.php';
$kyc = $conn->query("
    SELECT k.*, u.username 
    FROM kyc_verifications k
    JOIN users u ON k.user_id = u.id
    ORDER BY k.submitted_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>KYC Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kyc-img { max-width: 200px; cursor: pointer; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-4">
        <h2>KYC Submissions</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Document</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($doc = $kyc->fetch_assoc()): ?>
                <tr>
                    <td><?= $doc['id'] ?></td>
                    <td><?= $doc['username'] ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $doc['document_type'])) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $doc['status'] == 'verified' ? 'success' : 
                            ($doc['status'] == 'pending' ? 'warning' : 'danger')
                        ?>">
                            <?= ucfirst($doc['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($doc['submitted_at'])) ?></td>
                    <td>
                        <a href="view_kyc.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-primary">View</a>
                        <?php if ($doc['status'] == 'pending'): ?>
                            <a href="process_kyc.php?id=<?= $doc['id'] ?>&action=approve" class="btn btn-sm btn-success">Approve</a>
                            <a href="process_kyc.php?id=<?= $doc['id'] ?>&action=reject" class="btn btn-sm btn-danger">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

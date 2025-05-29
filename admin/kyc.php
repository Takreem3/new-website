<?php
require_once '../includes/auth_check.php';

// Debugging setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle KYC verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $kyc_id = (int)$_POST['kyc_id'];
    $action = $_POST['action'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    // Validate action
    if (!in_array($action, ['verify', 'reject'])) {
        die("Invalid action specified");
    }
    
    $status = ($action === 'verify') ? 'verified' : 'rejected';
    
    $update = mysqli_query($conn, "UPDATE kyc_verifications SET 
        status = '$status',
        verified_at = NOW(),
        notes = '$notes'
        WHERE id = $kyc_id");
    
    if (!$update) {
        die("KYC update failed: " . mysqli_error($conn));
    }
    
    $_SESSION['success'] = "KYC $status successfully!";
}

// Get all KYC requests
$kyc_requests = mysqli_query($conn, "
    SELECT k.*, u.username, u.email 
    FROM kyc_verifications k
    JOIN users u ON k.user_id = u.id
    ORDER BY k.submitted_at DESC
");

if (!$kyc_requests) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<!-- Rest of your HTML remains the same -->
<head>
    <title>KYC Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kyc-image { max-width: 200px; cursor: pointer; }
        .modal-kyc-img { max-width: 100%; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>KYC Verification</h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Document Type</th>
                    <th>Front</th>
                    <th>Back</th>
                    <th>Selfie</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($kyc = mysqli_fetch_assoc($kyc_requests)): ?>
                <tr>
                    <td><?= htmlspecialchars($kyc['username']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $kyc['document_type'])) ?></td>
                    <td>
                        <img src="../uploads/kyc/<?= $kyc['front_image'] ?>" 
                             class="kyc-image" onclick="showImage(this.src)">
                    </td>
                    <td>
                        <?php if ($kyc['back_image']): ?>
                            <img src="../uploads/kyc/<?= $kyc['back_image'] ?>" 
                                 class="kyc-image" onclick="showImage(this.src)">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($kyc['selfie_image']): ?>
                            <img src="../uploads/kyc/<?= $kyc['selfie_image'] ?>" 
                                 class="kyc-image" onclick="showImage(this.src)">
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $kyc['status'] == 'verified' ? 'success' : 
                            ($kyc['status'] == 'pending' ? 'warning' : 'danger')
                        ?>">
                            <?= ucfirst($kyc['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($kyc['submitted_at'])) ?></td>
                    <td>
                        <?php if ($kyc['status'] == 'pending'): ?>
                            <form method="post">
                                <input type="hidden" name="kyc_id" value="<?= $kyc['id'] ?>">
                                <textarea name="notes" class="form-control mb-2" 
                                          placeholder="Verification notes"></textarea>
                                <button type="submit" name="action" value="verify" 
                                        class="btn btn-sm btn-success">Approve</button>
                                <button type="submit" name="action" value="reject" 
                                        class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalKycImage" class="modal-kyc-img">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImage(src) {
            document.getElementById('modalKycImage').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }
    </script>
</body>
</html>
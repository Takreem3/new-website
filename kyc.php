<?php
include 'includes/config.php';
include 'includes/auth.php';

// Check existing KYC status
$kycStatus = $conn->query("
    SELECT status FROM kyc_verifications 
    WHERE user_id = {$_SESSION['user_id']} 
    ORDER BY submitted_at DESC LIMIT 1
")->fetch_assoc()['status'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $kycStatus != 'pending') {
    $uploadDir = 'assets/kyc/';
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    
    // Validate files
    $frontImage = validateUpload($_FILES['front_image'], $uploadDir, $allowedTypes);
    $selfieImage = validateUpload($_FILES['selfie_image'], $uploadDir, $allowedTypes);
    $backImage = !empty($_FILES['back_image']['name']) 
        ? validateUpload($_FILES['back_image'], $uploadDir, $allowedTypes) 
        : null;

    if ($frontImage && $selfieImage) {
        $conn->query("INSERT INTO kyc_verifications SET 
            user_id = {$_SESSION['user_id']},
            document_type = '{$_POST['document_type']}',
            front_image = '$frontImage',
            back_image = " . ($backImage ? "'$backImage'" : "NULL") . ",
            selfie_image = '$selfieImage',
            status = 'pending'
        ");
        $_SESSION['success'] = "KYC submitted for verification!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid file uploads";
    }
}

function validateUpload($file, $dir, $allowedTypes) {
    if (!in_array($file['type'], $allowedTypes)) return false;
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "kyc_".$_SESSION['user_id']."_".time().".".$ext;
    move_uploaded_file($file['tmp_name'], $dir.$filename);
    return $filename;
}

include 'includes/header.php';
?>

<div class="container py-4">
    <?php if ($kycStatus == 'pending'): ?>
        <div class="alert alert-info">Your KYC is under review</div>
    <?php elseif ($kycStatus == 'approved'): ?>
        <div class="alert alert-success">Your account is verified</div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Identity Verification</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Document Type*</label>
                            <select name="document_type" class="form-select" required>
                                <option value="">Select Document</option>
                                <option value="id_card">National ID Card</option>
                                <option value="passport">Passport</option>
                                <option value="driving_license">Driver's License</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Document Front*</label>
                            <input type="file" name="front_image" class="form-control" accept="image/*,.pdf" required>
                            <small class="text-muted">Clear photo/scan of document front</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Document Back</label>
                            <input type="file" name="back_image" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted">Required for ID cards/driver licenses</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Selfie with Document*</label>
                            <input type="file" name="selfie_image" class="form-control" accept="image/*" required>
                            <small class="text-muted">Hold document near your face</small>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                Submit Verification
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
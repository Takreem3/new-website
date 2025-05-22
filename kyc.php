<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/config.php';
include 'includes/auth.php';

// Check existing KYC status
$stmt = $conn->prepare("SELECT status FROM kyc_verifications WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$kycStatus = $stmt->get_result()->fetch_assoc()['status'] ?? null;

if($_SERVER['REQUEST_METHOD'] == 'POST' && $kycStatus != 'pending') {
    $uploadDir = 'assets/kyc/';
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $frontImage = uploadFile('front_image', $uploadDir);
    $selfieImage = uploadFile('selfie_image', $uploadDir);
    $backImage = !empty($_FILES['back_image']['name']) ? uploadFile('back_image', $uploadDir) : null;

    if($frontImage && $selfieImage) {
        $stmt = $conn->prepare("INSERT INTO kyc_verifications (user_id, document_type, front_image, back_image, selfie_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['user_id'], $_POST['document_type'], $frontImage, $backImage, $selfieImage);
        
        if($stmt->execute()) {
            $_SESSION['success'] = "KYC submitted successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Database error: ".$conn->error;
        }
    } else {
        $error = "Please upload all required files (Front + Selfie)";
    }
}

function uploadFile($field, $dir) {
    $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
    
    if(!isset($_FILES[$field]) || $_FILES[$field]['error'] != UPLOAD_ERR_OK) {
        return false;
    }
    
    if(!in_array($_FILES[$field]['type'], $allowed)) {
        return false;
    }
    
    $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $filename = "kyc_".$_SESSION['user_id']."_".time()."_$field.$ext";
    $target = $dir.$filename;
    
    if(move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        return $filename;
    }
    return false;
}

include 'includes/header.php';
?>

<div class="container py-4">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if($kycStatus == 'pending'): ?>
        <div class="alert alert-info">Your KYC verification is in progress</div>
    <?php elseif($kycStatus == 'approved'): ?>
        <div class="alert alert-success">Your account is verified</div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Identity Verification</h4>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Document Type*</label>
                        <select name="document_type" class="form-control" required>
                            <option value="">Select Document</option>
                            <option value="id_card">National ID Card</option>
                            <option value="passport">Passport</option>
                            <option value="driving_license">Driver's License</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Document Front*</label>
                        <input type="file" name="front_image" class="form-control" accept="image/*,.pdf" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Document Back</label>
                        <input type="file" name="back_image" class="form-control" accept="image/*,.pdf">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Selfie with Document*</label>
                        <input type="file" name="selfie_image" class="form-control" accept="image/*" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Verification</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
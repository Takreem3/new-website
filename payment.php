<?php
session_start();
require __DIR__.'/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $amount = (float)$_POST['amount'];
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $transaction_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
    
    // Handle file upload
    $proof_path = '';
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $target_dir = __DIR__."/uploads/payments/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $filename = "payment_".$user_id."_".time().".".$ext;
        $target_file = $target_dir.$filename;
        
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
            $proof_path = $filename;
        } else {
            $error = "Failed to upload payment proof";
        }
    }
    
    if (empty($error)) {
        mysqli_query($conn, "INSERT INTO payments 
            (user_id, amount, payment_method, transaction_id, payment_proof) 
            VALUES ($user_id, $amount, '$method', '$transaction_id', '$proof_path')");
            
        mysqli_query($conn, "UPDATE users SET payment_status = 'pending' WHERE id = $user_id");
        $success = "Payment submitted for verification";
    }
}

// Handle KYC submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_kyc'])) {
    $doc_type = mysqli_real_escape_string($conn, $_POST['doc_type']);
    
    // Handle file uploads
    $front_image = '';
    $back_image = '';
    $selfie_image = '';
    
    $upload_dir = __DIR__."/uploads/kyc/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Process front image
    if (isset($_FILES['front_image']) && $_FILES['front_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['front_image']['name'], PATHINFO_EXTENSION);
        $filename = "kyc_front_".$user_id."_".time().".".$ext;
        if (move_uploaded_file($_FILES['front_image']['tmp_name'], $upload_dir.$filename)) {
            $front_image = $filename;
        }
    }
    
    // Process back image (if required)
    if (isset($_FILES['back_image']) && $_FILES['back_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['back_image']['name'], PATHINFO_EXTENSION);
        $filename = "kyc_back_".$user_id."_".time().".".$ext;
        if (move_uploaded_file($_FILES['back_image']['tmp_name'], $upload_dir.$filename)) {
            $back_image = $filename;
        }
    }
    
    // Process selfie
    if (isset($_FILES['selfie_image']) && $_FILES['selfie_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['selfie_image']['name'], PATHINFO_EXTENSION);
        $filename = "kyc_selfie_".$user_id."_".time().".".$ext;
        if (move_uploaded_file($_FILES['selfie_image']['tmp_name'], $upload_dir.$filename)) {
            $selfie_image = $filename;
        }
    }
    
    if (!empty($front_image)) {
        mysqli_query($conn, "INSERT INTO kyc_verifications 
            (user_id, document_type, front_image, back_image, selfie_image) 
            VALUES ($user_id, '$doc_type', '$front_image', '$back_image', '$selfie_image')");
            
        $success = "KYC documents submitted for verification";
    } else {
        $error = "Please upload at least the front of your document";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment & KYC Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Complete Your Registration</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Payment Section -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4>Step 1: Make Payment</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="submit_payment" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" name="amount" class="form-control" required step="0.01">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select" required>
                                    <option value="">Select method</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="crypto">Cryptocurrency</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Transaction ID</label>
                                <input type="text" name="transaction_id" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Proof (Screenshot/Receipt)</label>
                                <input type="file" name="payment_proof" class="form-control" required accept="image/*">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- KYC Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4>Step 2: KYC Verification</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="submit_kyc" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label">Document Type</label>
                                <select name="doc_type" class="form-select" required>
                                    <option value="">Select document</option>
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                    <option value="student_card">Student Card</option>
                                    <option value="driving_license">Driving License</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Front of Document</label>
                                <input type="file" name="front_image" class="form-control" required accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Back of Document (if applicable)</label>
                                <input type="file" name="back_image" class="form-control" accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Selfie with Document</label>
                                <input type="file" name="selfie_image" class="form-control" required accept="image/*">
                                <small class="text-muted">Hold your document next to your face</small>
                            </div>
                            
                            <button type="submit" class="btn btn-info text-white">Submit KYC</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
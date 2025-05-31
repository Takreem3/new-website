<?php
require 'database.php';
require 'includes/auth_check.php';

// Create uploads directory if missing
if (!file_exists('uploads/kyc')) {
    mkdir('uploads/kyc', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $document_type = $conn->real_escape_string($_POST['document_type']);
    $document_number = $conn->real_escape_string($_POST['document_number']);
    
    // File upload
    $file_name = basename($_FILES["document_file"]["name"]);
    $target_file = "uploads/kyc/" . uniqid() . "_" . $file_name;
    
    if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO kyc_verifications 
                              (user_id, document_type, document_number, file_path) 
                              VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $document_type, $document_number, $target_file);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "KYC submitted successfully!";
            header("Location: dashboard.php");
            exit;
        }
    }
    
    $_SESSION['error'] = "KYC submission failed";
    header("Location: kyc.php");
    exit;
}
?>

<!-- KYC Form -->
<div class="container">
    <h2>KYC Verification</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Document Type</label>
            <select name="document_type" class="form-control" required>
                <option value="passport">Passport</option>
                <option value="id_card">National ID Card</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Document Number</label>
            <input type="text" name="document_number" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Upload Document</label>
            <input type="file" name="document_file" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit KYC</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>

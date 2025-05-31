<?php
require 'database.php';
require 'includes/auth_check.php';

// Simple working KYC form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $doc_type = $conn->real_escape_string($_POST['doc_type']);
    $doc_number = $conn->real_escape_string($_POST['doc_number']);
    
    // File upload
    $file_name = basename($_FILES["document"]["name"]);
    $target_file = "uploads/" . uniqid() . "_" . $file_name;
    
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        $conn->query("INSERT INTO kyc_verifications 
                     (user_id, document_type, document_number, file_path) 
                     VALUES ($user_id, '$doc_type', '$doc_number', '$target_file')");
        echo "<script>alert('KYC Submitted!'); window.location='dashboard.php';</script>";
    }
}
?>

<?php require 'includes/header.php'; ?>

<div class="container">
    <h2>KYC Submission</h2>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label>Document Type:</label>
            <select name="doc_type" required>
                <option value="passport">Passport</option>
                <option value="id_card">ID Card</option>
            </select>
        </div>
        <div>
            <label>Document Number:</label>
            <input type="text" name="doc_number" required>
        </div>
        <div>
            <label>Upload Document:</label>
            <input type="file" name="document" required>
        </div>
        <button type="submit">Submit KYC</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
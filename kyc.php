<?php
require __DIR__ . '/../database.php';
require 'includes/auth_check.php'; // Your existing auth check

// Simple working KYC form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = __DIR__ . "/uploads/kyc/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);
    
    $target_file = $target_dir . basename($_FILES["document"]["name"]);
    
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO kyc_verifications 
                              (user_id, document_type, document_number, file_path) 
                              VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", 
            $_SESSION['user_id'],
            $_POST['doc_type'],
            $_POST['doc_number'],
            $target_file
        );
        $stmt->execute();
        echo "<script>alert('KYC Submitted!'); window.location='dashboard.php';</script>";
    }
}
?>

<!-- Simple HTML Form -->
<form method="post" enctype="multipart/form-data">
    <select name="doc_type" required>
        <option value="passport">Passport</option>
        <option value="id_card">ID Card</option>
    </select>
    <input type="text" name="doc_number" placeholder="Document Number" required>
    <input type="file" name="document" required>
    <button type="submit">Submit KYC</button>
</form>

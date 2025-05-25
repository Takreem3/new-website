<?php
include '../includes/config.php';
include '../includes/auth.php';
adminOnly();

$id = (int)$_GET['id'];
$kyc = $conn->query("SELECT * FROM kyc_verifications WHERE id=$id")->fetch_assoc();
include '../includes/header.php';
?>

<div class="container">
    <h3>KYC Documents</h3>
    <div class="row">
        <div class="col-md-4">
            <h5>Front Document</h5>
            <img src="../assets/kyc/<?= $kyc['front_image'] ?>" class="img-fluid">
        </div>
        <?php if ($kyc['back_image']): ?>
        <div class="col-md-4">
            <h5>Back Document</h5>
            <img src="../assets/kyc/<?= $kyc['back_image'] ?>" class="img-fluid">
        </div>
        <?php endif; ?>
        <div class="col-md-4">
            <h5>Selfie</h5>
            <img src="../assets/kyc/<?= $kyc['selfie_image'] ?>" class="img-fluid">
        </div>
    </div>
    <a href="kyc.php" class="btn btn-secondary mt-3">Back to List</a>
</div>

<?php include '../includes/footer.php'; ?>

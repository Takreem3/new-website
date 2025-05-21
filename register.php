<?php
include 'includes/config.php';
include 'includes/header.php';

// Initialize variables
$error = '';
$formData = [
    'username' => '',
    'email' => '',
    'phone' => '',
    'sponsor_identifier' => isset($_GET['ref']) ? $_GET['ref'] : ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $formData = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'sponsor_identifier' => trim($_POST['sponsor_identifier']),
        'position' => $_POST['position'] ?? null
    ];

    // Validate inputs
    if (empty($formData['username']) || empty($formData['email']) || empty($_POST['password'])) {
        $error = "All fields are required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if username/email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $formData['username'], $formData['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Username or email already exists";
        }
    }

    // Process sponsor
    $sponsor_id = null;
    if (empty($error) && !empty($formData['sponsor_identifier'])) {
        $sponsor_identifier = $conn->real_escape_string($formData['sponsor_identifier']);
        
        $sponsor = $conn->query("
            SELECT id FROM users 
            WHERE id = '$sponsor_identifier' OR username = '$sponsor_identifier'
            LIMIT 1
        ");

        if ($sponsor->num_rows > 0) {
            $sponsor_id = $sponsor->fetch_assoc()['id'];
        } else {
            $error = "Sponsor not found. Please verify their username/ID";
        }
    }

    // Complete registration
    if (empty($error)) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users 
            (username, password, email, phone, sponsor_id, position) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssis", 
            $formData['username'], 
            $password, 
            $formData['email'], 
            $formData['phone'], 
            $sponsor_id, 
            $formData['position']
        );

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Initialize user details
            $conn->query("INSERT INTO user_details (user_id) VALUES ($user_id)");
            
            // Process MLM commissions (if sponsored)
            if ($sponsor_id) {
                require_once 'includes/mlm_commission.php';
                processMLMCommission($conn, $user_id, $sponsor_id);
            }

            // Auto-login
            $_SESSION['user_id'] = $user_id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card registration-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Join Our Network</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" id="registrationForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username*</label>
                                    <input type="text" name="username" class="form-control" 
                                           value="<?= htmlspecialchars($formData['username']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email*</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($formData['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password*</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone*</label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($formData['phone']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sponsor (Username or ID)</label>
                            <input type="text" name="sponsor_identifier" class="form-control" 
                                   value="<?= htmlspecialchars($formData['sponsor_identifier']) ?>">
                            <small class="text-muted">Leave blank if you found us independently</small>
                        </div>

                        <?php if (!empty($formData['sponsor_identifier'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Position*</label>
                                <select name="position" class="form-control" required>
                                    <option value="">Select Position</option>
                                    <option value="left" <?= ($formData['position'] == 'left') ? 'selected' : '' ?>>Left</option>
                                    <option value="right" <?= ($formData['position'] == 'right') ? 'selected' : '' ?>>Right</option>
                                </select>
                                <small class="text-muted">Required when joining under a sponsor</small>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Register Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
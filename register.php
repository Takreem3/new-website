<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require __DIR__.'/includes/config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sponsor = $_POST['sponsor'] ?? '';

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Escape inputs
        $username = mysqli_real_escape_string($conn, $username);
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        $sponsor = mysqli_real_escape_string($conn, $sponsor);

        // Sponsor validation
        $sponsor_id = null;
        $sponsor_path = '';
        $sponsor_level = 0;
        
        if (!empty($sponsor)) {
            $sponsor_check = mysqli_query($conn, "SELECT id, tree_path, level FROM users WHERE username='$sponsor'");
            if (mysqli_num_rows($sponsor_check) == 0) {
                $error = "Sponsor username does not exist";
            } else {
                $sponsor_data = mysqli_fetch_assoc($sponsor_check);
                $sponsor_id = $sponsor_data['id'];
                $sponsor_path = $sponsor_data['tree_path'];
                $sponsor_level = $sponsor_data['level'];
            }
        }

        if (empty($error)) {
            // Create password hash
            $password_hash = md5($password);
            
            // Insert user
            $insert = mysqli_query($conn, "INSERT INTO users 
                (username, email, password, sponsor_id) 
                VALUES ('$username', '$email', '$password_hash', " . ($sponsor_id ?: 'NULL') . ")");
            
            if ($insert) {
                $new_user_id = mysqli_insert_id($conn);
                
                // Calculate level and tree path
                $level = $sponsor_id ? $sponsor_level + 1 : 0;
                $tree_path = $sponsor_id ? $sponsor_path . $new_user_id . '/' : '/' . $new_user_id . '/';
                
                // Update user with tree path and level
                mysqli_query($conn, "UPDATE users SET 
                    tree_path = '$tree_path',
                    level = $level 
                    WHERE id = $new_user_id");
                
                // Set success session and redirect
                $_SESSION['registration_success'] = true;
                header("Location: register_success.php");
                exit();
            } else {
                $error = "Registration error: ".mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 500px; 
            margin: 20px auto; 
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 24px;
        }
        .error { 
            color: #d32f2f; 
            padding: 12px; 
            background: #ffebee; 
            border: 1px solid #ffcdd2; 
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-group { 
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }
        button:hover {
            background: #0069d9;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .password-note {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <div class="password-note">Minimum 8 characters</div>
            </div>
            
            <div class="form-group">
                <label for="sponsor">Sponsor Username (optional):</label>
                <input type="text" id="sponsor" name="sponsor"
                    value="<?php echo isset($_POST['sponsor']) ? htmlspecialchars($_POST['sponsor']) : ''; ?>">
                <div class="password-note">Enter the username of who referred you</div>
            </div>
            
            <button type="submit">Create Account</button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </form>
    </div>
</body>
</html>
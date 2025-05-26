<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';
$user = null;

// Get current user data
try {
    $stmt = $pdo->prepare("SELECT username, email, phone, address FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("User not found");
    }
} catch (Exception $e) {
    $error_message = "Error loading user data: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update basic info
        if (!empty($username) && !empty($email)) {
            // Check if email already exists for another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                throw new Exception("Email already exists");
            }

            // Update user info
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$username, $email, $phone, $address, $user_id]);
            $success_message = "Profile updated successfully";
            
            // Update session data
            $_SESSION['username'] = $username;
            
            // Refresh user data
            $user['username'] = $username;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['address'] = $address;
        }

        // Handle password change if requested
        if (!empty($current_password) && !empty($new_password)) {
            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match");
            }

            if (strlen($new_password) < 6) {
                throw new Exception("New password must be at least 6 characters long");
            }

            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $current_user = $stmt->fetch();

            if (!password_verify($current_password, $current_user['password'])) {
                throw new Exception("Current password is incorrect");
            }

            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            $success_message = "Profile and password updated successfully";
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">My Profile</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($user): ?>
                            <form method="POST" action="profile.php">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Delivery Address</label>
                                    <textarea class="form-control" id="address" name="address" 
                                              rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>

                                <h3 class="card-title h5 mb-4 mt-5">Change Password</h3>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <small class="text-muted">Leave blank if you don't want to change password</small>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                           minlength="6">
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                           minlength="6">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .card {
        border: none;
        border-radius: 10px;
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .btn-primary {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .alert {
        border-radius: 8px;
        padding: 1rem 1.5rem;
    }
    </style>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
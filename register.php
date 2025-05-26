<?php
require_once 'config/database.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all required fields';
    } elseif (!preg_match('/^[a-zA-Z]+$/', $username)) {
        $error_message = 'Username can only contain letters (no numbers or special characters)';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
        $error_message = 'Invalid email id';
    } elseif (!empty($phone) && !preg_match('/^[0-9]+$/', $phone)) {
        $error_message = 'Phone number can only contain digits';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } else {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                throw new Exception('Username already exists');
            }

            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
            if ($stmt->execute([$username, $email, $hashed_password, $phone])) {
                $success_message = 'Registration successful! You can now login.';
                
                // Optional: Automatically log in the user after registration
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                
                // Redirect to login page after successful registration
                header('Location: login.php');
                exit;
            } else {
                throw new Exception('Registration failed. Please try again.');
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="register-container">
            <h2 class="text-center mb-4">Create an Account</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <div class="form-group">
                    <label for="username" class="required-field">Username</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           pattern="[a-zA-Z]+" required>
                    <div class="invalid-feedback">Username can only contain letters (no numbers or special characters).</div>
                </div>

                <div class="form-group">
                    <label for="email" class="required-field">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>"
                           pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$" required>
                    <div class="invalid-feedback">Invalid email id</div>
                </div>

                <div class="form-group">
                    <label for="password" class="required-field">Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           minlength="6" required>
                    <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="required-field">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" 
                           name="confirm_password" required>
                    <div class="invalid-feedback">Passwords must match.</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                           pattern="[0-9]+">
                    <div class="form-text">Optional: Enter your phone number for order updates (digits only).</div>
                    <div class="invalid-feedback">Phone number can only contain digits.</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </div>

                <div class="text-center mt-3">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Username validation - no numbers allowed
        document.getElementById('username').addEventListener('input', function() {
            if (!/^[a-zA-Z]+$/.test(this.value)) {
                this.setCustomValidity('Username can only contain letters');
            } else {
                this.setCustomValidity('');
            }
        });

        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            if (this.value !== document.getElementById('password').value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Phone number validation - digits only
        document.getElementById('phone').addEventListener('input', function() {
            if (this.value && !/^[0-9]+$/.test(this.value)) {
                this.setCustomValidity('Phone number can only contain digits');
            } else {
                this.setCustomValidity('');
            }
        });

        // Email validation - must contain @, . and end with .com
        document.getElementById('email').addEventListener('input', function() {
            var pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/;
            if (!pattern.test(this.value)) {
                this.setCustomValidity('Invalid email id');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 
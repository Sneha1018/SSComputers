<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$success_message = '';
$error_message = '';
$admin_email = 'aprillover709@gmail.com';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            // Insert into contact_messages table
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);

            // Prepare email content with proper headers
            $to = $admin_email;
            $email_subject = "Contact Form: $subject";
            
            // Create email body
            $email_body = "You have received a new contact form message.\n\n";
            $email_body .= "Name: $name\n";
            $email_body .= "Email: $email\n";
            $email_body .= "Subject: $subject\n\n";
            $email_body .= "Message:\n$message\n";

            // Additional headers
            $headers = array(
                'From: ' . $email,
                'Reply-To: ' . $email,
                'X-Mailer: PHP/' . phpversion(),
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8'
            );

            // Send email (this won't work on localhost)
            if (@mail($to, $email_subject, $email_body, implode("\r\n", $headers))) {
                $success_message = "Thank you for your message. We'll get back to you soon!";
                // Clear form data after successful submission
                $name = $email = $subject = $message = '';
            } else {
                // Message saved but email failed
                $success_message = "Thank you for your message. Your message has been saved and we will review it shortly.";
                // Log the email failure for admin
                error_log("Failed to send contact form email from $email");
            }
        } catch (PDOException $e) {
            $error_message = "Error saving message. Please try again later.";
            error_log("Contact form database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="text-center mb-5">Contact Us</h1>
        
        <div class="row">
            <!-- Contact Information -->
            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Get in Touch</h2>
                        
                        <div class="mb-4">
                            <h3 class="h6 mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>Address
                            </h3>
                            <p class="mb-0">
                                #20, 4th b cross, seethappa layout,<br>
                                chamundinagar, R. T. Nagar<br>
                                bangalore, Karnataka 560032<br>
                                India
                            </p>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 mb-3">
                                <i class="fas fa-phone text-primary me-2"></i>Phone
                            </h3>
                            <p class="mb-0">
                                <a href="tel:+917406274205" class="text-decoration-none">+91 7406274205</a>
                            </p>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 mb-3">
                                <i class="fas fa-envelope text-primary me-2"></i>Email
                            </h3>
                            <p class="mb-0">
                                <a href="mailto:aprillover709@gmail.com" class="text-decoration-none">aprillover709@gmail.com</a>
                            </p>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>Business Hours
                            </h3>
                            <p class="mb-0">
                                Monday - Saturday<br>
                                9:00 AM - 6:00 PM
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Send us a Message</h2>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="contact.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
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

    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .btn-primary {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .social-links a {
        transition: opacity 0.3s;
    }

    .social-links a:hover {
        opacity: 0.8;
    }
    </style>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
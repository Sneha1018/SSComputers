<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4">About Us</h1>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-3">Our Story</h2>
                        <p class="card-text">
                            Welcome to SSComputers, your premier destination for high-quality computer hardware 
                            and accessories. Established with a passion for technology and customer satisfaction, we've been 
                            serving tech enthusiasts, gamers, and professionals with top-tier computing solutions.
                        </p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-3">Our Mission</h2>
                        <p class="card-text">
                            Our mission is to provide customers with the best computing solutions at competitive prices. 
                            We believe in:
                        </p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i> Quality products from trusted brands</li>
                            <li><i class="fas fa-check text-success me-2"></i> Exceptional customer service</li>
                            <li><i class="fas fa-check text-success me-2"></i> Competitive pricing</li>
                            <li><i class="fas fa-check text-success me-2"></i> Expert technical support</li>
                            <li><i class="fas fa-check text-success me-2"></i> Fast and reliable shipping</li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-3">Why Choose Us?</h2>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-shield-alt text-primary fs-4 me-3 mt-1"></i>
                                    <div>
                                        <h5 class="h6">Secure Shopping</h5>
                                        <p class="small mb-0">Your security is our priority. We use industry-standard encryption for all transactions.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-truck text-primary fs-4 me-3 mt-1"></i>
                                    <div>
                                        <h5 class="h6">Fast Delivery</h5>
                                        <p class="small mb-0">Quick and reliable shipping across India with real-time tracking.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-headset text-primary fs-4 me-3 mt-1"></i>
                                    <div>
                                        <h5 class="h6">24/7 Support</h5>
                                        <p class="small mb-0">Our technical support team is always ready to help you.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-undo text-primary fs-4 me-3 mt-1"></i>
                                    <div>
                                        <h5 class="h6">Easy Returns</h5>
                                        <p class="small mb-0">Hassle-free return policy with full refund guarantee.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-3">Contact Information</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    Email: aprillover709@gmail.com
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    Phone: +91 7406274205
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    Hours: Mon-Sat, 9:00 AM - 6:00 PM
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    Location: Bangalore, India
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
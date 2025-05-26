<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_count = $stmt->fetchColumn();
    } catch(PDOException $e) {
        // Silently handle error
    }
}

// Get wishlist count
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $wishlist_count = $stmt->fetchColumn();
    } catch(PDOException $e) {
        // Silently handle error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSComputers - Your One-Stop Tech Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="header-top">
        <div class="container">
            <div class="contact-info">
                <a href="tel:+7406274205"><i class="fas fa-phone"></i> +7406274205</a>
                <a href="mailto:aprillover709@gmail.com"><i class="fas fa-envelope"></i> aprillover709@gmail.com</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-desktop"></i>
                    <span>SSComputers</span>
                </a>
            </div>

            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-info-circle"></i> About
                    </a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <i class="fas fa-th-list"></i> Categories <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="products.php?category=laptops"><i class="fas fa-laptop"></i> Laptops</a></li>
                            <li><a href="products.php?category=desktops"><i class="fas fa-desktop"></i> Desktops</a></li>
                            <li><a href="products.php?category=accessories"><i class="fas fa-keyboard"></i> Accessories</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.php" <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : ''; ?>>
                        <i class="fas fa-envelope"></i> Contact
                    </a></li>
                </ul>

                <div class="nav-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="wishlist.php" class="wishlist-link">
                            <i class="fas fa-heart"></i> Wishlist
                            <?php if ($wishlist_count > 0): ?>
                                <span class="badge"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="cart.php" class="cart-link">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if ($cart_count > 0): ?>
                                <span class="badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="orders.php" class="btn btn-primary"><i class="fas fa-box"></i> My Orders</a>
                        <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    <div class="main-content">
</body>
</html> 
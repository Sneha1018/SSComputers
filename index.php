<?php
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <div class="content" data-aos="fade-right">
            <h1>Welcome to SSComputers</h1>
            <p class="par">Your one-stop destination for all your computing needs. From high-performance laptops to custom-built desktops, we've got you covered.</p>
            <div class="cta-buttons">
                <a href="products.php" class="cta-button">Shop Now <i class="fas fa-arrow-right"></i></a>
                <a href="about.php" class="cta-button secondary">About Us <i class="fas fa-info-circle"></i></a>
            </div>
        </div>
        <div class="hero-image" data-aos="fade-left">
            <img src="./images/accessories.png" alt="Computers" class="store-image">
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <div class="feature-card" data-aos="fade-up">
            <i class="fas fa-truck"></i>
            <h3>Free Shipping</h3>
            <p>On orders over $500</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-headset"></i>
            <h3>24/7 Support</h3>
            <p>Expert assistance</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-shield-alt"></i>
            <h3>Secure Payment</h3>
            <p>100% secure checkout</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
            <i class="fas fa-undo"></i>
            <h3>Easy Returns</h3>
            <p>30-day return policy</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
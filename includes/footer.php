<?php
// Close the main-content div from header
?>
</div> <!-- End of main-content -->

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Newsletter</h3>
                <form class="newsletter-form" action="subscribe.php" method="POST">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> TechStore. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });

    // Mobile menu toggle
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.nav-content').classList.toggle('active');
    });
</script>
</body>
</html> 
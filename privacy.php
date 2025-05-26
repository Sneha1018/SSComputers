<?php
$pageTitle = "Privacy Policy";
require_once 'includes/header.php';
?>

<div class="container privacy-section">
    <h1>Privacy Policy</h1>
    
    <div class="privacy-content">
        <section>
            <h2>Information We Collect</h2>
            <p>We collect information that you provide directly to us, including:</p>
            <ul>
                <li>Name and contact information</li>
                <li>Account credentials</li>
                <li>Payment information</li>
                <li>Order history</li>
                <li>Communication preferences</li>
            </ul>
        </section>

        <section>
            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Process your orders and payments</li>
                <li>Communicate with you about your orders</li>
                <li>Send you marketing communications (with your consent)</li>
                <li>Improve our services</li>
                <li>Protect against fraud</li>
            </ul>
        </section>

        <section>
            <h2>Information Sharing</h2>
            <p>We do not sell your personal information. We may share your information with:</p>
            <ul>
                <li>Payment processors</li>
                <li>Shipping partners</li>
                <li>Service providers</li>
                <li>Law enforcement when required by law</li>
            </ul>
        </section>

        <section>
            <h2>Data Security</h2>
            <p>We implement appropriate security measures to protect your personal information, including:</p>
            <ul>
                <li>Encryption of sensitive data</li>
                <li>Secure server infrastructure</li>
                <li>Regular security audits</li>
                <li>Employee training on data protection</li>
            </ul>
        </section>

        <section>
            <h2>Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your information</li>
                <li>Opt-out of marketing communications</li>
                <li>Lodge a complaint with supervisory authorities</li>
            </ul>
        </section>

        <section>
            <h2>Contact Us</h2>
            <p>If you have any questions about our Privacy Policy, please contact us at:</p>
            <div class="contact-info">
                <p>Email: aprillover709@gmail.com</p>
                <p>Phone: + 7406274205</p>
                <p>Address: seethappa layout, Bangalore, Karnataka, India - 560032</p>
            </div>
        </section>
    </div>
</div>

<style>
.privacy-section {
    padding: 3rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

.privacy-content {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

section {
    margin-bottom: 2rem;
}

h2 {
    color: var(--secondary-color);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

p {
    color: var(--text-color);
    line-height: 1.6;
    margin-bottom: 1rem;
}

ul {
    list-style: none;
    padding-left: 1.5rem;
}

ul li {
    margin-bottom: 0.5rem;
    position: relative;
}

ul li:before {
    content: "•";
    color: black;
    position: absolute;
    left: -1.5rem;
}

.contact-info {
    background: light_blue;
    padding: 1.5rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.contact-info p {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .privacy-section {
        padding: 2rem 1rem;
    }
    
    .privacy-content {
        padding: 1.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 
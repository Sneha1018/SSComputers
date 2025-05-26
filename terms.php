<?php
$pageTitle = "Terms & Conditions";
require_once 'includes/header.php';
?>

<div class="container terms-section">
    <h1>Terms & Conditions</h1>
    
    <div class="terms-content">
        <section>
            <h2>Acceptance of Terms</h2>
            <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>
        </section>

        <section>
            <h2>Use License</h2>
            <ul>
                <li>Permission is granted to temporarily download one copy of the materials for personal, non-commercial transitory viewing only.</li>
                <li>This is the grant of a license, not a transfer of title.</li>
                <li>This license shall automatically terminate if you violate any of these restrictions.</li>
            </ul>
        </section>

        <section>
            <h2>Ordering & Payment</h2>
            <ul>
                <li>All orders are subject to product availability.</li>
                <li>Prices are subject to change without notice.</li>
                <li>Payment must be received prior to shipment.</li>
                <li>We accept major credit cards and online payment methods.</li>
            </ul>
        </section>

        <section>
            <h2>Shipping & Delivery</h2>
            <ul>
                <li>Orders are typically processed within 1-2 business days.</li>
                <li>Delivery times vary by location and shipping method.</li>
                <li>Shipping costs are calculated at checkout.</li>
                <li>Risk of loss transfers upon delivery to the carrier.</li>
            </ul>
        </section>

        <section>
            <h2>Returns & Refunds</h2>
            <ul>
                <li>Products may be returned within 30 days of delivery.</li>
                <li>Items must be unused and in original packaging.</li>
                <li>Refunds will be processed within 7-14 business days.</li>
                <li>Shipping costs for returns are the customer's responsibility.</li>
            </ul>
        </section>

        <section>
            <h2>Product Warranty</h2>
            <ul>
                <li>Products carry manufacturer's warranty only.</li>
                <li>Warranty claims must be processed through manufacturers.</li>
                <li>We assist with warranty claim processing.</li>
                <li>Extended warranty options available on select products.</li>
            </ul>
        </section>

        <section>
            <h2>Disclaimer</h2>
            <p>The materials on this website are provided on an 'as is' basis. SSComputers makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
        </section>

        <section>
            <h2>Limitations</h2>
            <p>In no event shall SSComputers or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on this website.</p>
        </section>

        <section>
            <h2>Contact Information</h2>
            <p>If you have any questions about these Terms & Conditions, please contact us:</p>
            <div class="contact-info">
                <p>Email: aprillover709@gmail.com</p>
                <p>Phone: +7406274205</p>
                <p>Address: Seethapa layout, Bangalore, Karnataka, India - 560032</p>
            </div>
        </section>
    </div>
</div>

<style>
.terms-section {
    padding: 3rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

.terms-content {
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
    color: var(--primary-color);
    position: absolute;
    left: -1.5rem;
}

.contact-info {
    background:light_blue;
    padding: 1.5rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.contact-info p {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .terms-section {
        padding: 2rem 1rem;
    }
    
    .terms-content {
        padding: 1.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 
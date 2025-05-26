<?php
$pageTitle = "Computer Monitors";
require_once 'includes/header.php';

// Get monitors from database
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = 'monitors'");
    $stmt->execute();
    $monitors = $stmt->fetchAll();
} catch(PDOException $e) {
    $monitors = [];
}
?>

<div class="container products-section">
    <h1>Computer Monitors</h1>
    
    <div class="filters">
        <select id="sortBy" class="filter-select">
            <option value="price-asc">Price: Low to High</option>
            <option value="price-desc">Price: High to Low</option>
            <option value="name-asc">Name: A to Z</option>
            <option value="name-desc">Name: Z to A</option>
        </select>
    </div>

    <div class="product-grid">
        <?php if (empty($monitors)): ?>
            <div class="no-products">
                <i class="fas fa-tv"></i>
                <p>No monitors available at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($monitors as $monitor): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($monitor['image_url']); ?>" alt="<?php echo htmlspecialchars($monitor['name']); ?>" class="product-image">
                    <h3 class="product-title"><?php echo htmlspecialchars($monitor['name']); ?></h3>
                    <p class="product-description"><?php echo htmlspecialchars($monitor['description']); ?></p>
                    <div class="product-price">₹<?php echo number_format($monitor['price'], 2); ?></div>
                    <div class="product-actions">
                        <button class="add-to-cart" data-product-id="<?php echo $monitor['id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="add-to-wishlist" data-product-id="<?php echo $monitor['id']; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.products-section {
    padding: 3rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

.filters {
    margin-bottom: 2rem;
    display: flex;
    justify-content: flex-end;
}

.filter-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.product-card {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.product-title {
    font-size: 1.25rem;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.product-description {
    color: var(--text-light);
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    font-size: 1.5rem;
    color: var(--primary-color);
    font-weight: bold;
    margin-bottom: 1rem;
}

.product-actions {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
}

.add-to-cart {
    background: var(--primary-color);
    color: #fff;
    border: none;
    padding: 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background-color 0.3s ease;
}

.add-to-cart:hover {
    background: var(--secondary-color);
}

.add-to-wishlist {
    background: #fff;
    color: var(--text-color);
    border: 1px solid #ddd;
    padding: 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-to-wishlist:hover {
    color: #dc3545;
    border-color: #dc3545;
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
}

.no-products i {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .products-section {
        padding: 2rem 1rem;
    }
    
    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Sorting functionality
document.getElementById('sortBy').addEventListener('change', function() {
    const products = Array.from(document.querySelectorAll('.product-card'));
    const container = document.querySelector('.product-grid');
    
    products.sort((a, b) => {
        const value = this.value;
        if (value === 'price-asc') {
            return getPriceFromElement(a) - getPriceFromElement(b);
        } else if (value === 'price-desc') {
            return getPriceFromElement(b) - getPriceFromElement(a);
        } else if (value === 'name-asc') {
            return getNameFromElement(a).localeCompare(getNameFromElement(b));
        } else if (value === 'name-desc') {
            return getNameFromElement(b).localeCompare(getNameFromElement(a));
        }
    });
    
    container.innerHTML = '';
    products.forEach(product => container.appendChild(product));
});

function getPriceFromElement(element) {
    return parseFloat(element.querySelector('.product-price').textContent.replace('₹', '').replace(',', ''));
}

function getNameFromElement(element) {
    return element.querySelector('.product-title').textContent;
}

// Add to cart functionality
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        // Add to cart logic here
        this.innerHTML = '<i class="fas fa-check"></i> Added';
        this.style.background = '#28a745';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            this.style.background = '';
        }, 2000);
    });
});

// Add to wishlist functionality
document.querySelectorAll('.add-to-wishlist').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        // Add to wishlist logic here
        this.style.color = '#dc3545';
        this.style.borderColor = '#dc3545';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 
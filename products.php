<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Function to format price in Indian Rupees
function formatIndianPrice($price) {
    return '₹' . number_format($price, 2, '.', ',');
}

// Build the base query for featured products
$featured_query = "SELECT * FROM products WHERE featured = 1";
$params = [];

// Add category filter if specified
if (!empty($category)) {
    $featured_query .= " AND category = ?";
    $params[] = $category;
}

// Add price range filter only if values are provided
if ($min_price !== null) {
    $featured_query .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== null) {
    $featured_query .= " AND price <= ?";
    $params[] = $max_price;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $featured_query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $featured_query .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $featured_query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $featured_query .= " ORDER BY name DESC";
        break;
    default:
        $featured_query .= " ORDER BY id DESC";
}

$featured_query .= " LIMIT 4";

// Execute featured products query
$stmt = $pdo->prepare($featured_query);
$stmt->execute($params);
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build the base query for all products
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

// Add category filter if specified
if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

// Add price range filter only if values are provided
if ($min_price !== null) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== null) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}

// Execute all products query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $categories = [];
}
?>

<!-- Hero Section -->
<div class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/products-banner.jpg') center/cover;">
    <div class="container">
        <h1>Our Products</h1>
        <p>Discover our wide range of computer products and accessories</p>
    </div>
</div>

<!-- Filter Section -->
<section class="filter-section py-2">
    <div class="container">
        <form action="products.php" method="GET" class="filter-form">
            <div class="filter-bar">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                                <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="sort" class="form-select">
                    <option value="">Latest</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                </select>

                <div class="price-inputs">
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="min_price" placeholder="Min" 
                               value="<?php echo $min_price; ?>" class="form-control">
                    </div>
                    <span class="separator">-</span>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="max_price" placeholder="Max" 
                               value="<?php echo $max_price; ?>" class="form-control">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-search"></i>
                    </button>
                    <a href="products.php" class="btn btn-light">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Featured Products Section -->
<?php if (!empty($featured_products)): ?>
<section class="featured-section py-4">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2>Featured Products</h2>
            <p>Check out our most popular items</p>
        </div>
        <div class="products-row">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card shadow-sm">
                    <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                        <?php else: ?>
                            <img src="./images/accessories1.png" alt="Product placeholder" class="img-fluid">
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price"><?php echo formatIndianPrice($product['price']); ?></p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- All Products Section -->
<section class="all-products-section py-4">
    <div class="container">
        <?php if (!empty($products)): ?>
            <div class="products-row">
                <?php foreach ($products as $product): ?>
                    <div class="product-card shadow-sm">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="img-fluid">
                            <?php else: ?>
                                <img src="./images/accessories1.png" 
                                     alt="Product placeholder"
                                     class="img-fluid">
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-price"><?php echo formatIndianPrice($product['price']); ?></p>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary btn-sm w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No products found matching your criteria.
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-section {
    padding: 40px 0;
    text-align: center;
    color: white;
    margin-bottom: 30px;
}

.hero-section h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.hero-section p {
    font-size: 1.1rem;
    margin-bottom: 0;
}

.featured-section {
    padding: 40px 0;
    background-color: #f8f9fa;
}

.section-header {
    margin-bottom: 40px;
}

.products-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.product-card {
    background: white;
    border-radius: 6px;
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0,0,0,0.1);
    width: 200px;  /* Fixed width */
    margin: 0;
    flex-shrink: 0;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.product-image {
    position: relative;
    padding-top: 100%;  /* Make it square */
    overflow: hidden;
    border-radius: 6px 6px 0 0;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}

.product-details {
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.product-title {
    font-size: 0.85rem;
    font-weight: 500;
    color: #333;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2rem;
}

.product-price {
    font-size: 0.9rem;
    color: #e44d26;
    font-weight: bold;
    margin: 0;
}

.product-actions {
    margin-top: auto;
}

.product-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.filters-sidebar {
    position: sticky;
    top: 20px;
}

.form-label {
    font-weight: 500;
    color: #333;
}

.alert {
    border-radius: 8px;
    padding: 1rem;
}

@media (max-width: 991px) {
    .filters-sidebar {
        margin-bottom: 30px;
        position: static;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 60px 0;
    }
    
    .hero-section h1 {
        font-size: 2.5rem;
    }
    
    .products-row {
        gap: 10px;
        justify-content: center;
    }
    
    .product-card {
        width: 160px;
    }
    
    .product-title {
        font-size: 0.85rem;
        height: 2rem;
    }
    
    .product-price {
        font-size: 0.95rem;
    }
    
    .product-details {
        padding: 0.5rem;
    }
}

.products-main-section {
    padding-top: 20px;
}

.row-cols-1 > * {
    padding: 8px;
}

.g-4 {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1rem;
}

.product-card-horizontal,
.product-image-horizontal,
.product-details-horizontal,
.product-title-horizontal,
.product-price-horizontal,
.product-actions-horizontal {
    display: none;
}

.products-container {
    margin-top: 2rem;
}

.products-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.product-card {
    background: white;
    border-radius: 6px;
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0,0,0,0.1);
    width: 220px;
    margin: 0;
    flex-shrink: 0;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.product-image {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
    border-radius: 6px 6px 0 0;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}

.product-details {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.product-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.4rem;
}

.product-price {
    font-size: 1rem;
    color: #e44d26;
    font-weight: bold;
    margin: 0;
}

.filters-form-section {
    background: #fff;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .products-row {
        gap: 10px;
        justify-content: center;
    }
    
    .product-card {
        width: 180px;
    }
}

.filter-section {
    background: white;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.filter-bar {
    display: flex;
    align-items: center;
    gap: 15px;
}

.form-select {
    min-width: 160px;
    max-width: 200px;
    height: 40px;
    padding: 0 12px;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background-color: white;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 280px;
}

.price-inputs .input-group {
    flex: 1;
}

.input-group-text {
    background: white;
    border: 1px solid #dee2e6;
    border-right: none;
    color: #666;
    padding: 0 10px;
    height: 40px;
}

.form-control {
    height: 40px;
    padding: 0 12px;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
    border-radius: 0 6px 6px 0;
}

.separator {
    color: #999;
    margin: 0 -4px;
}

.filter-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
}

.filter-actions .btn {
    width: 40px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.filter-actions .btn i {
    font-size: 16px;
}

.btn-light {
    background: white;
    border: 1px solid #dee2e6;
    color: #666;
}

.btn-light:hover {
    background: #f8f9fa;
    color: #333;
    border-color: #c1c9d0;
}

@media (max-width: 991px) {
    .filter-bar {
        flex-wrap: wrap;
        gap: 10px;
    }

    .form-select {
        min-width: calc(33.333% - 8px);
        max-width: none;
    }

    .price-inputs {
        min-width: calc(66.666% - 8px);
    }

    .filter-actions {
        min-width: calc(33.333% - 8px);
        margin-left: 0;
        justify-content: flex-end;
    }
}

@media (max-width: 768px) {
    .form-select {
        min-width: calc(50% - 5px);
    }

    .price-inputs {
        min-width: 100%;
    }

    .filter-actions {
        width: 100%;
        justify-content: stretch;
    }

    .filter-actions .btn {
        flex: 1;
    }
}

.featured-section {
    background-color: #fff;
    padding-top: 2rem;
}

.all-products-section {
    background-color: #f8f9fa;
    padding-top: 2rem;
}

/* Updated icon styles */
.input-group-text i {
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-actions .btn i {
    font-size: 1rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add to wishlist functionality
        const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
        wishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                        alert('Product added to wishlist!');
                    } else {
                        alert(data.message || 'Error adding to wishlist');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding to wishlist');
                });
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?> 
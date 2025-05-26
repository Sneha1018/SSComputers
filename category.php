<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Get category name from URL
$category = $_GET['name'] ?? '';

// Validate category
$valid_categories = ['Laptops', 'Accessories', 'Desktops'];
if (!in_array($category, $valid_categories)) {
    header('Location: index.php');
    exit;
}

// Get products for this category
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->execute([$category]);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error fetching products';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4"><?php echo htmlspecialchars($category); ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No products found in this category.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="img-fluid product-image">
                            <div class="p-3">
                                <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                </p>
                                <p class="product-price"><?php echo CURRENCY . number_format($product['price'], 2); ?></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$success_message = '';
$error_message = '';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];

try {
    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: index.php');
        exit;
    }

    // Check if product is in user's wishlist
    $in_wishlist = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $in_wishlist = $stmt->fetch() !== false;
    }

    // Handle add to cart
    if (isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $quantity = (int)$_POST['quantity'];
        
        if ($quantity <= 0 || $quantity > $product['stock']) {
            $error_message = "Invalid quantity";
        } else {
            // Check if product already in cart
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $cart_item = $stmt->fetch();

            if ($cart_item) {
                // Update quantity if already in cart
                $new_quantity = $cart_item['quantity'] + $quantity;
                if ($new_quantity <= $product['stock']) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                    $stmt->execute([$new_quantity, $cart_item['id']]);
                    $success_message = "Cart updated successfully!";
                } else {
                    $error_message = "Not enough stock available";
                }
            } else {
                // Add new item to cart
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
                $success_message = "Product added to cart successfully!";
            }
        }
    }

    // Handle wishlist actions
    if (isset($_POST['toggle_wishlist'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        if ($in_wishlist) {
            // Remove from wishlist
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $in_wishlist = false;
            $success_message = "Product removed from wishlist!";
        } else {
            // Add to wishlist
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $in_wishlist = true;
            $success_message = "Product added to wishlist!";
        }
    }

} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <?php if (strpos($success_message, 'cart') !== false): ?>
                    <div class="mt-2">
                        <a href="cart.php" class="btn btn-success btn-sm">View Cart</a>
                        <a href="products.php" class="btn btn-primary btn-sm">Continue Shopping</a>
                    </div>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="img-fluid rounded">
                <?php else: ?>
                    <img src="images/no-image.jpg" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="img-fluid rounded">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h1 class="mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="lead mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                
                <div class="mb-4">
                    <h2 class="h4">Price: ₹<?php echo number_format($product['price'], 2); ?></h2>
                    <p class="text-muted">Stock: <?php echo $product['stock']; ?> units available</p>
                </div>

                <div class="mb-4">
                    <form method="POST" class="d-inline-block">
                        <button type="submit" name="toggle_wishlist" class="btn btn-outline-danger">
                            <i class="fas <?php echo $in_wishlist ? 'fa-heart' : 'fa-heart-o'; ?>"></i>
                            <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                        </button>
                    </form>
                </div>

                <form method="POST" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>

                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title h5">Product Details</h3>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        <p class="mb-0">
                            <strong>Category:</strong> 
                            <a href="products.php?category=<?php echo urlencode($product['category']); ?>">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$success_message = '';
$error_message = '';
$wishlist_items = []; // Initialize wishlist_items array

// Handle remove from wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $wishlist_id = (int)$_POST['wishlist_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
        $stmt->execute([$wishlist_id, $_SESSION['user_id']]);
        $success_message = "Item removed from wishlist!";
    } catch (PDOException $e) {
        $error_message = "Error removing item from wishlist";
        error_log($e->getMessage());
    }
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND stock > 0");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $cart_item = $stmt->fetch();

            if ($cart_item) {
                // Update quantity if already in cart
                $new_quantity = $cart_item['quantity'] + 1;
                if ($new_quantity <= $product['stock']) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                    $stmt->execute([$new_quantity, $cart_item['id']]);
                    $success_message = "Cart updated successfully!";
                } else {
                    $error_message = "Not enough stock available";
                }
            } else {
                // Add new item to cart
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                $success_message = "Product added to cart successfully!";
            }
        } else {
            $error_message = "Product not available";
        }
    } catch (PDOException $e) {
        $error_message = "Error adding to cart";
        error_log($e->getMessage());
    }
}

// Get wishlist items
try {
    $stmt = $pdo->prepare("
        SELECT w.id as wishlist_id, p.* 
        FROM wishlist w 
        INNER JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error loading wishlist";
    error_log($e->getMessage());
}

// Include header before any HTML output
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-4">My Wishlist</h1>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <?php if (strpos($success_message, 'cart') !== false): ?>
                <div class="mt-2">
                    <a href="cart.php" class="btn btn-success btn-sm">View Cart</a>
                    <a href="products.php" class="btn btn-primary btn-sm">Continue Shopping</a>
                </div>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($wishlist_items)): ?>
        <div class="alert alert-info">
            Your wishlist is empty. <a href="products.php">Browse products</a> to add items to your wishlist.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?>
                            </p>
                            <p class="card-text">
                                <strong>Price:</strong> ₹<?php echo number_format($item['price'], 2); ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?php echo $item['stock'] > 0 ? $item['stock'] . ' in stock' : 'Out of stock'; ?>
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-heart-broken"></i> Remove
                                    </button>
                                </form>
                                <div>
                                    <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                    <?php if ($item['stock'] > 0): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
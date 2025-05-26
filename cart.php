<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT c.id as cart_id, c.quantity, p.* 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();

    $total = 0;
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Error loading cart items";
}
?>

<div class="container mt-5">
    <h2>Shopping Cart</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">Your cart is empty. <a href="index.php">Continue shopping</a></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <?php else: ?>
                                        <img src="images/no-image.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </div>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2, '.', ','); ?></td>
                            <td>
                                <input type="number" class="form-control quantity-input" style="width: 80px" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['stock']; ?>"
                                       data-cart-id="<?php echo $item['cart_id']; ?>">
                            </td>
                            <td>₹<?php echo number_format($subtotal, 2, '.', ','); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>₹<?php echo number_format($total, 2, '.', ','); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<!-- Add jQuery before Bootstrap and our custom script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Handle quantity updates
    $('.quantity-input').change(function() {
        const cartId = $(this).data('cart-id');
        const quantity = $(this).val();
        
        $.post('update_cart.php', {
            cart_id: cartId,
            quantity: quantity
        }).done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }).fail(function() {
            alert('Error updating cart');
        });
    });

    // Handle item removal
    $('.remove-item').click(function() {
        if (confirm('Are you sure you want to remove this item?')) {
            const cartId = $(this).data('cart-id');
            
            $.post('remove_from_cart.php', {
                cart_id: cartId
            }).done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            }).fail(function() {
                alert('Error removing item');
            });
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 
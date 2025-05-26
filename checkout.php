<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if cart is empty
$stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_count = $stmt->fetchColumn();

if ($cart_count == 0) {
    header('Location: cart.php');
    exit();
}

// Get cart items for display
$stmt = $conn->prepare("
    SELECT p.name, p.price, c.quantity, (p.price * c.quantity) as total
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate grand total
$grand_total = 0;
foreach ($cart_items as $item) {
    $grand_total += $item['total'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    
    if (empty($shipping_address)) {
        $error_message = "Please enter a shipping address";
    } else {
        // Store shipping address in session
        $_SESSION['shipping_address'] = $shipping_address;
        
        // Redirect to payment page
        header('Location: payment.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Checkout</h2>
                        
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="checkout.php">
                            <div class="mb-3">
                                <h4>Shipping Address</h4>
                                <label for="shipping_address" class="form-label">Delivery Address</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                        rows="3" required><?php echo htmlspecialchars($_POST['shipping_address'] ?? ''); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="cart.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Cart
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Continue to Payment <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Order Summary</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="text-end">₹<?php echo number_format($item['total'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Grand Total:</strong></td>
                                        <td class="text-end"><strong>₹<?php echo number_format($grand_total, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
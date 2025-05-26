<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = (int)$_GET['order_id'];

try {
    // Get order details
    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: index.php');
        exit();
    }

    // Get order items
    $stmt = $conn->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    header('Location: index.php');
    exit();
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="h3 mb-3">Order Placed Successfully!</h1>
                    <p class="text-muted mb-4">Thank you for shopping with us. Your order has been received and is being processed.</p>
                    
                    <div class="order-details text-start mb-4">
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <h5 class="h6 mb-2">Order Number</h5>
                                <p class="mb-0">#<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="h6 mb-2">Date</h5>
                                <p class="mb-0"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <h5 class="h6 mb-2">Total Amount</h5>
                                <p class="mb-0">₹<?php echo number_format($order['total_amount'], 2); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="h6 mb-2">Status</h5>
                                <p class="mb-0">
                                    <span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="h6 mb-2">Shipping Address</h5>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>

                        <div class="order-items">
                            <h5 class="h6 mb-3">Order Items</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                                                <td class="text-end">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                            <td class="text-end"><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                        <a href="profile.php" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.order-details {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin: 2rem 0;
}

.table th {
    font-weight: 500;
    color: #666;
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.text-success {
    color: #28a745 !important;
}
</style>

<?php require_once 'includes/footer.php'; ?> 
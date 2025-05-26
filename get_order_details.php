<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    http_response_code(400);
    exit('Order ID is required');
}

// Get order details
try {
    $stmt = $conn->prepare("
        SELECT 
            o.*,
            GROUP_CONCAT(
                CONCAT(p.name, ' (', oi.quantity, ' x ₹', oi.price, ')')
                SEPARATOR '\n'
            ) as order_items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = ? AND o.user_id = ?
        GROUP BY o.id
    ");
    $stmt->execute([$_GET['order_id'], $_SESSION['user_id']]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order_details) {
        http_response_code(404);
        exit('Order not found');
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('Error fetching order details');
}
?>

<div class="order-details">
    <div class="mb-3">
        <h6>Order #<?php echo $order_details['id']; ?></h6>
        <div class="timeline">
            <div class="timeline-item">
                <i class="fas fa-circle text-success"></i>
                <span>Order Placed</span>
                <small class="text-muted d-block">
                    <?php echo date('d M Y H:i', strtotime($order_details['created_at'])); ?>
                </small>
            </div>
            <?php if ($order_details['status'] != 'pending'): ?>
                <div class="timeline-item">
                    <i class="fas fa-circle <?php echo $order_details['status'] == 'processing' ? 'text-info' : 'text-muted'; ?>"></i>
                    <span>Processing</span>
                </div>
            <?php endif; ?>
            <?php if (in_array($order_details['status'], ['shipped', 'delivered'])): ?>
                <div class="timeline-item">
                    <i class="fas fa-circle <?php echo $order_details['status'] == 'shipped' ? 'text-primary' : 'text-muted'; ?>"></i>
                    <span>Shipped</span>
                </div>
            <?php endif; ?>
            <?php if ($order_details['status'] == 'delivered'): ?>
                <div class="timeline-item">
                    <i class="fas fa-circle text-success"></i>
                    <span>Delivered</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-3">
        <h6>Shipping Address</h6>
        <p class="mb-0"><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
    </div>

    <div class="mb-3">
        <h6>Order Items</h6>
        <ul class="list-unstyled">
            <?php foreach (explode("\n", $order_details['order_items']) as $item): ?>
                <li><?php echo htmlspecialchars($item); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="mb-0">
        <h6>Total Amount</h6>
        <p class="mb-0 fs-5">₹<?php echo number_format($order_details['total_amount'], 2); ?></p>
    </div>
</div> 
<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: order_history.php');
    exit();
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
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order_details) {
        header('Location: order_history.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching order details: " . $e->getMessage();
}
?>

<div class="container py-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Order #<?php echo $order_details['id']; ?></h1>
                <a href="order_history.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php else: ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
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
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <h6>Order Items</h6>
                                <ul class="list-unstyled">
                                    <?php foreach (explode("\n", $order_details['order_items']) as $item): ?>
                                        <li class="mb-2"><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Total Amount:</h6>
                                    <span class="fs-5 fw-bold">₹<?php echo number_format($order_details['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
    margin: 1rem 0;
}

.timeline-item {
    position: relative;
    padding-bottom: 15px;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: 4px;
    top: 20px;
    height: 100%;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item i {
    position: absolute;
    left: -30px;
    top: 4px;
    font-size: 10px;
}

/* Fix scrolling issues */
body {
    min-height: 100vh;
    position: relative;
    margin: 0;
    padding-bottom: 60px;
    overflow-y: auto;
}

.container {
    margin-bottom: 2rem;
}

.main-content {
    min-height: calc(100vh - 60px);
    position: relative;
    overflow-y: auto;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem;
}

.card-body {
    padding: 1.5rem;
}
</style>

<?php require_once 'includes/footer.php'; ?> 
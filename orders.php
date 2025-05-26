<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's orders with order items
try {
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .order-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            text-transform: capitalize;
        }
        .items-list {
            max-height: 100px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>My Orders</h2>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-cart"></i> Continue Shopping
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
                        <a href="index.php" class="alert-link">Start shopping</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="card order-card">
                            <div class="card-header bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>Order #<?php echo $order['id']; ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge bg-<?php 
                                            echo match($order['status']) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?> status-badge">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Items:</h6>
                                        <div class="items-list">
                                            <?php echo htmlspecialchars($order['items']); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Payment Details:</h6>
                                        <p class="mb-1">Method: <?php echo ucfirst($order['payment_method']); ?></p>
                                        <p class="mb-0">Status: 
                                            <span class="badge bg-<?php 
                                                echo match($order['payment_status']) {
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <small class="text-muted">
                                            Shipping Address: <?php echo htmlspecialchars($order['shipping_address'] ?? 'Not provided'); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="order_confirmation.php?order_id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
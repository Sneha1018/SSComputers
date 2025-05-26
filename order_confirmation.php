<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
try {
    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
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
    $_SESSION['error'] = "Error retrieving order details: " . $e->getMessage();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .product-image {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
        }
        .payment-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="bi bi-check-circle"></i> Order Confirmed!</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <h4>Thank you for your order!</h4>
                            <p class="text-muted">Order ID: #<?php echo $order_id; ?></p>
                            <p>We'll send you an email confirmation shortly.</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span></p>
                                <p><strong>Payment Method:</strong> <?php echo $order['payment_method'] == 'cod' ? 'Cash on Delivery' : 'UPI Payment'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Details</h5>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            </div>
                        </div>

                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image me-2">
                                                    <?php endif; ?>
                                                    <div><?php echo htmlspecialchars($item['name']); ?></div>
                                                </div>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if ($order['payment_method'] == 'upi'): ?>
                            <div class="payment-info text-center">
                                <h5>UPI Payment Instructions</h5>
                                <p>Please complete your payment by scanning the QR code below:</p>
                                <img src="./images/QR.jpg" alt="UPI QR Code" class="upi-qr" style="max-width: 200px;">
                                <p class="text-muted">Scan this QR code using any UPI app to complete your payment.</p>
                                <p><strong>Order ID: #<?php echo $order_id; ?></strong></p>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary">
                                <i class="bi bi-house"></i> Continue Shopping
                            </a>
                            <a href="orders.php" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-box"></i> View My Orders
                            </a>
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
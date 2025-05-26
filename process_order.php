<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if payment method is selected
if (!isset($_POST['payment_method'])) {
    header('Location: payment.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$shipping_address = $_SESSION['shipping_address'] ?? '';

// Validate payment method
if (!in_array($payment_method, ['cod', 'upi'])) {
    header('Location: payment.php');
    exit();
}

try {
    // Start transaction
    $conn->beginTransaction();
    
    // Get cart items
    $stmt = $conn->prepare("
        SELECT c.*, p.name, p.price, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cart_items)) {
        throw new Exception("Your cart is empty.");
    }
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['quantity'] * $item['price'];
        
        // Check stock
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Insufficient stock for {$item['name']}. Available: {$item['stock']}");
        }
    }
    
    // Create order
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, shipping_address, total_amount, payment_method, payment_status, status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $payment_status = $payment_method == 'cod' ? 'pending' : 'awaiting_payment';
    $stmt->execute([$user_id, $shipping_address, $total_amount, $payment_method, $payment_status]);
    $order_id = $conn->lastInsertId();
    
    // Add order items and update stock
    foreach ($cart_items as $item) {
        // Add order item
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        
        // Update stock
        $stmt = $conn->prepare("
            UPDATE products 
            SET stock = stock - ? 
            WHERE id = ?
        ");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    // Create payment record
    $stmt = $conn->prepare("
        INSERT INTO payments (order_id, amount, payment_method, status, transaction_id, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $transaction_id = $payment_method == 'cod' ? 'COD-' . $order_id : 'UPI-' . $order_id . '-' . time();
    $stmt->execute([$order_id, $total_amount, $payment_method, $payment_status, $transaction_id]);
    
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Commit transaction
    $conn->commit();
    
    // Set success message
    $_SESSION['success'] = "Order placed successfully! Order ID: #" . $order_id;
    
    // Redirect to order confirmation page
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    // Set error message
    $_SESSION['error'] = "Error placing order: " . $e->getMessage();
    
    // Redirect back to payment page
    header('Location: payment.php');
    exit();
}
?> 
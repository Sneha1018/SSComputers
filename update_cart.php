<?php
session_start();
require_once 'includes/config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to update your cart']);
    exit;
}

// Validate input
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$cartId = intval($_POST['cart_id']);
$quantity = intval($_POST['quantity']);

// Validate quantity
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get cart item and check ownership
    $stmt = $pdo->prepare("
        SELECT c.*, p.stock, p.name 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$cartId, $_SESSION['user_id']]);
    $cartItem = $stmt->fetch();

    if (!$cartItem) {
        throw new Exception('Cart item not found');
    }

    // Check stock availability
    if ($quantity > $cartItem['stock']) {
        throw new Exception("Sorry, only {$cartItem['stock']} units of {$cartItem['name']} are available");
    }

    // Update quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$quantity, $cartId, $_SESSION['user_id']]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
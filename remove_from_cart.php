<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Set the content type to JSON since we'll be returning JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage your cart']);
    exit;
}

// Check if cart_id is provided
if (!isset($_POST['cart_id']) || !is_numeric($_POST['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

$cart_id = (int)$_POST['cart_id'];
$user_id = $_SESSION['user_id'];

try {
    // Start transaction
    $pdo->beginTransaction();

    // First check if the cart item exists and belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    $cart_item = $stmt->fetch();

    if (!$cart_item) {
        throw new Exception('Cart item not found');
    }

    // Delete the cart item
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);

    // Commit transaction
    $pdo->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => 'Error removing item from cart: ' . $e->getMessage()
    ]);
}
?> 
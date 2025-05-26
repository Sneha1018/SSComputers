<?php
session_start();
require_once 'includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?? 1;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

try {
    // First check if the product exists and is in stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }

    // Check if item already exists in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cartItem = $stmt->fetch();

    if ($cartItem) {
        // Update quantity if item exists
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $cartItem['id']]);
    } else {
        // Insert new item if it doesn't exist
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cartCount = $stmt->fetch()['count'];

    echo json_encode([
        'success' => true, 
        'message' => 'Item added to cart successfully',
        'cartCount' => $cartCount
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding item to cart']);
}
?> 
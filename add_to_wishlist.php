<?php
require_once 'includes/config.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to your wishlist'
    ]);
    exit;
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit;
    }

    // Check if product is already in wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Product is already in your wishlist'
        ]);
        exit;
    }

    // Add to wishlist
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Product added to wishlist successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding product to wishlist'
    ]);
    error_log($e->getMessage());
}
?> 
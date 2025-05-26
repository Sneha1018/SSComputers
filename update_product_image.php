<?php
require_once 'includes/config.php';

try {
    // Update the image URL for the gaming keyboard product
    $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE category = 'Accessories' AND name LIKE '%Gaming%'");
    $stmt->execute(['images/gaming-keyboard.jpg']);
    
    echo "Product image URL updated successfully!";
} catch (PDOException $e) {
    echo "Error updating product image: " . $e->getMessage();
}
?> 
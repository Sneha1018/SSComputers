<?php
require_once 'config.php';

try {
    // Update existing product images
    $updates = [
        'Premium Gaming Laptop' => 'https://via.placeholder.com/400x300?text=Gaming+Laptop',
        'Professional Desktop PC' => 'https://via.placeholder.com/400x300?text=Desktop+PC',
        'Wireless Gaming Mouse' => 'https://via.placeholder.com/400x300?text=Gaming+Mouse',
        '4K Gaming Monitor' => 'https://via.placeholder.com/400x300?text=4K+Monitor'
    ];

    foreach ($updates as $name => $image_url) {
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE name = ?");
        $stmt->execute([$image_url, $name]);
    }

    echo "Product images updated successfully!";
    echo "<br><a href='index.php'>Return to homepage</a>";

} catch(PDOException $e) {
    die("Error updating images: " . $e->getMessage());
}
?> 
<?php
require_once 'includes/config.php';

try {
    // Create wishlist table
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_wishlist (user_id, product_id)
    )");

    echo "Wishlist table created successfully.<br>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    die("Error creating wishlist table: " . $e->getMessage());
}
?> 
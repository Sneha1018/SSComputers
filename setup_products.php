<?php
// Include database configuration
require_once 'config/database.php';

try {
    // Drop dependent tables first
    $conn->exec("DROP TABLE IF EXISTS cart_items");
    $conn->exec("DROP TABLE IF EXISTS wishlist_items");
    $conn->exec("DROP TABLE IF EXISTS order_items");
    $conn->exec("DROP TABLE IF EXISTS products");
    echo "Dropped existing tables.<br>";

    // Create products table
    $conn->exec("CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        category VARCHAR(50),
        featured BOOLEAN DEFAULT FALSE,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (id)
    ) ENGINE=InnoDB");

    // Insert sample products
    $sample_products = [
        [
            'name' => 'Gaming Laptop',
            'description' => 'High-performance gaming laptop with RTX 3080',
            'price' => 129999.99,
            'stock' => 10,
            'category' => 'Laptops',
            'featured' => 1,
            'image' => 'images/products/gaming-laptop.jpg'
        ],
        [
            'name' => 'Gaming Mouse',
            'description' => 'RGB gaming mouse with 8 programmable buttons',
            'price' => 4999.99,
            'stock' => 20,
            'category' => 'Accessories',
            'featured' => 1,
            'image' => 'images/products/gaming-mouse.jpg'
        ],
        [
            'name' => 'Mechanical Keyboard',
            'description' => 'RGB mechanical keyboard with Cherry MX switches',
            'price' => 7999.99,
            'stock' => 15,
            'category' => 'Accessories',
            'featured' => 1,
            'image' => 'images/products/mechanical-keyboard.jpg'
        ]
    ];
    
    // Create products directory if it doesn't exist
    if (!file_exists('images/products')) {
        mkdir('images/products', 0777, true);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, featured, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_products as $product) {
        $stmt->execute([
            $product['name'],
            $product['description'],
            $product['price'],
            $product['stock'],
            $product['category'],
            $product['featured'],
            $product['image']
        ]);
    }
    
    echo "Sample products added successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
<?php
// Include database configuration
require_once 'config.php';

try {
    // Create products table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(255),
        category VARCHAR(50),
        featured BOOLEAN DEFAULT 0,
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Products table created successfully\n";

    // Check if products table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert sample products
        $sample_products = [
            [
                'name' => 'Premium Gaming Laptop',
                'description' => 'High-performance gaming laptop with RTX 4080, 32GB RAM, and 1TB SSD',
                'price' => 1999.99,
                'image_url' => 'images/products/laptop1.jpg',
                'category' => 'laptops',
                'featured' => 1,
                'stock' => 10
            ],
            [
                'name' => 'Professional Desktop PC',
                'description' => 'Powerful workstation with Intel i9, 64GB RAM, and 2TB SSD',
                'price' => 2499.99,
                'image_url' => 'images/products/desktop1.jpg',
                'category' => 'desktops',
                'featured' => 1,
                'stock' => 5
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'description' => 'Ergonomic wireless gaming mouse with RGB lighting and programmable buttons',
                'price' => 79.99,
                'image_url' => 'images/products/mouse1.jpg',
                'category' => 'accessories',
                'featured' => 1,
                'stock' => 50
            ],
            [
                'name' => '4K Gaming Monitor',
                'description' => '27-inch 4K monitor with 144Hz refresh rate and HDR support',
                'price' => 599.99,
                'image_url' => 'images/products/monitor1.jpg',
                'category' => 'monitors',
                'featured' => 0,
                'stock' => 15
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, featured, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");

        foreach ($sample_products as $product) {
            $stmt->execute([
                $product['name'],
                $product['description'],
                $product['price'],
                $product['image_url'],
                $product['category'],
                $product['featured'],
                $product['stock']
            ]);
        }
        echo "Sample products added successfully\n";
    } else {
        echo "Products table already contains data\n";
    }

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 
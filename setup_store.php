<?php
require_once 'includes/config.php';

try {
    // Drop existing cart table if exists (to recreate with correct structure)
    $pdo->exec("DROP TABLE IF EXISTS cart");
    
    // Drop existing wishlist table if exists (to recreate with correct structure)
    $pdo->exec("DROP TABLE IF EXISTS wishlist");
    
    // Create products table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(255),
        category VARCHAR(50) NOT NULL,
        featured BOOLEAN DEFAULT false,
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Products table created successfully!<br>";

    // Create cart table with correct structure
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    echo "Cart table created successfully!<br>";

    // Create wishlist table with correct structure
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_product (user_id, product_id)
    )");
    echo "Wishlist table created successfully!<br>";

    // Check if products table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        // Insert sample products
        $products = [
            [
                'name' => 'ASUS ROG Strix G17 Gaming Laptop',
                'description' => 'AMD Ryzen 9 7945HX, 16GB DDR5, 1TB SSD, RTX 4060 8GB Graphics, 17.3" FHD 165Hz, Windows 11',
                'price' => 149990.00,
                'image_url' => 'https://via.placeholder.com/400x300?text=Gaming+Laptop',
                'category' => 'Laptops',
                'featured' => true,
                'stock' => 10
            ],
            [
                'name' => 'HP Victus Gaming Desktop',
                'description' => 'Intel Core i7-13700F, 32GB DDR5, 1TB SSD, RTX 4070 12GB Graphics, Windows 11',
                'price' => 159990.00,
                'image_url' => 'https://via.placeholder.com/400x300?text=Gaming+Desktop',
                'category' => 'Desktops',
                'featured' => true,
                'stock' => 5
            ],
            [
                'name' => 'Logitech G502 X PLUS',
                'description' => 'Wireless Gaming Mouse with LIGHTFORCE Hybrid Switches and LIGHTSYNC RGB',
                'price' => 12495.00,
                'image_url' => 'https://via.placeholder.com/400x300?text=Gaming+Mouse',
                'category' => 'Accessories',
                'featured' => true,
                'stock' => 20
            ],
            [
                'name' => 'Samsung Odyssey G7',
                'description' => '32-inch QHD 240Hz Gaming Monitor with 1000R Curved Screen',
                'price' => 54999.00,
                'image_url' => 'https://via.placeholder.com/400x300?text=Gaming+Monitor',
                'category' => 'Monitors',
                'featured' => true,
                'stock' => 8
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, featured, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($products as $product) {
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
        echo "Sample products added successfully!<br>";
    }

    echo "<br>Setup completed successfully!<br>";
    echo "<a href='index.php' class='btn btn-primary'>Go to Homepage</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?> 
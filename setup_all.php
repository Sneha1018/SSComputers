<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create connection without database
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<h2>Database Setup Progress</h2>";

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS onlinecomputerstore_db");
    echo "<p style='color:green'>✓ Database 'onlinecomputerstore_db' created successfully</p>";

    // Select the database
    $pdo->exec("USE onlinecomputerstore_db");
    echo "<p style='color:green'>✓ Database selected</p>";

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p style='color:green'>✓ Users table created successfully</p>";

    // Create products table
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
    echo "<p style='color:green'>✓ Products table created successfully</p>";

    // Create cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "<p style='color:green'>✓ Cart table created successfully</p>";

    // Check if admin user exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $adminCount = $stmt->fetchColumn();

    if ($adminCount == 0) {
        // Create default admin user
        $adminUsername = 'admin';
        $adminEmail = 'admin@example.com';
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$adminUsername, $adminEmail, $adminPassword]);
        
        echo "<p style='color:green'>✓ Default admin user created</p>";
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<p><strong>Admin Login Credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Username: admin</li>";
        echo "<li>Email: admin@example.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p style='color:blue'>ℹ Admin user already exists</p>";
    }

    // Check if products table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    // Define sample products
    $sample_products = [
        [
            'name' => 'ASUS ROG Strix G17 Gaming Laptop',
            'description' => 'AMD Ryzen 9 7945HX, 16GB DDR5, 1TB SSD, RTX 4060 8GB Graphics, 17.3" FHD 165Hz, Windows 11',
            'price' => 149990.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=ASUS+ROG+Strix+G17',
            'category' => 'laptops',
            'featured' => 1,
            'stock' => 10
        ],
        [
            'name' => 'HP Victus Gaming Desktop',
            'description' => 'Intel Core i7-13700F, 16GB DDR5, 1TB SSD, NVIDIA RTX 4070 8GB, Windows 11 Pro',
            'price' => 159990.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=HP+Victus+Desktop',
            'category' => 'desktops',
            'featured' => 1,
            'stock' => 5
        ],
        [
            'name' => 'Logitech G502 X PLUS',
            'description' => 'Wireless Gaming Mouse with LIGHTFORCE Hybrid Switches, LIGHTSYNC RGB, HERO 25K Sensor',
            'price' => 12495.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=Logitech+G502',
            'category' => 'accessories',
            'featured' => 1,
            'stock' => 50
        ],
        [
            'name' => 'Samsung Odyssey G7',
            'description' => '32-inch QHD (2560x1440) 240Hz, 1ms, Curved Gaming Monitor, HDR600, G-Sync Compatible',
            'price' => 54999.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=Samsung+Odyssey+G7',
            'category' => 'monitors',
            'featured' => 1,
            'stock' => 15
        ],
        [
            'name' => 'MSI Katana 15',
            'description' => 'Intel Core i7-13620H, 16GB DDR5, 1TB NVMe SSD, RTX 4060 8GB, 15.6" FHD 144Hz',
            'price' => 124990.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=MSI+Katana+15',
            'category' => 'laptops',
            'featured' => 0,
            'stock' => 8
        ],
        [
            'name' => 'Corsair K100 RGB',
            'description' => 'Mechanical Gaming Keyboard with OPX Switches, PBT Double-Shot Keycaps, iCUE Control Wheel',
            'price' => 23999.00,
            'image_url' => 'https://placehold.co/400x300/333/white?text=Corsair+K100',
            'category' => 'accessories',
            'featured' => 0,
            'stock' => 25
        ]
    ];

    if ($count == 0) {
        // Insert sample products
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
        echo "<p style='color:green'>✓ Sample products added successfully</p>";
    } else {
        // Update existing products with new data
        $pdo->exec("TRUNCATE TABLE products"); // Clear existing products
        
        // Reinsert the new products
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
        echo "<p style='color:blue'>ℹ Products updated successfully</p>";
    }

    echo "<div style='margin-top: 20px; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
    echo "<p style='color: #2e7d32'><strong>✓ Setup completed successfully!</strong></p>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Visit the homepage</a></li>";
    echo "<li><a href='login.html'>Login to your account</a></li>";
    echo "<li><a href='register.html'>Register a new account</a></li>";
    echo "</ul>";
    echo "</div>";

} catch(PDOException $e) {
    echo "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h3>⚠️ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?> 
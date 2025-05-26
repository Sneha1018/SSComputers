<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect without database selected
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $database = 'onlinecomputerstore_db';
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    $pdo->exec($sql);
    echo "Database '$database' created successfully or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE $database");
    echo "Database selected successfully.<br>";
    
    // Now create the users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Users table created successfully.<br>";
    
    // Create default admin user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$adminPassword]);
        echo "Default admin user created successfully!<br>";
    }
    
    echo "<br>Setup completed successfully!";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 
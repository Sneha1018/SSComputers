<?php
// Include database configuration
require_once 'config/database.php';

try {
    // Add new columns to users table if they don't exist
    $conn->exec("ALTER TABLE users 
                ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user',
                ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    
    echo "Users table updated successfully!<br>";
    
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        // Create default admin user if none exists
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@sscomputers.com', $admin_password, 'admin']);
        echo "Default admin user created!<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
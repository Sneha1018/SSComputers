<?php
require_once 'config/database.php';

try {
    // Create contact_messages table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('new', 'read', 'replied') DEFAULT 'new'
    ) ENGINE=InnoDB");
    
    echo "Contact messages table created successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
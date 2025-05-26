<?php
require_once 'config.php';

try {
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Drop existing users table if exists
    $pdo->exec("DROP TABLE IF EXISTS users");
    echo "Dropped existing users table.\n";
    
    // Create new users table with correct structure
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Users table created with correct structure.\n";
    
    // Verify table structure before adding user
    echo "\nVerifying table structure:\n";
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "  {$column['Field']} ({$column['Type']})";
        if ($column['Key'] == 'PRI') echo " PRIMARY KEY";
        if ($column['Key'] == 'UNI') echo " UNIQUE";
        if ($column['Null'] == 'NO') echo " NOT NULL";
        echo "\n";
    }
    
    // Create default admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$adminPassword]);
    echo "\nDefault admin user created.\n";
    
    // Verify admin user was created correctly
    $sql = "SELECT id, username, role, LENGTH(password) as pass_length FROM users WHERE username = 'admin'";
    $admin = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    echo "\nVerifying admin user:\n";
    echo "  ID: {$admin['id']}\n";
    echo "  Username: {$admin['username']}\n";
    echo "  Role: {$admin['role']}\n";
    echo "  Password hash length: {$admin['pass_length']} characters\n";
    
    echo "\nSetup completed successfully!";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\nError code: " . $e->getCode());
}
?> 
<?php
require_once 'config.php';

try {
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    echo "Starting table modification...<br>";
    
    // First check if we can connect and have proper permissions
    $pdo->query("SELECT 1");
    echo "Database connection verified.<br>";
    
    // Check if the table exists and get its structure
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Users table exists. Checking structure...<br>";
        
        // Get current columns
        $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
        echo "Current columns: " . implode(", ", $columns) . "<br>";
        
        // Add missing columns if they don't exist
        $alterQueries = [];
        
        if (!in_array('username', $columns)) {
            $alterQueries[] = "ADD COLUMN username VARCHAR(50) UNIQUE NOT NULL AFTER id";
        }
        
        if (!in_array('role', $columns)) {
            $alterQueries[] = "ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password";
        }
        
        if (!in_array('created_at', $columns)) {
            $alterQueries[] = "ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        }
        
        if (!in_array('updated_at', $columns)) {
            $alterQueries[] = "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        }
        
        if (!empty($alterQueries)) {
            $alterSql = "ALTER TABLE users " . implode(", ", $alterQueries);
            echo "Executing: " . htmlspecialchars($alterSql) . "<br>";
            $pdo->exec($alterSql);
            echo "Table structure updated successfully!<br>";
        } else {
            echo "Table structure is already up to date.<br>";
        }
        
        // Show final table structure
        echo "Final table structure:<br>";
        $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($columns);
        echo "</pre>";
        
        // Check if we need to create a default admin user
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $adminCount = $stmt->fetchColumn();
        
        if ($adminCount == 0) {
            // Create default admin user (password: admin123)
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$adminPassword]);
            echo "Default admin user created successfully!<br>";
        } else {
            echo "Admin user already exists.<br>";
        }
    } else {
        // Create new table if it doesn't exist
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        echo "Creating new table with SQL: <br>";
        echo "<pre>" . htmlspecialchars($sql) . "</pre><br>";
        
        $pdo->exec($sql);
        echo "Users table created successfully!<br>";
    }
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "<br>Error code: " . $e->getCode());
} catch (Exception $e) {
    die("General Error: " . $e->getMessage());
}
?> 
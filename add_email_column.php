<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Adding Email Column to Users Table</h2>";

try {
    // Include database configuration
    require_once 'config.php';
    
    // Check if email column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    $emailExists = $stmt->rowCount() > 0;
    
    if (!$emailExists) {
        // Add email column
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE AFTER username");
        echo "<p style='color:green;'>Email column added successfully!</p>";
        
        // Update existing users with placeholder emails
        $stmt = $pdo->query("SELECT id, username FROM users");
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            $email = $user['username'] . '@example.com';
            $updateStmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $updateStmt->execute([$email, $user['id']]);
        }
        
        echo "<p style='color:green;'>Updated existing users with placeholder emails.</p>";
    } else {
        echo "<p style='color:blue;'>Email column already exists in the users table.</p>";
    }
    
    // Show table structure
    echo "<h3>Users Table Structure:</h3>";
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='login.html'>Go to Login Page</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?> 
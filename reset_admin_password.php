<?php
require_once 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Reset Admin Password</h2>";

try {
    // Check if admin user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'admin'");
    $adminExists = $stmt->rowCount() > 0;
    
    if ($adminExists) {
        // Update admin password
        $newPassword = 'admin123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $updateStmt->execute([$hashedPassword]);
        
        echo "<p style='color:green;'>Admin password has been reset successfully!</p>";
        echo "<p>New password: <strong>admin123</strong></p>";
    } else {
        // Create admin user
        $newPassword = 'admin123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $insertStmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES ('admin', ?, 'admin')");
        $insertStmt->execute([$hashedPassword]);
        
        echo "<p style='color:green;'>Admin user has been created successfully!</p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
    }
    
    // Verify the password was set correctly
    $verifyStmt = $pdo->query("SELECT * FROM users WHERE username = 'admin'");
    $admin = $verifyStmt->fetch();
    
    if ($admin) {
        echo "<p>Admin user details:</p>";
        echo "<ul>";
        echo "<li>ID: {$admin['id']}</li>";
        echo "<li>Username: {$admin['username']}</li>";
        echo "<li>Role: {$admin['role']}</li>";
        echo "<li>Password hash length: " . strlen($admin['password']) . " characters</li>";
        echo "</ul>";
        
        // Test password verification
        $testPassword = 'admin123';
        $passwordVerified = password_verify($testPassword, $admin['password']);
        
        echo "<p>Password verification test: " . ($passwordVerified ? 
            "<span style='color:green;'>SUCCESS</span>" : 
            "<span style='color:red;'>FAILED</span>") . "</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='debug_login.php'>Go to Debug Login Page</a></p>";
?> 
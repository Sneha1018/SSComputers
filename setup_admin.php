<?php
require_once 'includes/config.php';

try {
    // Add role column if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(10) DEFAULT 'user'");

    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        // Create admin user
        $admin_username = 'admin';
        $admin_email = 'admin@sscomputers.com';
        $admin_password = 'admin123'; // You should change this password after first login
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);

        echo "Admin user created successfully!<br>";
        echo "Username: " . htmlspecialchars($admin_username) . "<br>";
        echo "Email: " . htmlspecialchars($admin_email) . "<br>";
        echo "Password: " . htmlspecialchars($admin_password) . "<br>";
        echo "<strong>Please change this password after first login!</strong><br>";
    } else {
        echo "Admin user already exists!";
    }

    echo "<br><a href='index.php'>Return to homepage</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 
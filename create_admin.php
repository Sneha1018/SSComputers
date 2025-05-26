<?php
// Include database configuration
require_once 'config/database.php';

try {
    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;

    if (!$adminExists) {
        // Admin credentials
        $username = 'admin';
        $email = 'admin@sscomputers.com';
        $password = 'Admin@123'; // This is a secure password that meets our requirements
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert admin user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            echo "Admin user created successfully!<br>";
            echo "Username: " . htmlspecialchars($username) . "<br>";
            echo "Email: " . htmlspecialchars($email) . "<br>";
            echo "Password: " . htmlspecialchars($password) . "<br>";
            echo "<br>Please change these credentials after first login for security.";
        } else {
            echo "Failed to create admin user.";
        }
    } else {
        echo "Admin user already exists in the database.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
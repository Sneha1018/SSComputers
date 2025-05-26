<?php
// Database configuration
$config = [
    'host' => 'localhost',
    'db'   => 'onlinecomputerstore_db',
    'user' => 'root',
    'pass' => ''
];

try {
    // Connect to the database
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create users table if it doesn't exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableSQL)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Sample users to insert
    $users = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'admin'
        ],
        [
            'username' => 'user1',
            'password' => 'user123',
            'role' => 'user'
        ],
        [
            'username' => 'user2',
            'password' => 'password123',
            'role' => 'user'
        ]
    ];

    // Prepare statement for inserting users
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password = ?, role = ?");
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $username, $hashedPassword, $role, $hashedPassword, $role);

    // Insert each user
    foreach ($users as $user) {
        $username = $user['username'];
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $role = $user['role'];
        
        if ($stmt->execute()) {
            echo "User '$username' inserted/updated successfully.<br>";
        } else {
            echo "Error inserting user '$username': " . $stmt->error . "<br>";
        }
    }

    $stmt->close();
    echo "<br>All users have been inserted/updated. You can now test the login system with these credentials:<br>";
    echo "Admin: username: admin, password: admin123<br>";
    echo "User 1: username: user1, password: user123<br>";
    echo "User 2: username: user2, password: password123<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 
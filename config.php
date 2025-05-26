<?php
// Database configuration
$config = [
    'host' => 'localhost',
    'db'   => 'onlinecomputerstore_db',
    'user' => 'root',
    'pass' => ''
];

// For backward compatibility with existing code
define('DB_HOST', $config['host']);
define('DB_NAME', 'onlinecomputerstore_db');
define('DB_USER', $config['user']);
define('DB_PASS', $config['pass']);

// Create PDO connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?> 
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'onlinecomputerstore_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'SCC Computer Store');
define('SITE_URL', 'http://localhost/onlinecomputerstore');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Currency settings
define('CURRENCY', '₹');
define('CURRENCY_POSITION', 'before');
?> 
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'onlinecomputerstore_db';
$username = 'root';
$password = '';

// Set header to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get username and password from POST data
$username_input = $_POST['username'] ?? '';
$password_input = $_POST['password'] ?? '';

// Validate input
if (empty($username_input) || empty($password_input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare SQL statement
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username_input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug information
    $debug = [
        'username_exists' => !empty($user),
        'password_verify_result' => $user ? password_verify($password_input, $user['password']) : false,
        'user_data' => $user ? [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'password_hash_length' => strlen($user['password'])
        ] : null
    ];
    
    // Check if user exists and password is correct
    if ($user && password_verify($password_input, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'username' => $user['username'],
                'role' => $user['role']
            ],
            'debug' => $debug
        ]);
    } else {
        // Return error response
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid username or password',
            'debug' => $debug
        ]);
    }
} catch (PDOException $e) {
    // Log the error
    error_log("Login error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred',
        'debug' => [
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]
    ]);
}
?> 
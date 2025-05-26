<?php
// Database configuration
$config = [
    'host' => 'localhost',
    'db'   => 'onlinecomputerstore_db',
    'user' => 'root',
    'pass' => ''
];

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'user' => null
];

// Function to verify login credentials
function verifyLogin($conn, $username, $password) {
    global $response;
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            $response['success'] = true;
            $response['message'] = "Login successful!";
            $response['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            return true;
        } else {
            $response['message'] = "Invalid password.";
            return false;
        }
    } else {
        $response['message'] = "User not found.";
        return false;
    }
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Connect to the database
        $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            throw new Exception("Please fill in all fields");
        }

        verifyLogin($conn, $username, $password);
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
}

// If this is an AJAX request, return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .user-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }
        .actions {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify Login</h1>
        <p>Enter credentials to verify login functionality.</p>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Verify Login</button>
        </form>
        
        <?php if (!empty($response['message'])): ?>
            <div class="result <?php echo $response['success'] ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($response['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($response['success'] && $response['user']): ?>
            <div class="user-info">
                <h2>User Information</h2>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($response['user']['id']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($response['user']['username']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($response['user']['role']); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="view_users.php" class="btn">View All Users</a>
            <a href="test_login.php" class="btn">Test Login</a>
            <a href="login.html" class="btn">Go to Login Page</a>
        </div>
    </div>
</body>
</html> 
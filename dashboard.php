<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Get user information
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// Database configuration
$config = [
    'host' => 'localhost',
    'db'   => 'onlinecomputerstore_db',
    'user' => 'root',
    'pass' => ''
];

// Get user details from database
try {
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT username, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userDetails = $result->fetch_assoc();
    
    $stmt->close();
} catch (Exception $e) {
    $error = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SSComputers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .navbar {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #4CAF50;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info i {
            font-size: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .dashboard-header {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .welcome-message {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .user-details {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .user-details p {
            margin-bottom: 5px;
        }
        
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .dashboard-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-card h3 {
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dashboard-card i {
            color: #4CAF50;
        }
        
        .dashboard-card p {
            margin-bottom: 15px;
            color: #666;
        }
        
        .dashboard-card a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .dashboard-card a:hover {
            background-color: #45a049;
        }
        
        .admin-section {
            background-color: #fff3e0;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .admin-section h2 {
            color: #e65100;
            margin-bottom: 15px;
        }
        
        .admin-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .admin-links a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #ff9800;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .admin-links a:hover {
            background-color: #f57c00;
        }
        
        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .user-info {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">SSComputers</div>
        <div class="nav-links">
            <a href="index.html"><i class="fas fa-home"></i> Home</a>
            <a href="laptops.html"><i class="fas fa-laptop"></i> Laptops</a>
            <a href="desktops.html"><i class="fas fa-desktop"></i> Desktops</a>
            <a href="accessories.html"><i class="fas fa-keyboard"></i> Accessories</a>
            <a href="cart.html"><i class="fas fa-shopping-cart"></i> Cart</a>
        </div>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-header">
            <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>This is your personal dashboard where you can manage your account and view your orders.</p>
            
            <div class="user-details">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($userDetails['username'] ?? $username); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($userDetails['role'] ?? $role); ?></p>
                <p><strong>Account Created:</strong> <?php echo htmlspecialchars($userDetails['created_at'] ?? 'N/A'); ?></p>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-card">
                <h3><i class="fas fa-shopping-bag"></i> My Orders</h3>
                <p>View and track your recent orders.</p>
                <a href="#">View Orders</a>
            </div>
            
            <div class="dashboard-card">
                <h3><i class="fas fa-heart"></i> Wishlist</h3>
                <p>Check your saved items and wishlist.</p>
                <a href="wishlist.html">View Wishlist</a>
            </div>
            
            <div class="dashboard-card">
                <h3><i class="fas fa-user-cog"></i> Account Settings</h3>
                <p>Update your profile and account settings.</p>
                <a href="#">Manage Account</a>
            </div>
        </div>
        
        <?php if ($role === 'admin'): ?>
        <div class="admin-section">
            <h2><i class="fas fa-shield-alt"></i> Admin Controls</h2>
            <div class="admin-links">
                <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                <a href="view_users.php"><i class="fas fa-users"></i> Manage Users</a>
                <a href="#"><i class="fas fa-box"></i> Manage Products</a>
                <a href="#"><i class="fas fa-chart-bar"></i> View Reports</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 
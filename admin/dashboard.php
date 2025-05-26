<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header('Location: ../login.php');
    exit;
}

// Include database configuration
require_once '../config/database.php';

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Initialize variables with default values
$total_users = 0;
$total_products = 0;
$total_orders = 0;
$total_messages = 0;
$total_payments = 0;
$total_revenue = 0;
$cod_payments = 0;
$upi_payments = 0;
$recent_users = [];
$recent_products = [];

// Get statistics
try {
    // Total users
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_users = $stmt->fetchColumn();

    // Total products
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    // Total orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders");
    $total_orders = $stmt->fetchColumn();

    // Total messages
    $stmt = $conn->query("SELECT COUNT(*) FROM contact_messages");
    $total_messages = $stmt->fetchColumn();

    // Recent users
    $stmt = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent products
    $stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
    $recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if payments table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'payments'");
    if ($stmt->rowCount() > 0) {
        // Get payment statistics only if the table exists
        $stmt = $conn->query("SELECT COUNT(*) FROM payments");
        $total_payments = $stmt->fetchColumn();

        $stmt = $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'");
        $total_revenue = $stmt->fetchColumn();

        $stmt = $conn->query("SELECT COUNT(*) FROM payments WHERE payment_method = 'cod'");
        $cod_payments = $stmt->fetchColumn();

        $stmt = $conn->query("SELECT COUNT(*) FROM payments WHERE payment_method = 'upi'");
        $upi_payments = $stmt->fetchColumn();
    }

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .nav-link {
            color: rgba(255,255,255,.8);
        }
        .nav-link:hover {
            color: white;
        }
        .nav-link.active {
            background: rgba(255,255,255,.1);
        }
        .stat-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white">Admin Panel</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="bi bi-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">
                            <i class="bi bi-envelope"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div>
                        <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo $total_users; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <h2 class="card-text"><?php echo $total_products; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="card-text"><?php echo $total_orders; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Messages</h5>
                                <h2 class="card-text"><?php echo $total_messages; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <h2 class="card-text">₹<?php echo number_format($total_revenue, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Payments</h5>
                                <h2 class="card-text"><?php echo $total_payments; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">COD Orders</h5>
                                <h2 class="card-text"><?php echo $cod_payments; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">UPI Payments</h5>
                                <h2 class="card-text"><?php echo $upi_payments; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Products -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                                <td><?php echo $product['stock']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all payments with order and user information
try {
    $stmt = $conn->query("
        SELECT p.*, o.id as order_id, u.username, u.email 
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        JOIN users u ON o.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Calculate total payments
$stmt = $conn->prepare("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");
$stmt->execute();
$total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Get payment statistics
$stmt = $conn->prepare("SELECT payment_method, COUNT(*) as count FROM payments GROUP BY payment_method");
$stmt->execute();
$payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - SSComputers Admin</title>
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
        .status-badge {
            text-transform: capitalize;
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
                        <a class="nav-link" href="dashboard.php">
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
                        <a class="nav-link active" href="payments.php">
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
                <h2 class="mb-4">Payment Transactions</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Payment Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Payments</h5>
                                <h2 class="card-text">₹<?php echo number_format($total_payments, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($payment_methods as $method): ?>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo ucfirst($method['payment_method']); ?> Payments</h5>
                                    <h2 class="card-text"><?php echo $method['count']; ?></h2>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td>#<?php echo $payment['id']; ?></td>
                                        <td>
                                            <a href="orders.php?id=<?php echo $payment['order_id']; ?>">#<?php echo $payment['order_id']; ?></a>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($payment['username']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($payment['email']); ?></small>
                                        </td>
                                        <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                        <td>
                                            <span class="badge status-badge bg-<?php 
                                                echo match($payment['status']) {
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $payment['transaction_id'] ?? 'N/A'; ?></td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
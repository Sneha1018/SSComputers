<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize variables
$success = '';
$error = '';

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        // Update payment status
        $payment_status = match($new_status) {
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
        
        $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE order_id = ?");
        $stmt->execute([$payment_status, $order_id]);
        
        // Commit transaction
        $conn->commit();
        $success = "Order and payment status updated successfully";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Get all orders with user information
try {
    $stmt = $conn->query("
        SELECT o.*, u.username, u.email, 
               COUNT(oi.id) as total_items,
               GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - SSComputers Admin</title>
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
                        <a class="nav-link active" href="orders.php">
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
                <h2 class="mb-4">Manage Orders</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($order['username']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo $order['total_items']; ?> items</div>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['items']); ?></small>
                                        </td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge status-badge bg-<?php 
                                                echo match($order['status']) {
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="viewOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')">
                                                <i class="bi bi-pencil"></i> Update Status
                                            </button>
                                        </td>
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

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p class="mb-1">Name: <span id="customer_name"></span></p>
                            <p class="mb-1">Email: <span id="customer_email"></span></p>
                            <p class="mb-3">Shipping Address: <span id="shipping_address"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1">Order ID: #<span id="order_id"></span></p>
                            <p class="mb-1">Order Date: <span id="order_date"></span></p>
                            <p class="mb-1">Payment Method: <span id="payment_method"></span></p>
                            <p class="mb-3">Status: <span id="order_status"></span></p>
                        </div>
                    </div>
                    <h6>Order Items</h6>
                    <div id="order_items"></div>
                    <div class="text-end mt-3">
                        <h5>Total Amount: ₹<span id="total_amount"></span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="status_order_id">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status_select" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrder(order) {
            document.getElementById('customer_name').textContent = order.username;
            document.getElementById('customer_email').textContent = order.email;
            document.getElementById('shipping_address').textContent = order.shipping_address || 'Not provided';
            document.getElementById('order_id').textContent = order.id;
            document.getElementById('order_date').textContent = new Date(order.created_at).toLocaleString();
            document.getElementById('payment_method').textContent = order.payment_method || 'Not specified';
            document.getElementById('order_status').textContent = order.status;
            document.getElementById('total_amount').textContent = parseFloat(order.total_amount).toFixed(2);
            document.getElementById('order_items').textContent = order.items;
            
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }

        function updateStatus(orderId, currentStatus) {
            document.getElementById('status_order_id').value = orderId;
            document.getElementById('status_select').value = currentStatus;
            
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }
    </script>
</body>
</html> 
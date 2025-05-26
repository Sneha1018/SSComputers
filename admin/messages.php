<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle message deletion
if (isset($_POST['delete_message']) && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $_SESSION['success'] = "Message deleted successfully!";
    header("Location: messages.php");
    exit();
}

// Fetch all messages
$stmt = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages</title>
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
        .messages-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                        <a class="nav-link active" href="messages.php">
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

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Messages</h2>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages as $message): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($message['id']); ?></td>
                                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                            <td class="message-content"><?php echo htmlspecialchars($message['message']); ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($message['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $message['id']; ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                    <button type="submit" name="delete_message" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Message Modal -->
                                        <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Message Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
                                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                                                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                                                        <p><strong>Message:</strong></p>
                                                        <div class="border rounded p-3 bg-light">
                                                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                        </div>
                                                        <p class="mt-2"><small class="text-muted">Received: <?php echo date('Y-m-d H:i:s', strtotime($message['created_at'])); ?></small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
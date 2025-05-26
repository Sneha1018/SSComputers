<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get cart total
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT SUM(c.quantity * p.price) as total 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method - SSComputers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .payment-option {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-option:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .payment-option.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .upi-qr {
            max-width: 200px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Select Payment Method</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>Order Total: ₹<?php echo number_format($cart_total, 2); ?></h5>
                        </div>

                        <form id="paymentForm" action="process_order.php" method="POST">
                            <!-- Cash on Delivery -->
                            <div class="payment-option" onclick="selectPayment('cod')">
                                <input type="radio" name="payment_method" id="cod" value="cod" class="d-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash-stack fs-3 me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Cash on Delivery</h5>
                                        <p class="mb-0 text-muted">Pay when you receive your order</p>
                                    </div>
                                </div>
                            </div>

                            <!-- UPI Payment -->
                            <div class="payment-option" onclick="selectPayment('upi')">
                                <input type="radio" name="payment_method" id="upi" value="upi" class="d-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-phone fs-3 me-3"></i>
                                    <div>
                                        <h5 class="mb-1">UPI Payment</h5>
                                        <p class="mb-0 text-muted">Pay using UPI (Scan QR Code)</p>
                                    </div>
                                </div>
                                <div id="upiDetails" class="text-center mt-3 d-none">
                                    <img src="./images/QR.jpg" alt="UPI QR Code" class="upi-qr">
                                    <p class="text-muted mt-2">Scan this QR code to pay using any UPI app</p>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            const selectedOption = document.getElementById(method).closest('.payment-option');
            selectedOption.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
            
            // Show/hide UPI details
            const upiDetails = document.getElementById('upiDetails');
            if (method === 'upi') {
                upiDetails.classList.remove('d-none');
            } else {
                upiDetails.classList.add('d-none');
            }
        }
    </script>
</body>
</html> 
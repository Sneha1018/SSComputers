<?php
require_once '../config/database.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize variables
$success = '';
$error = '';

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    try {
        // Get the image URL before deleting the product
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        // Delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        // Delete the image file if it exists
        if ($product && $product['image']) {
            $image_path = __DIR__ . '/../' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $success = "Product deleted successfully";
    } catch (PDOException $e) {
        $error = "Error deleting product: " . $e->getMessage();
    }
}

// Handle product addition/update
if (isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    try {
        // Handle image upload
        $image = null;
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['product_image']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');
            }
            
            // Generate unique filename
            $new_filename = uniqid('product_') . '.' . $file_ext;
            $upload_path = '../uploads/products/' . $new_filename;
            
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload image.');
            }
            
            $image = 'uploads/products/' . $new_filename;
        }
        
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            // Update existing product
            if ($image) {
                // If new image is uploaded, delete old image
                $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->execute([$_POST['product_id']]);
                $old_product = $stmt->fetch();
                if ($old_product && $old_product['image']) {
                    $old_image_path = __DIR__ . '/../' . $old_product['image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, featured = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $stock, $category, $featured, $image, $_POST['product_id']]);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, featured = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $stock, $category, $featured, $_POST['product_id']]);
            }
            $success = "Product updated successfully";
        } else {
            // Add new product
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, featured, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $stock, $category, $featured, $image]);
            $success = "Product added successfully";
        }
    } catch (Exception $e) {
        $error = "Error saving product: " . $e->getMessage();
    }
}

// Get all products
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Get unique categories
$categories = [];
foreach ($products as $product) {
    if (!in_array($product['category'], $categories)) {
        $categories[] = $product['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - SSComputers Admin</title>
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
        .product-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
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
                        <a class="nav-link active" href="products.php">
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
                    <h2>Manage Products</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </button>
                </div>

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
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <?php if ($product['image']): ?>
                                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="product-image">
                                            <?php else: ?>
                                                <div class="text-muted">No image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['featured'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $product['featured'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" name="delete_product" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
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

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="product_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="stock" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" id="category" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="product_image" id="product_image" accept="image/*">
                            <div class="form-text">Supported formats: JPG, JPEG, PNG, GIF</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="featured" id="featured">
                                <label class="form-check-label">Featured Product</label>
                            </div>
                        </div>
                        
                        <div id="current_image_container" class="mb-3 d-none">
                            <label class="form-label">Current Image</label>
                            <img id="current_image" src="" alt="Current product image" class="d-block mb-2" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="save_product" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(product) {
            document.getElementById('product_id').value = product.id;
            document.getElementById('name').value = product.name;
            document.getElementById('description').value = product.description;
            document.getElementById('price').value = product.price;
            document.getElementById('stock').value = product.stock;
            document.getElementById('category').value = product.category;
            document.getElementById('featured').checked = product.featured == 1;
            
            // Handle current image display
            const currentImageContainer = document.getElementById('current_image_container');
            const currentImage = document.getElementById('current_image');
            
            if (product.image) {
                currentImage.src = '../' + product.image;
                currentImageContainer.classList.remove('d-none');
            } else {
                currentImageContainer.classList.add('d-none');
            }
            
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        // Reset form when modal is closed
        document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('product_id').value = '';
            document.querySelector('form').reset();
            document.getElementById('current_image_container').classList.add('d-none');
        });
    </script>
</body>
</html> 
<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$product = new Product();
$category = new Category();
$user = new User();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $category_name = $_POST['category'] ?? '';
            $image = $_POST['image'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if ($product->add($name, $price, $category_name, $image, $description, isset($_POST['isBestSeller']), isset($_POST['isFeatured']))) {
                $message = 'Product added successfully!';
            } else {
                $error = 'Failed to add product.';
            }
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $category_name = $_POST['category'] ?? '';
            $image = $_POST['image'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if ($product->update($id, $name, $price, $category_name, $image, $description, isset($_POST['isBestSeller']), isset($_POST['isFeatured']))) {
                $message = 'Product updated successfully!';
            } else {
                $error = 'Failed to update product.';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($product->delete($id)) {
                $message = 'Product deleted successfully!';
            } else {
                $error = 'Failed to delete product.';
            }
            break;
    }
}

// Get all products for display
$products = $product->getAll();
$categories = $category->getActive();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Products Management - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin-bootstrap.css" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fa-solid fa-cog me-2"></i>Muscle Bull</h3>
            <small>Admin Panel</small>
        </div>
        
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="nav-link">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="products.php" class="nav-link active">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="orders.php" class="nav-link">
                <i class="fa-solid fa-shopping-cart"></i> Orders
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="categories.php" class="nav-link">
                <i class="fa-solid fa-tags"></i> Categories
            </a>
            <a href="gift-cards.php" class="nav-link">
                <i class="fa-solid fa-gift"></i> Gift Cards
            </a>
        </nav>
        
        <div class="user-info">
            <div class="text-white">
                <i class="fa-solid fa-user me-2"></i>
                <?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?>
            </div>
            <small class="text-muted">Administrator</small>
        </div>
        
        <div class="logout-btn">
            <a href="logout.php" class="btn btn-outline-light btn-sm w-100">
                <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1><i class="fa-solid fa-box me-2"></i>Products Management</h1>
            <p class="mb-0">Manage your product inventory</p>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="form-section">
            <h3 class="h4 text-uppercase fw-bold mb-4">Add New Product</h3>
            <form method="POST" action="products.php">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Product Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Price (LKR)</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Image URL</label>
                        <input type="text" class="form-control" name="image" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="isBestSeller" id="isBestSeller">
                            <label class="form-check-label" for="isBestSeller">Best Seller</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="isFeatured" id="isFeatured">
                            <label class="form-check-label" for="isFeatured">Featured</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-plus me-2"></i>Add Product
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="table-responsive mb-5 mt-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">Manage Products (<?php echo count($products); ?> items)</h3>
            <table class="table product-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Best Seller</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>LKR <?php echo number_format($product['price']); ?></td>
                            <td>
                                <?php if ($product['isBestSeller']): ?>
                                    <span class="badge bg-primary badge-status">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-status">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['isFeatured']): ?>
                                    <span class="badge bg-success badge-status">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-status">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-action" 
                                            onclick="editProduct(<?php echo $product['id']; ?>)"
                                            title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <form method="POST" action="products.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-action" 
                                                onclick="return confirm('Are you sure you want to delete this product?')"
                                                title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Product Form (Hidden by default) -->
        <div id="editForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Edit Product</h3>
            <form method="POST" action="products.php" id="editProductForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editProductId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Product Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Price (LKR)</label>
                        <input type="number" class="form-control" name="price" id="editPrice" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category" id="editCategory" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Image URL</label>
                        <input type="text" class="form-control" name="image" id="editImage" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="isBestSeller" id="editIsBestSeller">
                            <label class="form-check-label" for="editIsBestSeller">Best Seller</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="isFeatured" id="editIsFeatured">
                            <label class="form-check-label" for="editIsFeatured">Featured</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Update Product
                        </button>
                        <button type="button" class="btn btn-outline-dark text-uppercase fw-bold ms-2" 
                                onclick="cancelEdit()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <script>
        // Store products data for JavaScript
        const products = <?php echo json_encode($products); ?>;
        
        function editProduct(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editName').value = product.name;
                document.getElementById('editPrice').value = product.price;
                document.getElementById('editCategory').value = product.category;
                document.getElementById('editImage').value = product.image;
                document.getElementById('editDescription').value = product.description;
                document.getElementById('editIsBestSeller').checked = product.isBestSeller;
                document.getElementById('editIsFeatured').checked = product.isFeatured;
                
                document.getElementById('editForm').style.display = 'block';
                document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function cancelEdit() {
            document.getElementById('editProductForm').reset();
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
<!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fa-solid fa-bars"></i>
    </button>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    
    <script>
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.mobile-menu-overlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
</body>
</html>

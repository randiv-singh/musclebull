<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Category.php';
require_once '../classes/Product.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$category = new Category();
$product = new Product();
$user = new User();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = $_POST['image'] ?? '';
            
            if ($category->add($name, $description, $image)) {
                $message = 'Category added successfully!';
            } else {
                $error = 'Failed to add category. Name might already exist.';
            }
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $image = $_POST['image'] ?? '';
            $status = $_POST['status'] ?? 'active';
            
            if ($category->update($id, $name, $description, $image, $status)) {
                $message = 'Category updated successfully!';
            } else {
                $error = 'Failed to update category.';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($category->delete($id)) {
                $message = 'Category deleted successfully!';
            } else {
                $error = 'Failed to delete category.';
            }
            break;
    }
}

// Get all categories with product counts
$categories = $category->getWithProductCounts($product);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Categories Management - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        
        .sidebar {
            background: #000;
            color: #fff;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        
        .sidebar-header h3 {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .nav-link {
            color: #fff;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: #333;
            color: #fff;
        }
        
        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .content-header {
            background: #fff;
            border: 2px solid #000;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .content-header h1 {
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        
        .form-section {
            background: #fff;
            padding: 2rem;
            border: 2px solid #000;
            margin-bottom: 2rem;
        }
        
        .category-table {
            background: #fff;
            border: 2px solid #000;
        }
        
        .category-table th {
            background: #000;
            color: #fff;
            border: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .category-table td {
            vertical-align: middle;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 0.25rem;
        }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .user-info {
            padding: 1rem 1.5rem;
            border-top: 1px solid #333;
            font-size: 0.875rem;
        }
        
        .logout-btn {
            position: absolute;
            bottom: 1rem;
            left: 1.5rem;
            right: 1.5rem;
        }
        
        .category-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        
        .product-count {
            font-size: 1.25rem;
            font-weight: bold;
            color: #000;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .logout-btn {
                position: relative;
                margin-top: 1rem;
            }
        }
    </style>
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
            <a href="products.php" class="nav-link">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="orders.php" class="nav-link">
                <i class="fa-solid fa-shopping-cart"></i> Orders
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="categories.php" class="nav-link active">
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
            <h1><i class="fa-solid fa-tags me-2"></i>Categories Management</h1>
            <p class="mb-0">Manage product categories and organization</p>
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

        <!-- Add Category Form -->
        <div class="form-section">
            <h3 class="h4 text-uppercase fw-bold mb-4">Add New Category</h3>
            <form method="POST" action="categories.php">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Image URL</label>
                        <input type="url" class="form-control" name="image" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Brief description of the category"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-plus me-2"></i>Add Category
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="table-responsive mb-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">Manage Categories (<?php echo count($categories); ?> categories)</h3>
            <table class="table category-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <?php if ($cat['image']): ?>
                                    <img src="<?php echo htmlspecialchars($cat['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                                         class="category-image">
                                <?php else: ?>
                                    <div class="category-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fa-solid fa-tag text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></div>
                                <small class="text-muted">ID: <?php echo $cat['id']; ?></small>
                            </td>
                            <td>
                                <?php 
                                $desc = $cat['description'];
                                echo htmlspecialchars(strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc); 
                                ?>
                            </td>
                            <td>
                                <span class="product-count"><?php echo $cat['product_count']; ?></span>
                                <small class="text-muted d-block">products</small>
                            </td>
                            <td>
                                <?php if ($cat['status'] === 'active'): ?>
                                    <span class="badge bg-success badge-status">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-status">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($cat['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-action" 
                                            onclick="editCategory(<?php echo $cat['id']; ?>)"
                                            title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <?php if ($cat['product_count'] == 0): ?>
                                        <form method="POST" action="categories.php" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-action" 
                                                    onclick="return confirm('Are you sure you want to delete this category?')"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-secondary btn-action" 
                                                disabled
                                                title="Cannot delete category with products">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Category Form (Hidden by default) -->
        <div id="editForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Edit Category</h3>
            <form method="POST" action="categories.php" id="editCategoryForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Image URL</label>
                        <input type="url" class="form-control" name="image" id="editImage">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="editStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Product Count</label>
                        <input type="text" class="form-control" id="editProductCount" readonly>
                        <small class="text-muted">Automatically calculated</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Update Category
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

    <!-- Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Store categories data for JavaScript
        const categories = <?php echo json_encode($categories); ?>;
        
        function editCategory(id) {
            const cat = categories.find(c => c.id === id);
            if (cat) {
                document.getElementById('editId').value = cat.id;
                document.getElementById('editName').value = cat.name;
                document.getElementById('editImage').value = cat.image || '';
                document.getElementById('editDescription').value = cat.description || '';
                document.getElementById('editStatus').value = cat.status;
                document.getElementById('editProductCount').value = cat.product_count + ' products';
                
                document.getElementById('editForm').style.display = 'block';
                document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function cancelEdit() {
            document.getElementById('editCategoryForm').reset();
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>

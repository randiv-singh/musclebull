<?php
require_once 'components/header.php';
require_once 'components/footer.php';
require_once 'classes/Product.php';

// Initialize Product
$product = new Product();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $category = $_POST['category'] ?? '';
            $image = $_POST['image'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if ($product->add($name, $price, $category, $image, $description, isset($_POST['isBestSeller']), isset($_POST['isFeatured']))) {
                $message = 'Product added successfully!';
            } else {
                $error = 'Failed to add product.';
            }
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $category = $_POST['category'] ?? '';
            $image = $_POST['image'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if ($product->update($id, $name, $price, $category, $image, $description, isset($_POST['isBestSeller']), isset($_POST['isFeatured']))) {
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
$categories = [];
foreach ($products as $p) {
    if (!in_array($p['category'], $categories)) {
        $categories[] = $p['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./css/style.css" rel="stylesheet" />
    <link href="./css/header.css" rel="stylesheet" />
    <link href="./css/footer.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        .admin-panel {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .admin-header {
            background: #000;
            color: #fff;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .product-table {
            background: #fff;
            border-radius: 0;
        }
        .product-table th {
            background: #000;
            color: #fff;
            border: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .form-section {
            background: #fff;
            padding: 2rem;
            border: 2px solid #000;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = '';
    include 'components/header.php'; 
    ?>

    <div class="admin-panel">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="container">
                <h1 class="h3 mb-0 text-uppercase fw-bold">
                    <i class="fa-solid fa-cog me-2"></i>Admin Panel
                </h1>
            </div>
        </div>

        <div class="container">
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
                <form method="POST" action="admin.php">
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
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Image URL</label>
                            <input type="url" class="form-control" name="image" required>
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
            <div class="table-responsive mb-5">
                <h3 class="h4 text-uppercase fw-bold mb-4">Manage Products</h3>
                <table class="table product-table">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>LKR <?php echo number_format($product['price']); ?></td>
                                <td>
                                    <?php if ($product['isBestSeller']): ?>
                                        <span class="badge bg-primary">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['isFeatured']): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-action" 
                                                onclick="editProduct(<?php echo $product['id']; ?>)"
                                                title="Edit">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <form method="POST" action="admin.php" style="display: inline;">
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
                <form method="POST" action="admin.php" id="editProductForm">
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
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Image URL</label>
                            <input type="url" class="form-control" name="image" id="editImage" required>
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
    </div>

    <?php 
    $border_top = true;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./js/bootstrap.bundle.min.js"></script>
    
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
</body>
</html>

<?php
require_once 'components/header.php';
require_once 'components/footer.php';
require_once 'components/product-card.php';
require_once 'classes/Product.php';

// Initialize Product
$product = new Product();

// Get products data
$products = $product->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="./assets/css/shop.css" rel="stylesheet" />
    <link href="./assets/css/checkout.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = 'shop';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Shop</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Products Section -->
        <section class="products-section py-5 bg-white">
            <div class="container">
                <div class="row">
                    <!-- Filters Sidebar -->
                    <div class="col-lg-3 mb-4 mb-lg-0">
                        <div class="filters-sidebar">
                            <h5 class="text-uppercase fw-bold mb-4">Filters</h5>
                            
                            <!-- Category Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title text-uppercase fw-bold mb-3">Category</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cat-all" checked>
                                    <label class="form-check-label" for="cat-all">All Products</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cat-tshirts">
                                    <label class="form-check-label" for="cat-tshirts">T-Shirts</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cat-hoodies">
                                    <label class="form-check-label" for="cat-hoodies">Hoodies</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cat-pants">
                                    <label class="form-check-label" for="cat-pants">Pants</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="cat-tanks">
                                    <label class="form-check-label" for="cat-tanks">Tank Tops</label>
                                </div>
                            </div>

                            <!-- Size Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title text-uppercase fw-bold mb-3">Size</h6>
                                <div class="size-options d-flex flex-wrap gap-2">
                                    <button class="size-btn active">XS</button>
                                    <button class="size-btn">S</button>
                                    <button class="size-btn">M</button>
                                    <button class="size-btn">L</button>
                                    <button class="size-btn">XL</button>
                                    <button class="size-btn">XXL</button>
                                </div>
                            </div>

                            <!-- Price Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title text-uppercase fw-bold mb-3">Price Range</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="price-1">
                                    <label class="form-check-label" for="price-1">Under LKR 3,000</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="price-2">
                                    <label class="form-check-label" for="price-2">LKR 3,000 - 5,000</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="price-3">
                                    <label class="form-check-label" for="price-3">LKR 5,000 - 7,000</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="price-4">
                                    <label class="form-check-label" for="price-4">Above LKR 7,000</label>
                                </div>
                            </div>

                            <!-- Clear Filters -->
                            <button class="btn btn-outline-dark w-100 text-uppercase fw-bold mt-3">Clear All</button>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="col-lg-9">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="fw-bold text-uppercase m-0">All Products</h2>
                            <select class="form-select sort-select">
                                <option selected>Sort By: Featured</option>
                                <option value="1">Price: Low to High</option>
                                <option value="2">Price: High to Low</option>
                                <option value="3">Newest</option>
                            </select>
                        </div>

                        <div class="row g-4" id="all-products-container">
                            <?php 
                            foreach ($products as $product) {
                                include 'components/product-card.php';
                            }
                            ?>
                        </div>

                        <!-- Pagination -->
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Quick View Modal -->
    <div class="quick-view-modal" id="quickViewModal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <div class="modal-product-detail">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <img src="" alt="Product" id="modalProductImage" class="modal-product-image">
                    </div>
                    <div class="col-lg-6">
                        <div class="modal-product-info">
                            <h2 class="modal-product-title" id="modalProductName">Product Name</h2>
                            <p class="modal-product-price" id="modalProductPrice">LKR 0</p>
                            <p class="modal-product-description">
                                Premium quality fitness apparel designed for maximum comfort and performance during your workouts.
                            </p>

                            <!-- Size Selector -->
                            <div class="modal-size-selector">
                                <label class="form-label fw-bold text-uppercase mb-3">Select Size</label>
                                <div class="modal-size-options">
                                    <button class="modal-size-btn">XS</button>
                                    <button class="modal-size-btn">S</button>
                                    <button class="modal-size-btn active">M</button>
                                    <button class="modal-size-btn">L</button>
                                    <button class="modal-size-btn">XL</button>
                                    <button class="modal-size-btn">XXL</button>
                                </div>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="modal-quantity-selector">
                                <label class="form-label fw-bold text-uppercase mb-3">Quantity</label>
                                <div class="modal-quantity-control">
                                    <button class="modal-qty-btn minus">-</button>
                                    <input type="number" value="1" min="1" class="modal-qty-input">
                                    <button class="modal-qty-btn plus">+</button>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="modal-actions">
                                <button class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold flex-grow-1">
                                    <i class="fa-solid fa-bag-shopping me-2"></i> Add to Cart
                                </button>
                                <a href="product.php" class="btn btn-outline-dark btn-lg px-4 py-3">
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $border_top = true;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/product.js"></script>
</body>
</html>

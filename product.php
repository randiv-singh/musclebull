<?php

require_once 'components/product-card.php';
require_once 'classes/Product.php';

// Initialize Product
$product = new Product();

// Get current product (default to first product for demo)
$current_product_id = $_GET['id'] ?? 1;
$current_product = $product->getById($current_product_id);

// Fallback to first product if not found
if (!$current_product) {
    $products = $product->getAll();
    $current_product = $products[0] ?? null;
}

// Get related products (exclude current product)
$all_products = $product->getAll();
$related_products = array_filter($all_products, function($product_item) use ($current_product) {
    return $current_product && $product_item['id'] != $current_product['id'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($current_product['name']); ?> - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="./assets/css/shop.css" rel="stylesheet" />
    <link href="./assets/css/product.css?v=2" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = '';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item"><a href="shop.php" class="text-black">Shop</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page"><?php echo htmlspecialchars($current_product['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Product Detail Section -->
        <section class="product-detail-section py-5 bg-white">
            <div class="container">
                <div class="row g-5">
                    <!-- Product Images -->
                    <div class="col-lg-6">
                        <div class="product-images">
                            <!-- Main Image -->
                            <div class="main-image mb-3">
                                <img src="<?php echo htmlspecialchars($current_product['image']); ?>" alt="<?php echo htmlspecialchars($current_product['name']); ?>" id="mainProductImage" class="img-fluid">
                            </div>
                            <!-- Thumbnail Images -->
                            <div class="thumbnail-images d-flex gap-3">
                                <?php foreach ($current_product['thumbnails'] as $index => $thumbnail): ?>
                                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="View <?php echo $index + 1; ?>" class="img-fluid">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-lg-6">
                        <div class="product-detail-info">
                            <!-- Badge -->
                            <?php if ($current_product['isBestSeller']): ?>
                                <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase fw-bold">Best Seller</span>
                            <?php endif; ?>
                            
                            <!-- Product Name -->
                            <h1 class="product-title fw-bold text-uppercase mb-3"><?php echo htmlspecialchars($current_product['name']); ?></h1>
                            
                            <!-- Rating -->
                            <div class="product-rating mb-3">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                                <span class="ms-2 fw-medium">(4.5) 127 Reviews</span>
                            </div>
                            
                            <!-- Price -->
                            <div class="product-price mb-4">
                                <span class="current-price fw-bold">LKR <?php echo number_format($current_product['price']); ?></span>
                            </div>
                            
                            <!-- Description -->
                            <p class="product-description mb-4">
                                <?php echo htmlspecialchars($current_product['description']); ?>
                            </p>

                            <!-- Size Selector -->
                            <div class="size-selector-single mb-4">
                                <label class="form-label fw-bold text-uppercase mb-3">Select Size</label>
                                <div class="size-options-single d-flex flex-wrap gap-3">
                                    <button class="size-btn-single">XS</button>
                                    <button class="size-btn-single">S</button>
                                    <button class="size-btn-single active">M</button>
                                    <button class="size-btn-single">L</button>
                                    <button class="size-btn-single">XL</button>
                                    <button class="size-btn-single">XXL</button>
                                </div>
                                <span role="button" class="size-guide-link mt-2 d-inline-block" id="sizeGuideBtn">Size Guide</span>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="quantity-selector-single mb-4">
                                <label class="form-label fw-bold text-uppercase mb-3">Quantity</label>
                                <div class="d-flex align-items-center">
                                    <div class="quantity-control">
                                        <button class="qty-btn-single minus">-</button>
                                        <input type="number" value="1" min="1" class="qty-input-single">
                                        <button class="qty-btn-single plus">+</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add to Cart & Wishlist -->
                            <div class="product-actions d-flex gap-3 mb-4">
                                <button class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold flex-grow-1">
                                    <i class="fa-solid fa-bag-shopping me-2"></i> Add to Cart
                                </button>
                                <button class="btn btn-outline-dark btn-lg px-4 py-3">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>

                            <!-- Product Features -->
                            <div class="product-features">
                                <div class="feature-item">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    <span>Free shipping on orders over LKR 5,000</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span>30-day return policy</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>Secure payment guaranteed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Tabs -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="product-tabs">
                            <ul class="nav nav-tabs product-tabs__nav mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link product-tab-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-selected="true">
                                        Description
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link product-tab-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-selected="false">
                                        Details
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link product-tab-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-selected="false">
                                        Reviews (127)
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="description" role="tabpanel">
                                    <p><?php echo htmlspecialchars($current_product['description']); ?></p>
                                    <p>Features include reinforced stitching, moisture-wicking properties, and our signature Muscle Bull logo. 
                                    Available in multiple sizes to fit your unique style.</p>
                                </div>
                                <div class="tab-pane fade" id="details" role="tabpanel">
                                    <ul class="details-list">
                                        <li><strong>Material:</strong> 100% Premium Cotton</li>
                                        <li><strong>Fit:</strong> Oversized</li>
                                        <li><strong>Color:</strong> Black</li>
                                        <li><strong>Care:</strong> Machine wash cold, tumble dry low</li>
                                        <li><strong>Made in:</strong> Sri Lanka</li>
                                        <li><strong>Weight:</strong> 200gsm</li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="reviews" role="tabpanel">
                                    <div class="reviews-section">
                                        <div class="review-item">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div>
                                                    <strong>John Doe</strong>
                                                    <div class="review-stars">
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                    </div>
                                                </div>
                                                <span class="text-muted small">2 days ago</span>
                                            </div>
                                            <p>Amazing quality! The fit is perfect and the material is super comfortable. Highly recommend!</p>
                                        </div>
                                        <hr>
                                        <div class="review-item">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div>
                                                    <strong>Jane Smith</strong>
                                                    <div class="review-stars">
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-solid fa-star"></i>
                                                        <i class="fa-regular fa-star"></i>
                                                    </div>
                                                </div>
                                                <span class="text-muted small">1 week ago</span>
                                            </div>
                                            <p>Great tee for the gym. Love the oversized fit!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </section>
    </main>

    <!-- Size Guide Modal -->
    <div class="quick-view-modal" id="sizeGuideModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
        <div class="modal-overlay" style="background-color: rgba(0,0,0,0.8); position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
        <div class="modal-content" style="max-width: 600px; width: 90%; background: #fff; padding: 30px; z-index: 10000; max-height: 90vh; overflow-y: auto; position: relative; border-radius: 8px;">
            <button class="close-modal" id="closeSizeGuideBtn" style="color: black; position: absolute; top: 15px; right: 20px; font-size: 28px; background: none; border: none; cursor: pointer; z-index: 10001; line-height: 1;">&times;</button>
            <div class="text-center mt-2">
                <h3 class="fw-bold text-uppercase mb-4">Size Guide</h3>
                <img src="./assets/images/products/size chart.jpg" alt="Size Chart" class="img-fluid" style="width: 100%; height: auto; display: block;">
            </div>
        </div>
    </div>

    <?php 
    $border_top = true;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <?php if ($current_product): ?>
    <script id="product-page-data" type="application/json"><?php
        echo json_encode([
            'id' => $current_product['id'],
            'name' => $current_product['name'],
            'price' => $current_product['price'],
            'image' => $current_product['image'],
            'description' => $current_product['description'],
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    ?></script>
    <?php endif; ?>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/product.js"></script>
</body>
</html>

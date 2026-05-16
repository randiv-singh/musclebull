<?php
require_once 'classes/Product.php';

// Initialize Product
$product = new Product();

// Get products data
$products = $product->getAll();
$best_sellers = $product->getBestSellers();
$featured_products = $product->getFeatured();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Muscle Bull - Premium Fitness Apparel</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="./assets/css/shop.css" rel="stylesheet" />
    <link href="./assets/css/product-card.css" rel="stylesheet" />
    <link href="./assets/css/checkout.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

    <!-- Header -->
    <?php include 'components/header.php'; ?>
    <!-- Header -->

    <main>
        <!-- Hero Section -->
        <section id="home" class="hero-section d-flex align-items-center">
            <div class="hero-overlay"></div>
            <div class="container position-relative z-2">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-6 text-start">
                        <div class="hero-badge mb-4">
                            <span class="badge bg-primary px-3 py-2 text-uppercase fw-bold">✨ New Drop</span>
                        </div>
                        <h1 class="hero-title-new text-white mb-4">
                            UNLEASH<br>
                            THE <span class="text-primary ">BEAST</span>
                        </h1>
                        <p class="hero-text text-white mb-5">
                            Premium fitness gear for those who refuse to settle.<br>
                            No excuses. Just results.
                        </p>
                        <div class="d-flex gap-3 flex-column flex-sm-row">
                            <a href="shop.php" class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold">
                                Shop Now <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Best Sellers Section -->
        <section id="best-sellers" class="py-5 bg-white">
            <div class="container py-4">
                <div class="products-section-header">
                    <h2 class="products-section-header__title">Best <span>Sellers</span></h2>
                    <a href="shop.php" class="products-section-header__link">
                        View All <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="row g-4 products-showcase" id="best-sellers-container">
                    <?php 
                    $count = 0;
                    foreach ($best_sellers as $product) {
                        if ($count >= 3) break;
                        include 'components/product-card.php';
                        $count++;
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section id="featured" class="py-5 bg-white">
            <div class="container py-4">
                <div class="products-section-header">
                    <h2 class="products-section-header__title">Featured <span>Drops</span></h2>
                    <a href="shop.php" class="products-section-header__link">
                        View All <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="row g-4 products-showcase" id="featured-container">
                    <?php 
                    $count = 0;
                    foreach ($featured_products as $product) {
                        if ($count >= 3) break;
                        include 'components/product-card.php';
                        $count++;
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Limited Edition Section -->
        <section id="limited" class="bg-white text-dark">
            <div class="container-fluid p-0">
                <div class="row g-0 align-items-center">
                    <div class="col-12 col-lg-6">
                        <img src="./assets/images/flayer/limited-edition.jpg" alt="Limited Edition Hoodies" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 500px;">
                    </div>
                    <div class="col-12 col-lg-6 p-5 p-xl-5">
                        <div class="px-xl-5">
                            <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase tracking-wide">Exclusive</span>
                            <h2 class="display-4 fw-bold text-uppercase mb-4 text-dark">Limited Edition Hoodies</h2>
                            <p class="lead mb-4 text-dark">Premium hoodies designed to bring out the bull in you. Grab yours before they are gone forever. Crafted with high-quality materials for maximum comfort and durability.</p>
                            <a href="shop.php" class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold rounded-0 border-0">Shop Limited Edition</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gift Card Section -->
        <section class="bg-white">
            <div class="container-fluid p-0">
                <div class="row g-0 align-items-center flex-column-reverse flex-lg-row">
                    <div class="col-12 col-lg-6 p-5 p-xl-5">
                        <div class="px-xl-5">
                            <h2 class="display-4 fw-bold text-uppercase mb-4 text-black">Musclebull E-Gift Card</h2>
                            <p class="lead mb-4 text-black">Surprise your beloved ones with the perfect gift. Let them choose their favorite gear to crush their fitness goals.</p>
                            <a href="gift-cards.php" class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold rounded-0">Buy Gift Card</a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <img src="./assets/images/flayer/gift-card.jpg" alt="Musclebull Gift Card" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 500px;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-5 bg-white">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8 text-center mb-5">
                        <h2 class="fw-bold text-uppercase text-black">Get In Touch</h2>
                        <p class="text-black">Have a question or need help with an order? Drop us a message.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <form class="bg-white p-4 p-md-5 border border-dark">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold text-black">Name</label>
                                    <input type="text" class="form-control rounded-0 py-2 border-dark text-black" id="name" placeholder="Your Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold text-black">Email</label>
                                    <input type="email" class="form-control rounded-0 py-2 border-dark text-black" id="email" placeholder="Your Email">
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label fw-bold text-black">Subject</label>
                                    <input type="text" class="form-control rounded-0 py-2 border-dark text-black" id="subject" placeholder="Subject">
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-bold text-black">Message</label>
                                    <textarea class="form-control rounded-0 py-2 border-dark text-black" id="message" rows="5" placeholder="Your Message"></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-dark btn-lg w-100 rounded-0 text-uppercase fw-bold">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section bg-white py-5 ">
            <div class="container">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-truck-fast fa-3x"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2">Shipping</h6>
                            <p class="small mb-0 text-black">Standard shipping (Estimated 3-5 days)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-shield-halved fa-3x"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2">Payments</h6>
                            <p class="small mb-0 text-black">Payment is 100% secure</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-rotate-left fa-3x"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2">Easy Returns</h6>
                            <p class="small mb-0 text-black">30 days to change your mind!</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-leaf fa-3x"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2">Made in Sri Lanka</h6>
                            <p class="small mb-0 text-black">Sustainably Sourced</p>
                        </div>
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
    $type = 'transparent';
    $border_top = false;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/product.js"></script>
</body>
</html>

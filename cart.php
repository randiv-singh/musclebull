<?php
require_once 'components/header.php';
require_once 'components/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./css/style.css" rel="stylesheet" />
    <link href="./css/header.css" rel="stylesheet" />
    <link href="./css/footer.css" rel="stylesheet" />
    <link href="./css/cart.css" rel="stylesheet" />
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
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Shopping Cart</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Cart Section -->
        <section class="cart-section py-5 bg-white">
            <div class="container">
                <h1 class="fw-bold text-uppercase mb-5">Shopping Cart</h1>
                
                <div class="row g-4">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div id="cart-items-container">
                            <!-- Cart items will be loaded here dynamically -->
                        </div>

                        <!-- Continue Shopping Button -->
                        <div class="mt-4">
                            <a href="shop.php" class="btn btn-outline-dark px-5 py-3 text-uppercase fw-bold">
                                <i class="fa-solid fa-arrow-left me-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h4 class="fw-bold text-uppercase mb-4">Order Summary</h4>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span class="fw-bold">LKR 13,500</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="fw-bold">LKR 500</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span class="fw-bold">LKR 0</span>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="summary-row summary-total">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-4">LKR 14,000</span>
                            </div>

                            <!-- Promo Code -->
                            <div class="promo-code mt-4">
                                <input type="text" class="form-control promo-input" placeholder="Enter promo code">
                                <button class="btn btn-dark promo-btn text-uppercase fw-bold">Apply</button>
                            </div>

                            <!-- Checkout Button -->
                            <a href="checkout.php" class="btn btn-primary w-100 py-3 text-uppercase fw-bold mt-4">
                                Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>

                            <!-- Payment Methods -->
                            <div class="payment-methods mt-4 text-center">
                                <p class="small text-black mb-2 fw-medium">We Accept</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <i class="fa-brands fa-cc-visa fa-2x"></i>
                                    <i class="fa-brands fa-cc-mastercard fa-2x"></i>
                                    <i class="fa-brands fa-cc-amex fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php 
    $border_top = true;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./js/bootstrap.bundle.min.js"></script>
    <script src="./js/app.js"></script>
    <script src="./js/cart.js"></script>
</body>
</html>

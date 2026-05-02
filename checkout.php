
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="./assets/css/checkout.css" rel="stylesheet" />
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
        <!-- Checkout Section -->
        <section class="checkout-section py-5 bg-white">
            <div class="container">
                <!-- Progress Steps -->
                <div class="checkout-steps mb-5">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <span class="step-label">Shipping</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <span class="step-label">Payment</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <span class="step-label">Review</span>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Checkout Form -->
                    <div class="col-lg-8">
                        <div class="checkout-form">
                            <!-- Shipping Information -->
                            <div class="form-section">
                                <h4 class="fw-bold text-uppercase mb-4">Shipping Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">First Name</label>
                                        <input type="text" class="form-control checkout-input" placeholder="John">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Last Name</label>
                                        <input type="text" class="form-control checkout-input" placeholder="Doe">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" class="form-control checkout-input" placeholder="john.doe@example.com">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <input type="tel" class="form-control checkout-input" placeholder="+94 77 123 4567">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Street Address</label>
                                        <input type="text" class="form-control checkout-input" placeholder="123 Main Street">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">City</label>
                                        <input type="text" class="form-control checkout-input" placeholder="Colombo">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Postal Code</label>
                                        <input type="text" class="form-control checkout-input" placeholder="10100">
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Method -->
                            <div class="form-section mt-4">
                                <h4 class="fw-bold text-uppercase mb-4">Shipping Method</h4>
                                <div class="shipping-options">
                                    <div class="shipping-option active">
                                        <input type="radio" name="shipping" id="standard" checked>
                                        <label for="standard">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Standard Delivery</strong>
                                                    <p class="mb-0 small">3-5 Business Days</p>
                                                </div>
                                                <strong>LKR 500</strong>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="shipping-option">
                                        <input type="radio" name="shipping" id="express">
                                        <label for="express">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Express Delivery</strong>
                                                    <p class="mb-0 small">1-2 Business Days</p>
                                                </div>
                                                <strong>LKR 1,000</strong>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="form-section mt-4">
                                <h4 class="fw-bold text-uppercase mb-4">Payment Method</h4>
                                <div class="payment-options">
                                    <div class="payment-option active">
                                        <input type="radio" name="payment" id="card" checked>
                                        <label for="card">
                                            <i class="fa-solid fa-credit-card me-2"></i>
                                            Credit / Debit Card
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" name="payment" id="cod">
                                        <label for="cod">
                                            <i class="fa-solid fa-money-bill me-2"></i>
                                            Cash on Delivery
                                        </label>
                                    </div>
                                </div>

                                <!-- Card Details -->
                                <div class="card-details mt-4">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Card Number</label>
                                            <input type="text" class="form-control checkout-input" placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Expiry Date</label>
                                            <input type="text" class="form-control checkout-input" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">CVV</label>
                                            <input type="text" class="form-control checkout-input" placeholder="123">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary-checkout">
                            <h4 class="fw-bold text-uppercase mb-4">Order Summary</h4>
                            
                            <!-- Order Items -->
                            <div class="order-items mb-4">
                                <div class="order-item">
                                    <img src="./assets/images/products/black oversize 1.jpg" alt="Product">
                                    <div class="order-item-info">
                                        <h6 class="fw-bold mb-1">Black Oversize Tee</h6>
                                        <p class="small mb-0">Size: L × 1</p>
                                    </div>
                                    <span class="fw-bold">LKR 3,500</span>
                                </div>
                                <div class="order-item">
                                    <img src="./assets/images/products/white hoodie 1.jpg" alt="Product">
                                    <div class="order-item-info">
                                        <h6 class="fw-bold mb-1">White Hoodie</h6>
                                        <p class="small mb-0">Size: M × 1</p>
                                    </div>
                                    <span class="fw-bold">LKR 5,200</span>
                                </div>
                                <div class="order-item">
                                    <img src="./assets/images/products/blue skinny 2.jpg" alt="Product">
                                    <div class="order-item-info">
                                        <h6 class="fw-bold mb-1">Blue Skinny Pants</h6>
                                        <p class="small mb-0">Size: 32 × 1</p>
                                    </div>
                                    <span class="fw-bold">LKR 4,800</span>
                                </div>
                            </div>
                            
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

                            <!-- Place Order Button -->
                            <button class="btn btn-primary w-100 py-3 text-uppercase fw-bold mt-4">
                                Place Order <i class="fa-solid fa-check ms-2"></i>
                            </button>

                            <!-- Security Badge -->
                            <div class="security-badge mt-4 text-center">
                                <i class="fa-solid fa-lock me-2"></i>
                                <span class="small">Secure Checkout</span>
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
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/cart.js"></script>
</body>
</html>

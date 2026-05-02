<?php
session_start();
require_once 'classes/Cart.php';
require_once 'classes/Order.php';

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();
$cart = new Cart($userId, $sessionId);
$order = new Order();

$error = '';
$success = '';

// Get cart data first (needed for both display and order processing)
$cartItems = $cart->getItemsArray();
$subtotal = $cart->getTotal();

// Redirect if cart is empty
if ($cart->getItemCount() === 0) {
    header('Location: cart.php');
    exit;
}

// Process order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postalCode = $_POST['postalCode'] ?? '';
    $shippingMethod = $_POST['shipping'] ?? 'standard';
    $paymentMethod = $_POST['payment'] ?? 'cod';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
        empty($address) || empty($city) || empty($postalCode)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Calculate totals with selected shipping
        $shippingCost = $shippingMethod === 'express' ? 1000 : ($subtotal > 5000 ? 0 : 500);
        $totalAmount = $subtotal + $shippingCost;
        
        // Prepare shipping address
        $shippingAddress = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postalCode,
            'shipping_method' => $shippingMethod,
            'shipping_cost' => $shippingCost
        ];
        
        // Create order
        $orderId = $order->add(
            $userId,  // null if guest
            $cartItems,
            $totalAmount,
            $shippingAddress,
            $paymentMethod
        );
        
        if ($orderId) {
            // Clear cart
            $cart->clearCart();
            
            // Store order info in session for confirmation page
            $_SESSION['last_order_id'] = $orderId;
            
            $success = 'Order placed successfully! Your order number is #' . $orderId;
            
            // Redirect to order confirmation after 2 seconds
            header('Refresh: 2; URL=order-confirmation.php?order_id=' . $orderId);
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}

// Calculate shipping and total for display
$shippingCost = $subtotal > 5000 ? 0 : 500;
$totalAmount = $subtotal + $shippingCost;
?>
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
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-0 mb-4" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success rounded-0 mb-4" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
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
                        <form method="POST" action="">
                        <div class="checkout-form">
                            <!-- Shipping Information -->
                            <div class="form-section">
                                <h4 class="fw-bold text-uppercase mb-4">Shipping Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">First Name</label>
                                        <input type="text" name="firstName" class="form-control checkout-input" placeholder="John" required
                                            value="<?php echo htmlspecialchars($_SESSION['user_name'] ? explode(' ', $_SESSION['user_name'])[0] : ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Last Name</label>
                                        <input type="text" name="lastName" class="form-control checkout-input" placeholder="Doe" required
                                            value="<?php echo htmlspecialchars($_SESSION['user_name'] ? explode(' ', $_SESSION['user_name'])[1] ?? '' : ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" name="email" class="form-control checkout-input" placeholder="john.doe@example.com" required
                                            value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control checkout-input" placeholder="+94 77 123 4567" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Street Address</label>
                                        <input type="text" name="address" class="form-control checkout-input" placeholder="123 Main Street" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">City</label>
                                        <input type="text" name="city" class="form-control checkout-input" placeholder="Colombo" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Postal Code</label>
                                        <input type="text" name="postalCode" class="form-control checkout-input" placeholder="10100" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Method -->
                            <div class="form-section mt-4">
                                <h4 class="fw-bold text-uppercase mb-4">Shipping Method</h4>
                                <div class="shipping-options">
                                    <div class="shipping-option active">
                                        <input type="radio" name="shipping" id="standard" value="standard" checked>
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
                                        <input type="radio" name="shipping" id="express" value="express">
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
                                    <div class="payment-option">
                                        <input type="radio" name="payment" id="card" value="card">
                                        <label for="card">
                                            <i class="fa-solid fa-credit-card me-2"></i>
                                            Credit / Debit Card
                                        </label>
                                    </div>
                                    <div class="payment-option active">
                                        <input type="radio" name="payment" id="cod" value="cod" checked>
                                        <label for="cod">
                                            <i class="fa-solid fa-money-bill me-2"></i>
                                            Cash on Delivery
                                        </label>
                                    </div>
                                </div>

                                <!-- Card Details (hidden by default, shown when card payment selected) -->
                                <div class="card-details mt-4" id="cardDetails" style="display: none;">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Card Number</label>
                                            <input type="text" name="cardNumber" class="form-control checkout-input" placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Expiry Date</label>
                                            <input type="text" name="cardExpiry" class="form-control checkout-input" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">CVV</label>
                                            <input type="text" name="cardCvv" class="form-control checkout-input" placeholder="123">
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
                                <?php foreach ($cartItems as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="order-item-info">
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <p class="small mb-0">Size: <?php echo htmlspecialchars($item['size']); ?> × <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <span class="fw-bold">LKR <?php echo number_format($item['price'] * $item['quantity']); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span class="fw-bold">LKR <?php echo number_format($subtotal); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="fw-bold" id="shippingCost">LKR <?php echo number_format($shippingCost); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span class="fw-bold">LKR 0</span>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="summary-row summary-total">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-4" id="totalAmount">LKR <?php echo number_format($totalAmount); ?></span>
                            </div>

                            <!-- Place Order Button -->
                            <button type="submit" name="place_order" class="btn btn-primary w-100 py-3 text-uppercase fw-bold mt-4">
                                Place Order <i class="fa-solid fa-check ms-2"></i>
                            </button>

                            <!-- Security Badge -->
                            <div class="security-badge mt-4 text-center">
                                <i class="fa-solid fa-lock me-2"></i>
                                <span class="small">Secure Checkout</span>
                            </div>
                        </div>
                    </div>
                    </form>
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
    <script>
        // Handle shipping method change
        document.querySelectorAll('input[name="shipping"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const isExpress = this.value === 'express';
                const subtotal = <?php echo $subtotal; ?>;
                let shippingCost;
                let shippingText;
                
                if (isExpress) {
                    shippingCost = 1000;
                    shippingText = 'LKR 1,000';
                } else if (subtotal > 5000) {
                    shippingCost = 0;
                    shippingText = 'Free';
                } else {
                    shippingCost = 500;
                    shippingText = 'LKR 500';
                }
                
                const total = subtotal + shippingCost;
                
                document.getElementById('shippingCost').textContent = shippingText;
                document.getElementById('totalAmount').textContent = 'LKR ' + total.toLocaleString();
            });
        });
        
        // Handle payment method change
        document.querySelectorAll('input[name="payment"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cardDetails = document.getElementById('cardDetails');
                if (this.value === 'card') {
                    cardDetails.style.display = 'block';
                } else {
                    cardDetails.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

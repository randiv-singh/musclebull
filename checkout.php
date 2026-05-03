<?php
session_start();
require_once 'classes/Order.php';

$order = new Order();
$userId = $_SESSION['user_id'] ?? null;

$error = '';
$success = '';

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
    $cartItems = json_decode($_POST['cart_items'] ?? '[]', true);
    
    // Calculate totals
    $subtotal = array_reduce($cartItems, function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);
    $shippingCost = $shippingMethod === 'express' ? 1000 : ($subtotal > 5000 ? 0 : 500);
    $totalAmount = $subtotal + $shippingCost;
    
    // Validation
    if (empty($cartItems)) {
        $error = 'Your cart is empty';
    } elseif (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
        empty($address) || empty($city) || empty($postalCode)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
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
            $userId,
            $cartItems,
            $totalAmount,
            $shippingAddress,
            $paymentMethod
        );
        
        if ($orderId) {
            $_SESSION['last_order_id'] = $orderId;
            $success = 'Order placed successfully! Your order number is #' . $orderId;
            header('Refresh: 2; URL=order-confirmation.php?order_id=' . $orderId);
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}
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
    <style>
        .order-summary-checkout {
            position: sticky;
            top: 20px;
            z-index: 100;
        }
        @media (max-width: 991px) {
            .order-summary-checkout {
                position: static;
            }
        }
    </style>
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
                    <div class="step active" id="step-shipping">
                        <div class="step-number">1</div>
                        <span class="step-label">Shipping</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step" id="step-review">
                        <div class="step-number">2</div>
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
                                            value="<?php 
                                                $userName = $_SESSION['user_name'] ?? '';
                                                $nameParts = $userName ? explode(' ', $userName) : [];
                                                echo htmlspecialchars($nameParts[0] ?? '');
                                            ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Last Name</label>
                                        <input type="text" name="lastName" class="form-control checkout-input" placeholder="Doe" required
                                            value="<?php 
                                                echo htmlspecialchars($nameParts[1] ?? '');
                                            ?>">
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

                            <!-- Review Order Button -->
                            <div class="form-section mt-4">
                                <button type="button" id="review-order-btn" class="btn btn-dark w-100 py-3 text-uppercase fw-bold" onclick="showReviewSection()">
                                    Review Order <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>

                            <!-- Review Section (Hidden initially) -->
                            <div id="review-section" style="display: none;">
                                <div class="form-section mt-4">
                                    <h4 class="fw-bold text-uppercase mb-4">Review Your Order</h4>
                                    
                                    <!-- Shipping Info Review -->
                                    <div class="review-block mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Shipping Information</h6>
                                            <a href="#" onclick="showShippingSection(); return false;" class="small text-decoration-underline">Edit</a>
                                        </div>
                                        <div class="p-3 bg-light" id="review-shipping-info">
                                            <!-- Filled by JS -->
                                        </div>
                                    </div>
                                    
                                    <!-- Shipping Method Review -->
                                    <div class="review-block mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0">Shipping Method</h6>
                                            <a href="#" onclick="showShippingSection(); return false;" class="small text-decoration-underline">Edit</a>
                                        </div>
                                        <div class="p-3 bg-light" id="review-shipping-method">
                                            <!-- Filled by JS -->
                                        </div>
                                    </div>
                                    
                                    <!-- Order Items Review -->
                                    <div class="review-block">
                                        <h6 class="fw-bold mb-2">Order Items</h6>
                                        <div id="review-order-items" class="p-3 bg-light">
                                            <!-- Filled by JS -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Place Order Button -->
                                <button type="submit" name="place_order" class="btn btn-primary w-100 py-3 text-uppercase fw-bold">
                                    Place Order <i class="fa-solid fa-check ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary-checkout">
                            <h4 class="fw-bold text-uppercase mb-4">Order Summary</h4>
                            
                            <!-- Order Items -->
                            <div class="order-items mb-4" id="order-items-container">
                                <!-- Items loaded via JavaScript -->
                            </div>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span class="fw-bold" id="subtotal-display">LKR 0</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="fw-bold" id="shippingCost">LKR 0</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span class="fw-bold">LKR 0</span>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="summary-row summary-total">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold fs-4" id="totalAmount">LKR 0</span>
                            </div>
                            
                            <!-- Hidden cart data field -->
                            <input type="hidden" name="cart_items" id="cart-items-input">
                            
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
    <script>
        let currentCart = [];
        let currentSubtotal = 0;

        // Load cart from localStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!$success): ?>
            // Only check for empty cart if order wasn't just placed
            currentCart = JSON.parse(localStorage.getItem('cart')) || [];
            
            if (currentCart.length === 0) {
                alert('Your cart is empty. Please add items before checkout.');
                window.location.href = 'cart.php';
                return;
            }
            
            // Fill hidden cart field for form submission
            document.getElementById('cart-items-input').value = JSON.stringify(currentCart);
            
            // Render order items
            renderOrderItems();
            
            // Calculate and display totals
            updateTotals();
            <?php endif; ?>
        });

        function renderOrderItems() {
            const container = document.getElementById('order-items-container');
            container.innerHTML = currentCart.map(item => `
                <div class="order-item">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="order-item-info">
                        <h6 class="fw-bold mb-1">${item.name}</h6>
                        <p class="small mb-0">Size: ${item.size} × ${item.quantity}</p>
                    </div>
                    <span class="fw-bold">LKR ${(item.price * item.quantity).toLocaleString()}</span>
                </div>
            `).join('');
        }

        function updateTotals() {
            currentSubtotal = currentCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shippingMethod = document.querySelector('input[name="shipping"]:checked')?.value || 'standard';
            const shippingCost = shippingMethod === 'express' ? 1000 : (currentSubtotal > 5000 ? 0 : 500);
            const total = currentSubtotal + shippingCost;
            
            document.getElementById('subtotal-display').textContent = 'LKR ' + currentSubtotal.toLocaleString();
            document.getElementById('shippingCost').textContent = shippingCost === 0 ? 'Free' : 'LKR ' + shippingCost.toLocaleString();
            document.getElementById('totalAmount').textContent = 'LKR ' + total.toLocaleString();
        }

        // Show Review Section
        function showReviewSection() {
            // Validate form first
            const form = document.querySelector('form[method="POST"]');
            if (!form) {
                console.error('Form not found');
                return;
            }
            const requiredFields = form.querySelectorAll('input[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Update progress steps
            document.getElementById('step-shipping').classList.remove('active');
            document.getElementById('step-review').classList.add('active');
            
            // Show review section
            document.getElementById('review-section').style.display = 'block';
            document.getElementById('review-order-btn').style.display = 'none';
            
            // Fill review data
            fillReviewData();
            
            // Scroll to review section
            document.getElementById('review-section').scrollIntoView({ behavior: 'smooth' });
        }

        // Show Shipping Section (from review)
        function showShippingSection() {
            // Update progress steps
            document.getElementById('step-shipping').classList.add('active');
            document.getElementById('step-review').classList.remove('active');
            
            // Hide review section
            document.getElementById('review-section').style.display = 'none';
            document.getElementById('review-order-btn').style.display = 'block';
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Fill review data
        function fillReviewData() {
            // Shipping info
            const firstName = document.querySelector('input[name="firstName"]').value;
            const lastName = document.querySelector('input[name="lastName"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const address = document.querySelector('input[name="address"]').value;
            const city = document.querySelector('input[name="city"]').value;
            const postalCode = document.querySelector('input[name="postalCode"]').value;
            
            document.getElementById('review-shipping-info').innerHTML = `
                <p class="mb-1"><strong>${firstName} ${lastName}</strong></p>
                <p class="mb-1">${address}</p>
                <p class="mb-1">${city}, ${postalCode}</p>
                <p class="mb-1">${email}</p>
                <p class="mb-0">${phone}</p>
            `;
            
            // Shipping method
            const shippingMethod = document.querySelector('input[name="shipping"]:checked').value;
            const shippingCost = shippingMethod === 'express' ? 1000 : (currentSubtotal > 5000 ? 0 : 500);
            const shippingText = shippingMethod === 'express' ? 'Express Delivery (1-2 days)' : 'Standard Delivery (3-5 days)';
            const shippingPriceText = shippingCost === 0 ? 'Free' : 'LKR ' + shippingCost.toLocaleString();
            
            document.getElementById('review-shipping-method').innerHTML = `
                <p class="mb-1"><strong>${shippingText}</strong></p>
                <p class="mb-0">${shippingPriceText}</p>
            `;
            
            // Order items
            document.getElementById('review-order-items').innerHTML = currentCart.map(item => `
                <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                    <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-bold">${item.name}</p>
                        <p class="mb-0 small">Size: ${item.size} × ${item.quantity}</p>
                    </div>
                    <span class="fw-bold">LKR ${(item.price * item.quantity).toLocaleString()}</span>
                </div>
            `).join('');
        }

        // Handle shipping method change
        document.querySelectorAll('input[name="shipping"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Update active class
                document.querySelectorAll('.shipping-option').forEach(opt => opt.classList.remove('active'));
                this.closest('.shipping-option').classList.add('active');
                updateTotals();
            });
        });

        // Clear cart after successful order
        <?php if ($success): ?>
            localStorage.removeItem('cart');
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
        <?php endif; ?>
    </script>
</body>
</html>

// Cart.js - LocalStorage only, no server sync
document.addEventListener('DOMContentLoaded', function() {
    // Handle Cart Page
    if (document.querySelector('.cart-section')) {
        renderCart();
    }

    // Handle Checkout Page
    if (document.querySelector('.checkout-section')) {
        renderCheckout();
    }

    function renderCart() {
        const cart = getCart();
        const itemsContainer = document.getElementById('cart-items-container');
        
        if (!itemsContainer) return;
        
        const checkoutBtn = document.getElementById('checkout-btn');
        const emptyBtn = document.getElementById('empty-cart-btn');
        const continueShopping = document.getElementById('continue-shopping-container');
        
        if (cart.length === 0) {
            itemsContainer.innerHTML = `
                <div class="text-center py-5">
                    <h3>Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary px-4 py-2 text-uppercase fw-bold">Shop Now</a>
                </div>
            `;
            updateSummary(0, '.order-summary');
            
            if (checkoutBtn) checkoutBtn.classList.add('d-none');
            if (emptyBtn) emptyBtn.classList.remove('d-none');
            if (continueShopping) continueShopping.classList.remove('d-none');
            return;
        }
        
        if (checkoutBtn) checkoutBtn.classList.remove('d-none');
        if (emptyBtn) emptyBtn.classList.add('d-none');
        if (continueShopping) continueShopping.classList.add('d-none');

        itemsContainer.innerHTML = cart.map((item, index) => `
            <div class="cart-item" data-index="${index}">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    </div>
                    <div class="col-md-4">
                        <h5 class="cart-item-name fw-bold text-uppercase mb-2">${item.name}</h5>
                        <p class="cart-item-details mb-2">Size: ${item.size}</p>
                    </div>
                    <div class="col-md-2">
                        <div class="quantity-selector">
                            <button class="qty-btn minus" data-id="${item.id}" data-size="${item.size}">-</button>
                            <input type="number" value="${item.quantity}" min="1" class="qty-input" data-id="${item.id}" data-size="${item.size}" readonly>
                            <button class="qty-btn plus" data-id="${item.id}" data-size="${item.size}">+</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <p class="cart-item-price fw-bold mb-0">${formatPrice(item.price * item.quantity)}</p>
                    </div>
                    <div class="col-md-1 text-end">
                        <button class="btn-remove" data-id="${item.id}" data-size="${item.size}"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `).join('');

        attachCartListeners();
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateSummary(subtotal, '.order-summary');
    }

    function renderCheckout() {
        const cart = getCart();
        const orderItemsContainer = document.querySelector('.order-items');
        
        if (!orderItemsContainer) return;

        if (cart.length === 0) {
            orderItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
            updateSummary(0, '.order-summary-checkout');
            return;
        }

        orderItemsContainer.innerHTML = cart.map(item => `
            <div class="order-item">
                <img src="${item.image}" alt="${item.name}">
                <div class="order-item-info">
                    <h6 class="fw-bold mb-1">${item.name}</h6>
                    <p class="small mb-0">Size: ${item.size} × ${item.quantity}</p>
                </div>
                <span class="fw-bold">${formatPrice(item.price * item.quantity)}</span>
            </div>
        `).join('');

        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateSummary(subtotal, '.order-summary-checkout');
    }

    function attachCartListeners() {
        document.querySelectorAll('.qty-btn.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const size = this.getAttribute('data-size');
                updateQuantity(id, size, -1);
            });
        });

        document.querySelectorAll('.qty-btn.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const size = this.getAttribute('data-size');
                updateQuantity(id, size, 1);
            });
        });

        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const size = this.getAttribute('data-size');
                removeFromCart(id, size);
            });
        });
    }

    function updateQuantity(id, size, change) {
        const cart = getCart();
        const item = cart.find(i => i.id === id && i.size === size);
        
        if (item) {
            const newQuantity = item.quantity + change;
            if (newQuantity <= 0) {
                removeFromCart(id, size);
            } else {
                item.quantity = newQuantity;
                saveCart(cart);
                renderCart();
                updateCartBadge();
            }
        }
    }

    function removeFromCart(id, size) {
        let cart = getCart();
        cart = cart.filter(i => !(i.id === id && i.size === size));
        saveCart(cart);
        renderCart();
        updateCartBadge();
    }

    function updateSummary(subtotal, containerSelector) {
        const shipping = subtotal > 0 ? (subtotal > 5000 ? 0 : 500) : 0;
        const total = subtotal + shipping;

        const container = document.querySelector(containerSelector);
        if (!container) return;

        const summaryRows = container.querySelectorAll('.summary-row span.fw-bold');
        if (summaryRows.length >= 3) {
            summaryRows[0].textContent = formatPrice(subtotal);
            summaryRows[1].textContent = shipping === 0 ? 'Free' : formatPrice(shipping);
            summaryRows[2].textContent = formatPrice(0);
        }

        const totalEl = container.querySelector('.summary-total span.fs-4');
        if (totalEl) {
            totalEl.textContent = formatPrice(total);
        }
    }
});

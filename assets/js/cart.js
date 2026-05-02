document.addEventListener('DOMContentLoaded', function() {
    const API_URL = 'api/cart.php';

    // Load cart from server on page load
    loadCartFromServer();

    // Handle Cart Page
    if (document.querySelector('.cart-section')) {
        const itemsContainer = document.getElementById('cart-items-container');
        if (itemsContainer) {
            renderCart();
        }
    }

    // Handle Checkout Page
    if (document.querySelector('.checkout-section')) {
        renderCheckout();
    }

    // Load cart from server
    async function loadCartFromServer() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (data.success) {
                // Merge server cart with local cart (server takes priority)
                const localCart = getCart();
                const serverCart = data.items || [];
                
                // If server has items, use them; otherwise sync local to server
                if (serverCart.length > 0) {
                    saveCart(serverCart);
                } else if (localCart.length > 0) {
                    // Sync local cart to server
                    await syncCartToServer(localCart);
                }
                
                updateCartBadge();
                
                // Re-render if on cart/checkout page
                if (document.querySelector('.cart-section')) {
                    renderCart();
                }
                if (document.querySelector('.checkout-section')) {
                    renderCheckout();
                }
            }
        } catch (error) {
            console.error('Error loading cart from server:', error);
        }
    }

    // Sync cart to server
    async function syncCartToServer(cart) {
        try {
            await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'sync', items: cart })
            });
        } catch (error) {
            console.error('Error syncing cart to server:', error);
        }
    }

    // Add item to server cart
    async function addItemToServer(item) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'add',
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    image: item.image,
                    size: item.size,
                    quantity: item.quantity
                })
            });
            const data = await response.json();
            if (data.success) {
                saveCart(data.items);
                updateCartBadge();
            }
            return data;
        } catch (error) {
            console.error('Error adding item to server cart:', error);
            return null;
        }
    }

    // Update item quantity on server
    async function updateItemOnServer(id, size, quantity) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update',
                    id: id,
                    size: size,
                    quantity: quantity
                })
            });
            const data = await response.json();
            if (data.success) {
                saveCart(data.items);
                updateCartBadge();
            }
            return data;
        } catch (error) {
            console.error('Error updating item on server:', error);
            return null;
        }
    }

    // Remove item from server cart
    async function removeItemFromServer(id, size) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'remove',
                    id: id,
                    size: size
                })
            });
            const data = await response.json();
            if (data.success) {
                saveCart(data.items);
                updateCartBadge();
            }
            return data;
        } catch (error) {
            console.error('Error removing item from server cart:', error);
            return null;
        }
    }

    function renderCart() {
        const cart = getCart();
        const itemsContainer = document.getElementById('cart-items-container');
        
        if (!itemsContainer) return;
        
        if (cart.length === 0) {
            itemsContainer.innerHTML = `
                <div class="text-center py-5">
                    <h3>Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary px-4 py-2 text-uppercase fw-bold">Shop Now</a>
                </div>
            `;
            updateSummary(0, '.order-summary');
            return;
        }

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
        
        // Calculate total
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
        // Minus buttons
        document.querySelectorAll('.qty-btn.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const size = this.getAttribute('data-size');
                updateQuantity(id, size, -1);
            });
        });

        // Plus buttons
        document.querySelectorAll('.qty-btn.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const size = this.getAttribute('data-size');
                updateQuantity(id, size, 1);
            });
        });

        // Remove buttons
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
                updateItemOnServer(id, size, newQuantity);
                renderCart();
            }
        }
    }

    function removeFromCart(id, size) {
        let cart = getCart();
        cart = cart.filter(i => !(i.id === id && i.size === size));
        saveCart(cart);
        removeItemFromServer(id, size);
        renderCart();
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
            // Tax is 0
            summaryRows[2].textContent = formatPrice(0);
        }

        const totalEl = container.querySelector('.summary-total span.fs-4');
        if (totalEl) {
            totalEl.textContent = formatPrice(total);
        }
    }

    // Override global addToCart to sync with server
    const originalAddToCart = window.addToCart;
    window.addToCart = async function(product, quantity = 1, size = 'M') {
        const cart = getCart();
        const existingItem = cart.find(item => item.id === product.id && item.size === size);
        
        if (existingItem) {
            existingItem.quantity += quantity;
            saveCart(cart);
            await updateItemOnServer(product.id, size, existingItem.quantity);
        } else {
            const newItem = {
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                size: size,
                quantity: quantity
            };
            cart.push(newItem);
            saveCart(cart);
            await addItemToServer(newItem);
        }
        
        updateCartBadge();
        alert(`${product.name} added to cart!`);
    };

    // Initial render is handled by the if blocks at the top
});
// Cart Management
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge();
}

function addToCart(product, quantity = 1, size = 'M') {
    const cart = getCart();
    const existingItem = cart.find(item => item.id === product.id && item.size === size);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            size: size,
            quantity: quantity
        });
    }
    
    saveCart(cart);
    alert(`${product.name} added to cart!`);
}

function updateCartBadge() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const cartIcons = document.querySelectorAll('a[href="cart.html"]');
    cartIcons.forEach(icon => {
        let badge = icon.querySelector('.cart-badge');
        
        if (!badge && icon.querySelector('.fa-bag-shopping')) {
            icon.classList.add('position-relative');
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            badge.style.position = 'absolute';
            badge.style.top = '-8px';
            badge.style.right = '-8px';
            badge.style.backgroundColor = '#000';
            badge.style.color = '#fff';
            badge.style.borderRadius = '50%';
            badge.style.padding = '2px 6px';
            badge.style.fontSize = '10px';
            badge.style.fontWeight = 'bold';
            icon.appendChild(badge);
        }
        
        if (badge) {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
    });
}

// Fetch products from JSON file
async function fetchProducts() {
    try {
        const response = await fetch('./products.json');
        if (!response.ok) {
            throw new Error('Failed to fetch products');
        }
        return await response.json();
    } catch (error) {
        console.error('Error loading products:', error);
        return [];
    }
}

// Format price
function formatPrice(price) {
    return `LKR ${price.toLocaleString()}`;
}

// Generate product card HTML
function generateProductCard(product, colClass = 'col-12 col-md-6 col-lg-3') {
    return `
        <div class="${colClass}">
            <a href="product.html?id=${product.id}" class="product-link">
                <div class="product-card-shop">
                    <div class="product-image-wrapper">
                        <img src="${product.image}" alt="${product.name}" class="product-image">
                        <button class="quick-view-btn" data-id="${product.id}">Quick View</button>
                    </div>
                    <div class="product-info">
                        <h6 class="product-name fw-bold text-uppercase">${product.name}</h6>
                        <p class="product-price fw-bold">${formatPrice(product.price)}</p>
                        <button class="btn btn-primary w-100 text-uppercase fw-bold btn-sm add-to-cart-btn" data-id="${product.id}">Add to Cart</button>
                    </div>
                </div>
            </a>
        </div>
    `;
}

// Render products to a container
function renderProducts(products, containerId, colClass) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = products.map(product => generateProductCard(product, colClass)).join('');
    
    // Re-attach quick view event listeners after rendering
    attachQuickViewListeners(products);
    attachAddToCartListeners(products);
}

// Attach add to cart listeners for product cards
function attachAddToCartListeners(products) {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        // Remove old listeners by cloning
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = parseInt(this.getAttribute('data-id'));
            const product = products.find(p => p.id === productId);
            
            if (product) {
                addToCart(product, 1, 'M'); // Default size M for quick add
            }
        });
    });
}

// Attach quick view listeners
function attachQuickViewListeners(products) {
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    const quickViewModal = document.getElementById('quickViewModal');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = parseInt(this.getAttribute('data-id'));
            const product = products.find(p => p.id === productId);
            
            if (product) {
                document.getElementById('modalProductImage').src = product.image;
                document.getElementById('modalProductName').textContent = product.name;
                document.getElementById('modalProductPrice').textContent = formatPrice(product.price);
                
                // Update "View Full Details" link
                const viewDetailsBtn = quickViewModal.querySelector('a.btn-outline-dark');
                if (viewDetailsBtn) {
                    viewDetailsBtn.href = `product.html?id=${product.id}`;
                }
                
                // Attach add to cart logic for modal
                const modalAddToCartBtn = quickViewModal.querySelector('.btn-primary');
                if (modalAddToCartBtn) {
                    // Clone to remove old listeners
                    const newBtn = modalAddToCartBtn.cloneNode(true);
                    modalAddToCartBtn.parentNode.replaceChild(newBtn, modalAddToCartBtn);
                    
                    newBtn.addEventListener('click', function() {
                        const qtyInput = quickViewModal.querySelector('.modal-qty-input');
                        const quantity = qtyInput ? parseInt(qtyInput.value) : 1;
                        
                        const activeSizeBtn = quickViewModal.querySelector('.modal-size-btn.active');
                        const size = activeSizeBtn ? activeSizeBtn.textContent : 'M';
                        
                        addToCart(product, quantity, size);
                        
                        // Close modal after adding
                        quickViewModal.classList.remove('active');
                        document.body.style.overflow = 'auto';
                    });
                }
                
                quickViewModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
}

// Initialize pages
document.addEventListener('DOMContentLoaded', async () => {
    // Initialize cart badge
    updateCartBadge();
    
    const products = await fetchProducts();
    
    // Index Page
    if (document.getElementById('best-sellers-container')) {
        const bestSellers = products.filter(p => p.isBestSeller).slice(0, 4);
        renderProducts(bestSellers, 'best-sellers-container', 'col-12 col-md-6 col-lg-3');
    }
    
    if (document.getElementById('featured-container')) {
        const featured = products.filter(p => p.isFeatured).slice(0, 3);
        renderProducts(featured, 'featured-container', 'col-12 col-md-6 col-lg-4');
    }
    
    // Shop Page
    if (document.getElementById('all-products-container')) {
        renderProducts(products, 'all-products-container', 'col-6 col-md-4');
    }
    
    // Product Detail Page
    const urlParams = new URLSearchParams(window.location.search);
    let productId = urlParams.get('id');
    
    if (document.getElementById('product-detail-container')) {
        if (!productId) {
            productId = 1; // Default to first product if no ID is provided
        }
        
        const product = products.find(p => p.id === parseInt(productId));
        if (product) {
            renderProductDetails(product);
            
            // Render related products
            if (document.getElementById('related-products-container')) {
                const related = products.filter(p => p.id !== product.id).slice(0, 4);
                renderProducts(related, 'related-products-container', 'col-6 col-md-3');
            }
        } else {
            document.getElementById('product-detail-container').innerHTML = '<div class="col-12 text-center py-5"><h2>Product not found</h2><a href="shop.html" class="btn btn-primary mt-3">Back to Shop</a></div>';
        }
    }

    // Gift Card Page
    const addGiftCardBtn = document.getElementById('addGiftCardBtn');
    if (addGiftCardBtn) {
        addGiftCardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get selected amount
            const selectedAmountInput = document.querySelector('input[name="amount"]:checked');
            let amount = 2500; // default
            
            if (selectedAmountInput) {
                const label = document.querySelector(`label[for="${selectedAmountInput.id}"]`);
                if (label) {
                    // Extract number from "LKR 2,500"
                    const amountText = label.textContent.replace('LKR', '').replace(/,/g, '').trim();
                    amount = parseInt(amountText);
                }
            }
            
            // Get delivery method
            const selectedDeliveryInput = document.querySelector('input[name="delivery"]:checked');
            let deliveryMethod = 'Email';
            if (selectedDeliveryInput && selectedDeliveryInput.id === 'delFriend') {
                deliveryMethod = 'Friend';
            }
            
            const giftCardProduct = {
                id: `gift-card-${amount}`,
                name: `Muscle Bull E-Gift Card`,
                price: amount,
                image: './assets/images/flayer/gift-card.jpg'
            };
            
            addToCart(giftCardProduct, 1, `Digital (${deliveryMethod})`);
        });
    }
});

function renderProductDetails(product) {
    // Update Breadcrumb
    const breadcrumbActive = document.querySelector('.breadcrumb-item.active');
    if (breadcrumbActive) breadcrumbActive.textContent = product.name;
    
    // Update Page Title
    document.title = `${product.name} - Muscle Bull`;
    
    // Render Images
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) mainImage.src = product.image;
    
    const thumbnailsContainer = document.querySelector('.thumbnail-images');
    if (thumbnailsContainer && product.thumbnails) {
        thumbnailsContainer.innerHTML = product.thumbnails.map((thumb, index) => `
            <div class="thumbnail ${index === 0 ? 'active' : ''}">
                <img src="${thumb}" alt="${product.name} View ${index + 1}" class="img-fluid">
            </div>
        `).join('');
        
        // Re-attach thumbnail listeners
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                mainImage.src = this.querySelector('img').src;
            });
        });
    }
    
    // Render Info
    const titleEl = document.querySelector('.product-title');
    if (titleEl) titleEl.textContent = product.name;
    
    const priceEl = document.querySelector('.current-price');
    if (priceEl) priceEl.textContent = formatPrice(product.price);
    
    const descEl = document.querySelector('.product-description');
    if (descEl) descEl.textContent = product.description;
    
    // Attach add to cart logic for product detail page
    const detailAddToCartBtn = document.querySelector('.product-actions .btn-primary');
    if (detailAddToCartBtn) {
        // Clone to remove old listeners
        const newBtn = detailAddToCartBtn.cloneNode(true);
        detailAddToCartBtn.parentNode.replaceChild(newBtn, detailAddToCartBtn);
        
        newBtn.addEventListener('click', function() {
            const qtyInput = document.querySelector('.qty-input-single');
            const quantity = qtyInput ? parseInt(qtyInput.value) : 1;
            
            const activeSizeBtn = document.querySelector('.size-btn-single.active');
            const size = activeSizeBtn ? activeSizeBtn.textContent : 'M';
            
            addToCart(product, quantity, size);
        });
    }

    /* Size Guide Modal */
    const sizeGuideBtn = document.getElementById('sizeGuideBtn');
    const sizeGuideModal = document.getElementById('sizeGuideModal');
    const closeSizeGuideBtn = document.getElementById('closeSizeGuideBtn');

    if (sizeGuideBtn && sizeGuideModal) {
        // Remove old listeners by cloning
        const newSizeGuideBtn = sizeGuideBtn.cloneNode(true);
        sizeGuideBtn.parentNode.replaceChild(newSizeGuideBtn, sizeGuideBtn);

        newSizeGuideBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            sizeGuideModal.style.display = 'flex';
            sizeGuideModal.style.alignItems = 'center';
            sizeGuideModal.style.justifyContent = 'center';
            sizeGuideModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        function closeSizeGuide() {
            sizeGuideModal.style.display = 'none';
            sizeGuideModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Initialize as hidden
        sizeGuideModal.style.display = 'none';

        if (closeSizeGuideBtn) {
            // Clone to prevent multiple listeners
            const newCloseBtn = closeSizeGuideBtn.cloneNode(true);
            closeSizeGuideBtn.parentNode.replaceChild(newCloseBtn, closeSizeGuideBtn);
            newCloseBtn.addEventListener('click', closeSizeGuide);
        }

        const sizeGuideOverlay = sizeGuideModal.querySelector('.modal-overlay');
        if (sizeGuideOverlay) {
            // Clone to prevent multiple listeners
            const newOverlay = sizeGuideOverlay.cloneNode(true);
            sizeGuideOverlay.parentNode.replaceChild(newOverlay, sizeGuideOverlay);
            newOverlay.addEventListener('click', closeSizeGuide);
        }
    }
}

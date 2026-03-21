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
    const badges = document.querySelectorAll('.cart-badge');
    
    // If badges don't exist in the DOM, let's try to add them to the cart icon
    if (badges.length === 0) {
        const cartIcons = document.querySelectorAll('a[href="cart.html"]');
        cartIcons.forEach(icon => {
            if (!icon.querySelector('.cart-badge') && icon.querySelector('.fa-bag-shopping')) {
                icon.classList.add('position-relative');
                const badge = document.createElement('span');
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
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
                icon.appendChild(badge);
            }
        });
    } else {
        badges.forEach(badge => {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        });
    }
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
                
                quickViewModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
}

// Initialize pages
document.addEventListener('DOMContentLoaded', async () => {
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
}

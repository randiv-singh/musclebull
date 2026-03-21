// Product Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail Image Switcher
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainProductImage');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Change main image
            const imgSrc = this.querySelector('img').src;
            mainImage.src = imgSrc;
        });
    });

    // Size Selector
    const sizeButtons = document.querySelectorAll('.size-btn-single');
    
    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all size buttons
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
        });
    });

    // Quantity Controls
    const qtyInput = document.querySelector('.qty-input-single');
    const minusBtn = document.querySelector('.qty-btn-single.minus');
    const plusBtn = document.querySelector('.qty-btn-single.plus');

    if (minusBtn && plusBtn && qtyInput) {
        minusBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
            }
        });

        plusBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            qtyInput.value = currentValue + 1;
        });
    }

    // Quick View Modal Functionality
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    const quickViewModal = document.getElementById('quickViewModal');
    const closeModalBtn = document.querySelector('.close-modal');
    const modalOverlay = document.querySelector('.modal-overlay');

    // Open Modal
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get product info from the card
            const productCard = this.closest('.product-card-shop');
            const productName = productCard.querySelector('.product-name').textContent;
            const productPrice = productCard.querySelector('.product-price').textContent;
            const productImage = productCard.querySelector('.product-image').src;
            
            // Populate modal with product info
            document.getElementById('modalProductImage').src = productImage;
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalProductPrice').textContent = productPrice;
            
            // Show modal
            quickViewModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Close Modal
    function closeModal() {
        quickViewModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeModal);
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && quickViewModal.classList.contains('active')) {
            closeModal();
        }
    });

    // Modal Size Selector
    const modalSizeButtons = document.querySelectorAll('.modal-size-btn');
    
    modalSizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modalSizeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Modal Quantity Controls
    const modalQtyInput = document.querySelector('.modal-qty-input');
    const modalMinusBtn = document.querySelector('.modal-qty-btn.minus');
    const modalPlusBtn = document.querySelector('.modal-qty-btn.plus');

    if (modalMinusBtn && modalPlusBtn && modalQtyInput) {
        modalMinusBtn.addEventListener('click', function() {
            let currentValue = parseInt(modalQtyInput.value);
            if (currentValue > 1) {
                modalQtyInput.value = currentValue - 1;
            }
        });

        modalPlusBtn.addEventListener('click', function() {
            let currentValue = parseInt(modalQtyInput.value);
            modalQtyInput.value = currentValue + 1;
        });
    }
});

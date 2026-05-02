<?php
// Product Card component for Muscle Bull website
// Parameters:
// - $product: array containing product data (id, name, price, category, image, description, isBestSeller, isFeatured)
// - $show_badge: whether to show best seller/featured badge (true/false)

if (!isset($product)) {
    return;
}

$show_badge = $show_badge ?? true;
$badge_text = '';
$badge_class = '';

if ($show_badge) {
    if ($product['isBestSeller']) {
        $badge_text = 'Best Seller';
        $badge_class = 'bg-primary';
    } elseif ($product['isFeatured']) {
        $badge_text = 'Featured';
        $badge_class = 'bg-success';
    }
}
?>

<div class="col-lg-4 col-md-6 product-item" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-price="<?php echo $product['price']; ?>">
    <div class="product-card">
        <div class="product-image-container">
            <?php if ($show_badge && $badge_text): ?>
                <span class="product-badge <?php echo $badge_class; ?> text-uppercase fw-bold"><?php echo $badge_text; ?></span>
            <?php endif; ?>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            <div class="product-overlay">
                <div class="product-actions">
                    <button class="btn btn-outline-white quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fa-solid fa-eye"></i> Quick View
                    </button>
                    <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="product-price">LKR <?php echo number_format($product['price']); ?></p>
        </div>
    </div>
</div>

<?php
// Product Card component for Muscle Bull website
// Parameters:
// - $product: array containing product data (id, name, price, category, image, description, isBestSeller, isFeatured)

if (!isset($product)) {
    return;
}
?>

<div class="col-lg-4 col-md-6 product-item" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-price="<?php echo $product['price']; ?>">
    <a href="product.php?id=<?php echo (int) $product['id']; ?>" class="product-card-link">
        <article class="product-card">
            <div class="product-image-container">
                <img
                    src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-image"
                    loading="lazy"
                >
            </div>
            <div class="product-info">
                <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="product-meta">
                    <p class="product-price">
                        <span class="product-price__label">LKR</span>
                        <span class="product-price__amount"><?php echo number_format($product['price']); ?></span>
                    </p>
                </div>
            </div>
        </article>
    </a>
</div>

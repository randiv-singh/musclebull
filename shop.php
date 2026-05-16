<?php

require_once 'components/product-card.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$validSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
$validSorts = ['featured', 'price_asc', 'price_desc', 'newest'];
$validPriceRanges = ['1', '2', '3', '4'];

$perPage = 9;

// Parse filters from query string
$categoryInput = $_GET['category'] ?? [];
if (!is_array($categoryInput)) {
    $categoryInput = [$categoryInput];
}

$showAllCategories = empty($categoryInput) || in_array('all', $categoryInput, true);
$selectedCategoryIds = [];
if (!$showAllCategories) {
    foreach ($categoryInput as $cat) {
        if ($cat !== 'all' && is_numeric($cat)) {
            $selectedCategoryIds[] = (int) $cat;
        }
    }
    $selectedCategoryIds = array_values(array_unique($selectedCategoryIds));
}

$priceInput = $_GET['price'] ?? [];
if (!is_array($priceInput)) {
    $priceInput = [$priceInput];
}
$selectedPriceRanges = array_values(array_intersect(
    array_map('strval', $priceInput),
    $validPriceRanges
));

$selectedSize = $_GET['size'] ?? 'M';
if (!in_array($selectedSize, $validSizes, true)) {
    $selectedSize = 'M';
}

$sort = $_GET['sort'] ?? 'featured';
if (!in_array($sort, $validSorts, true)) {
    $sort = 'featured';
}

$page = max(1, (int) ($_GET['page'] ?? 1));

$categories = $categoryModel->getActive();
$totalProducts = $productModel->countFiltered($selectedCategoryIds, $selectedPriceRanges);
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$products = $productModel->getFiltered(
    $selectedCategoryIds,
    $selectedPriceRanges,
    $sort,
    $perPage,
    $offset
);

/**
 * Build shop URL preserving current filters.
 */
function shop_url(array $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    unset($params['page']);
    $query = http_build_query($params);
    return 'shop.php' . ($query ? '?' . $query : '');
}

function shop_page_url($pageNum) {
    $params = $_GET;
    $params['page'] = $pageNum;
    return 'shop.php?' . http_build_query($params);
}

// Heading from active filters
$shopHeading = 'All Products';
if (!$showAllCategories && count($selectedCategoryIds) === 1) {
    foreach ($categories as $cat) {
        if ((int) $cat['id'] === $selectedCategoryIds[0]) {
            $shopHeading = $cat['name'];
            break;
        }
    }
} elseif (!$showAllCategories && count($selectedCategoryIds) > 1) {
    $shopHeading = 'Filtered Products';
}

$activeFilterCount = 0;
if (!$showAllCategories && !empty($selectedCategoryIds)) {
    $activeFilterCount += count($selectedCategoryIds);
}
if (!empty($selectedPriceRanges)) {
    $activeFilterCount += count($selectedPriceRanges);
}
if ($selectedSize !== 'M') {
    $activeFilterCount++;
}
$filtersOpenOnMobile = $activeFilterCount > 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop - Muscle Bull</title>

    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="./assets/css/shop.css?v=3" rel="stylesheet" />
    <link href="./assets/css/product-card.css" rel="stylesheet" />
    <link href="./assets/css/checkout.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php
    $type = 'white';
    $active_page = 'shop';
    include 'components/header.php';
    ?>

    <main>
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Shop</li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="products-section py-5 bg-white">
            <div class="container">
                <form method="get" action="shop.php" id="shop-filters-form">
                    <input type="hidden" name="size" value="<?php echo htmlspecialchars($selectedSize); ?>">
                    <div class="row">
                        <!-- Filters Sidebar -->
                        <div class="col-lg-3 mb-4 mb-lg-0 shop-filters-col">
                            <button
                                type="button"
                                class="shop-filters-toggle d-lg-none"
                                id="shop-filters-toggle"
                                aria-expanded="<?php echo $filtersOpenOnMobile ? 'true' : 'false'; ?>"
                                aria-controls="shop-filters-panel"
                            >
                                <span class="shop-filters-toggle__label">
                                    <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                                    Filters
                                    <?php if ($activeFilterCount > 0): ?>
                                        <span class="shop-filters-toggle__badge"><?php echo $activeFilterCount; ?></span>
                                    <?php endif; ?>
                                </span>
                                <i class="fa-solid fa-chevron-down shop-filters-toggle__icon" aria-hidden="true"></i>
                            </button>

                            <div id="shop-filters-panel"
                                class="shop-filters-panel <?php echo $filtersOpenOnMobile ? 'is-open' : ''; ?>"
                            >
                            <div class="filters-sidebar">
                                <h5 class="filters-sidebar__title text-uppercase fw-bold mb-0 d-none d-lg-block">Filters</h5>

                                <div class="filter-group filter-accordion mb-0 mb-lg-4">
                                    <button
                                        type="button"
                                        class="filter-accordion__trigger d-lg-none"
                                        aria-expanded="true"
                                        data-filter-accordion
                                    >
                                        <span class="filter-title text-uppercase fw-bold mb-0">Category</span>
                                        <i class="fa-solid fa-chevron-down filter-accordion__icon" aria-hidden="true"></i>
                                    </button>
                                    <h6 class="filter-title text-uppercase fw-bold mb-3 d-none d-lg-block">Category</h6>
                                    <div class="filter-accordion__body is-open">
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input shop-filter-auto"
                                            type="checkbox"
                                            name="category[]"
                                            value="all"
                                            id="cat-all"
                                            <?php echo $showAllCategories ? 'checked' : ''; ?>
                                        >
                                        <label class="form-check-label" for="cat-all">All Products</label>
                                    </div>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php $catId = (int) $cat['id']; ?>
                                        <div class="form-check mb-2">
                                            <input
                                                class="form-check-input shop-filter-auto shop-category-check"
                                                type="checkbox"
                                                name="category[]"
                                                value="<?php echo $catId; ?>"
                                                id="cat-<?php echo $catId; ?>"
                                                <?php echo !$showAllCategories && in_array($catId, $selectedCategoryIds, true) ? 'checked' : ''; ?>
                                            >
                                            <label class="form-check-label" for="cat-<?php echo $catId; ?>">
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="filter-group filter-accordion mb-0 mb-lg-4">
                                    <button
                                        type="button"
                                        class="filter-accordion__trigger d-lg-none"
                                        aria-expanded="true"
                                        data-filter-accordion
                                    >
                                        <span class="filter-title text-uppercase fw-bold mb-0">Size</span>
                                        <i class="fa-solid fa-chevron-down filter-accordion__icon" aria-hidden="true"></i>
                                    </button>
                                    <h6 class="filter-title text-uppercase fw-bold mb-3 d-none d-lg-block">Size</h6>
                                    <div class="filter-accordion__body is-open">
                                    <div class="size-options">
                                        <?php foreach ($validSizes as $size): ?>
                                            <a
                                                href="<?php echo htmlspecialchars(shop_url(['size' => $size])); ?>"
                                                class="size-btn <?php echo $selectedSize === $size ? 'active' : ''; ?>"
                                            ><?php echo $size; ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="size-filter-hint">Saved for when you add items to cart.</p>
                                    </div>
                                </div>

                                <a href="shop.php" class="btn btn-outline-dark w-100 text-uppercase fw-bold mt-3">Clear All</a>
                            </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="col-lg-9">
                            <div class="shop-products-header">
                                <div>
                                    <h2 class="shop-products-header__title mb-1"><?php echo htmlspecialchars($shopHeading); ?></h2>
                                    <p class="shop-results-count text-muted small mb-0">
                                        <?php if ($totalProducts === 0): ?>
                                            No products match your filters
                                        <?php else: ?>
                                            Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> product<?php echo $totalProducts !== 1 ? 's' : ''; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <select
                                    class="form-select sort-select"
                                    name="sort"
                                    id="shop-sort"
                                    aria-label="Sort products"
                                >
                                    <option value="featured" <?php echo $sort === 'featured' ? 'selected' : ''; ?>>Sort By: Featured</option>
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                </select>
                            </div>

                            <?php if (empty($products)): ?>
                                <div class="shop-empty-state text-center py-5">
                                    <p class="lead mb-3">No products found.</p>
                                    <a href="shop.php" class="btn btn-primary text-uppercase fw-bold">Clear filters</a>
                                </div>
                            <?php else: ?>
                                <div class="row g-4 products-showcase" id="all-products-container">
                                    <?php foreach ($products as $product): ?>
                                        <?php include 'components/product-card.php'; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($totalPages > 1): ?>
                                <nav class="mt-5" aria-label="Shop pagination">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $page > 1 ? htmlspecialchars(shop_page_url($page - 1)) : '#'; ?>">Previous</a>
                                        </li>
                                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo htmlspecialchars(shop_page_url($p)); ?>"><?php echo $p; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $page < $totalPages ? htmlspecialchars(shop_page_url($page + 1)) : '#'; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <div class="quick-view-modal" id="quickViewModal">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <button type="button" class="close-modal">&times;</button>
            <div class="modal-product-detail">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <img src="" alt="Product" id="modalProductImage" class="modal-product-image">
                    </div>
                    <div class="col-lg-6">
                        <div class="modal-product-info">
                            <h2 class="modal-product-title" id="modalProductName">Product Name</h2>
                            <p class="modal-product-price" id="modalProductPrice">LKR 0</p>
                            <p class="modal-product-description">
                                Premium quality fitness apparel designed for maximum comfort and performance during your workouts.
                            </p>
                            <div class="modal-size-selector">
                                <label class="form-label fw-bold text-uppercase mb-3">Select Size</label>
                                <div class="modal-size-options">
                                    <?php foreach ($validSizes as $size): ?>
                                        <button type="button" class="modal-size-btn <?php echo $size === $selectedSize ? 'active' : ''; ?>"><?php echo $size; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="modal-quantity-selector">
                                <label class="form-label fw-bold text-uppercase mb-3">Quantity</label>
                                <div class="modal-quantity-control">
                                    <button type="button" class="modal-qty-btn minus">-</button>
                                    <input type="number" value="1" min="1" class="modal-qty-input">
                                    <button type="button" class="modal-qty-btn plus">+</button>
                                </div>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-primary btn-lg px-5 py-3 text-uppercase fw-bold flex-grow-1">
                                    <i class="fa-solid fa-bag-shopping me-2"></i> Add to Cart
                                </button>
                                <a href="product.php" class="btn btn-outline-dark btn-lg px-4 py-3">View Full Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $border_top = true;
    include 'components/footer.php';
    ?>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/shop.js"></script>
    <script src="./assets/js/product.js"></script>
</body>
</html>

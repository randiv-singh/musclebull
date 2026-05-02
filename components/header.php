<?php
// Header component for Muscle Bull website
// Parameters:
// - $type: 'transparent' for homepage, 'white' for other pages
// - $active_page: current active page for highlighting nav items

$type = $type ?? 'white';
$active_page = $active_page ?? '';
?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg <?php echo $type === 'transparent' ? 'position-absolute w-100 z-3 navbar-glass' : 'navbar-static bg-white'; ?>">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="./assets/images/logo/mb logo.png" alt="Muscle Bull Logo" height="40">
            <span class="fw-bold text-uppercase <?php echo $type === 'transparent' ? 'text-white' : 'text-black'; ?> brand-text">Muscle Bull</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto nav-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'transparent' ? 'text-white' : 'text-black'; ?> fw-medium text-uppercase <?php echo $active_page === 'shop' ? 'active' : ''; ?>" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'transparent' ? 'text-white' : 'text-black'; ?> fw-medium text-uppercase <?php echo $active_page === 'gift-cards' ? 'active' : ''; ?>" href="gift-cards.php">Gift Cards</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'transparent' ? 'text-white' : 'text-black'; ?> fw-medium text-uppercase <?php echo $active_page === 'about' ? 'active' : ''; ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $type === 'transparent' ? 'text-white' : 'text-black'; ?> fw-medium text-uppercase <?php echo $active_page === 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                </li>
            </ul>
            <div class="navbar-icons d-flex gap-3">
                <a href="#" class="<?php echo $type === 'transparent' ? 'nav-icon' : 'nav-icon-dark'; ?>">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
                <a href="login.php" class="<?php echo $type === 'transparent' ? 'nav-icon' : 'nav-icon-dark'; ?>">
                    <i class="fa-solid fa-user"></i>
                </a>
                <a href="cart.php" class="<?php echo $type === 'transparent' ? 'nav-icon' : 'nav-icon-dark'; ?>">
                    <i class="fa-solid fa-bag-shopping"></i>
                </a>
            </div>
        </div>
    </div>
</nav>
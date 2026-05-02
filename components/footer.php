<?php
// Footer component for Muscle Bull website
// Parameters:
// - $border_top: whether to show border top (true/false)
?>

<!-- Footer -->
<footer class="bg-white text-black pt-5 pb-4 <?php echo $border_top ? 'border-top border-dark' : ''; ?>">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <h4 class="text-uppercase fw-bold mb-4">Muscle Bull</h4>
                <p>Premium fitness apparel for those who push their limits. Join the herd and unleash your true potential.</p>
                <div class="footer-socials d-flex gap-3 mt-4">
                    <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-2 mb-4 mb-md-0">
                <h5 class="text-uppercase fw-bold mb-4">Shop</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="shop.php">Men</a></li>
                    <li class="mb-2"><a href="shop.php">Women</a></li>
                    <li class="mb-2"><a href="shop.php">Accessories</a></li>
                    <li class="mb-2"><a href="shop.php">New Arrivals</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2 mb-4 mb-md-0">
                <h5 class="text-uppercase fw-bold mb-4">Support</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="#">FAQ</a></li>
                    <li class="mb-2"><a href="#">Shipping</a></li>
                    <li class="mb-2"><a href="#">Returns</a></li>
                    <li class="mb-2"><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-4">
                <h5 class="text-uppercase fw-bold mb-4">Newsletter</h5>
                <p class="mb-3">Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
                <div class="footer-newsletter">
                    <input type="email" class="form-control rounded-0 border-dark" placeholder="Enter your email">
                    <button class="btn btn-primary rounded-0 px-4 text-uppercase fw-bold" type="button">Subscribe</button>
                </div>
            </div>
        </div>
        <hr class="my-4 border-dark">
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0 fw-medium">&copy; <?php echo date('Y'); ?> Muscle Bull. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>
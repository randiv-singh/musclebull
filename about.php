<?php
require_once 'components/header.php';
require_once 'components/footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./css/style.css" rel="stylesheet" />
    <link href="./css/header.css" rel="stylesheet" />
    <link href="./css/footer.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = 'about';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- About Hero Section -->
        <section class="about-hero py-5 bg-white">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <h1 class="display-2 fw-bold text-uppercase mb-4 text-black" style="line-height: 0.9; letter-spacing: -0.03em;">
                            WE ARE<br><span class="text-primary">MUSCLE BULL</span>
                        </h1>
                        <p class="lead mb-4 text-black fw-medium">
                            Born from the desire to push boundaries. We create premium fitness apparel for those who refuse to settle for average.
                        </p>
                        <p class="text-black mb-5">
                            Muscle Bull isn't just a brand; it's a statement. It's for the early risers, the late grinders, and everyone in between who demands more from themselves and their gear. We blend cutting-edge fabric technology with streetwear aesthetics to bring you apparel that performs in the gym and turns heads on the street.
                        </p>
                        <a href="shop.php" class="btn btn-dark btn-lg px-5 py-3 text-uppercase fw-bold rounded-0">
                            Explore The Gear
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <img src="./assets/images/backgrounds/non black.png" alt="About Muscle Bull" class="img-fluid w-100 object-fit-cover" style="min-height: 500px;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Values Section -->
        <section class="values-section py-5 bg-black text-white">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-4 fw-bold text-uppercase text-white">Our Core Values</h2>
                </div>
                <div class="row g-5 text-center mt-4">
                    <div class="col-md-4">
                        <i class="fa-solid fa-fire fa-3x text-primary mb-4"></i>
                        <h4 class="fw-bold text-uppercase mb-3">Relentless Drive</h4>
                        <p class="text-white-50">We never stop innovating. Our gear is constantly evolving to match your growing ambition and performance needs.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fa-solid fa-gem fa-3x text-primary mb-4"></i>
                        <h4 class="fw-bold text-uppercase mb-3">Premium Quality</h4>
                        <p class="text-white-50">No compromises. We source only the highest quality materials to ensure durability, comfort, and style.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fa-solid fa-users fa-3x text-primary mb-4"></i>
                        <h4 class="fw-bold text-uppercase mb-3">The Herd</h4>
                        <p class="text-white-50">More than customers, we are a community. We support, motivate, and celebrate each other's victories.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section (Reused) -->
        <section class="features-section bg-white py-5 border-top border-dark">
            <div class="container">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-truck-fast fa-3x text-black"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2 text-black">Shipping</h6>
                            <p class="small mb-0 text-black">Standard shipping (Estimated 3-5 days)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-shield-halved fa-3x text-black"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2 text-black">Payments</h6>
                            <p class="small mb-0 text-black">Payment is 100% secure</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-rotate-left fa-3x text-black"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2 text-black">Easy Returns</h6>
                            <p class="small mb-0 text-black">30 days to change your mind!</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="feature-item">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-leaf fa-3x text-black"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase mb-2 text-black">Made in Sri Lanka</h6>
                            <p class="small mb-0 text-black">Sustainably Sourced</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php 
    $border_top = true;
    include 'components/footer.php'; 
    ?>

    <!-- Bootstrap JS -->
    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>

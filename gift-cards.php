
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gift Cards - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = 'gift-cards';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Gift Cards</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Gift Card Section -->
        <section class="gift-card-page py-5 bg-white">
            <div class="container py-4">
                <div class="row g-5 align-items-center">
                    <!-- Gift Card Image -->
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="./assets/images/flayer/gift-card.jpg" alt="Musclebull Gift Card" class="img-fluid w-100 object-fit-cover border border-dark" style="min-height: 500px;">
                            <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                                <h2 class="display-4 fw-bold text-white text-uppercase" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">E-Gift Card</h2>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gift Card Details -->
                    <div class="col-lg-6">
                        <div class="px-lg-4">
                            <h1 class="display-5 fw-bold text-uppercase mb-3 text-black">Muscle Bull<br>E-Gift Card</h1>
                            <p class="lead mb-4 text-black">
                                Give the gift of choice. Perfect for the gym addict in your life. Let them pick their favorite gear to crush their fitness goals.
                            </p>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-uppercase mb-3 text-black">Select Amount</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <input type="radio" class="btn-check" name="amount" id="amt1" autocomplete="off" checked>
                                    <label class="btn btn-outline-dark px-4 py-2 fw-bold" for="amt1">LKR 2,500</label>

                                    <input type="radio" class="btn-check" name="amount" id="amt2" autocomplete="off">
                                    <label class="btn btn-outline-dark px-4 py-2 fw-bold" for="amt2">LKR 5,000</label>

                                    <input type="radio" class="btn-check" name="amount" id="amt3" autocomplete="off">
                                    <label class="btn btn-outline-dark px-4 py-2 fw-bold" for="amt3">LKR 10,000</label>

                                    <input type="radio" class="btn-check" name="amount" id="amt4" autocomplete="off">
                                    <label class="btn btn-outline-dark px-4 py-2 fw-bold" for="amt4">LKR 15,000</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-uppercase mb-3 text-black">Delivery Method</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input border-dark" type="radio" name="delivery" id="delEmail" checked>
                                    <label class="form-check-label text-black" for="delEmail">
                                        Send to me (I'll forward it later)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input border-dark" type="radio" name="delivery" id="delFriend">
                                    <label class="form-check-label text-black" for="delFriend">
                                        Send directly to a friend
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-3 mt-5">
                                <button class="btn btn-primary btn-lg py-3 text-uppercase fw-bold rounded-0" id="addGiftCardBtn">
                                    <i class="fa-solid fa-cart-plus me-2"></i> Add to Cart
                                </button>
                            </div>
                            
                            <div class="mt-4 pt-4 border-top border-dark">
                                <ul class="list-unstyled text-black small">
                                    <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> Delivered instantly via email</li>
                                    <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> No expiration date</li>
                                    <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> Can be used on all products</li>
                                </ul>
                            </div>
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
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/app.js"></script>
</body>
</html>

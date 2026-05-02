
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Muscle Bull</title>
    
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
    $active_page = 'contact';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-page py-5 bg-white">
            <div class="container py-4">
                <div class="row g-5">
                    <!-- Contact Info -->
                    <div class="col-lg-5">
                        <h1 class="display-4 fw-bold text-uppercase mb-4 text-black" style="line-height: 0.9; letter-spacing: -0.03em;">
                            GET IN<br><span class="text-primary">TOUCH</span>
                        </h1>
                        <p class="lead mb-5 text-black">
                            Have a question about our gear, an order, or just want to say what's up? Drop us a line. We're here to help you crush your goals.
                        </p>
                        
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-black text-white p-3 rounded-0 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-location-dot fa-xl"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-uppercase mb-1">HQ Address</h5>
                                    <p class="mb-0 text-black">123 Fitness Avenue, Colombo 03, Sri Lanka</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-black text-white p-3 rounded-0 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-envelope fa-xl"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-uppercase mb-1">Email Us</h5>
                                    <p class="mb-0 text-black">support@musclebull.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-black text-white p-3 rounded-0 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-phone fa-xl"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-uppercase mb-1">Call Us</h5>
                                    <p class="mb-0 text-black">+94 11 234 5678</p>
                                    <p class="small text-muted mb-0">Mon-Fri, 9am - 6pm</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="col-lg-7">
                        <div class="bg-white p-4 p-md-5 border border-dark">
                            <h3 class="fw-bold text-uppercase mb-4 text-black">Send a Message</h3>
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label fw-bold text-uppercase small text-black">First Name</label>
                                        <input type="text" class="form-control rounded-0 py-3 border-dark text-black" id="firstName" placeholder="John">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label fw-bold text-uppercase small text-black">Last Name</label>
                                        <input type="text" class="form-control rounded-0 py-3 border-dark text-black" id="lastName" placeholder="Doe">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label fw-bold text-uppercase small text-black">Email Address</label>
                                        <input type="email" class="form-control rounded-0 py-3 border-dark text-black" id="email" placeholder="john@example.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="orderNumber" class="form-label fw-bold text-uppercase small text-black">Order Number (Optional)</label>
                                        <input type="text" class="form-control rounded-0 py-3 border-dark text-black" id="orderNumber" placeholder="#MB12345">
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label fw-bold text-uppercase small text-black">Message</label>
                                        <textarea class="form-control rounded-0 py-3 border-dark text-black" id="message" rows="5" placeholder="How can we help you?"></textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-dark btn-lg w-100 rounded-0 text-uppercase fw-bold py-3">Send Message</button>
                                    </div>
                                </div>
                            </form>
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
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Muscle Bull</title>
    
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
    $active_page = '';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Create Account</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Register Section -->
        <section class="register-page py-5 bg-white">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-6">
                        <div class="bg-white p-4 p-md-5 border border-dark">
                            <div class="text-center mb-5">
                                <h2 class="fw-bold text-uppercase mb-2 text-black">Join The Herd</h2>
                                <p class="text-black">Create an account for faster checkout and exclusive offers.</p>
                            </div>
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label fw-bold text-uppercase small text-black">First Name</label>
                                        <input type="text" class="form-control rounded-0 py-3 border-dark text-black" id="firstName" placeholder="First Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label fw-bold text-uppercase small text-black">Last Name</label>
                                        <input type="text" class="form-control rounded-0 py-3 border-dark text-black" id="lastName" placeholder="Last Name">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label fw-bold text-uppercase small text-black">Email Address</label>
                                        <input type="email" class="form-control rounded-0 py-3 border-dark text-black" id="email" placeholder="Enter your email">
                                    </div>
                                    <div class="col-12">
                                        <label for="password" class="form-label fw-bold text-uppercase small text-black">Password</label>
                                        <input type="password" class="form-control rounded-0 py-3 border-dark text-black" id="password" placeholder="Create a password">
                                    </div>
                                    <div class="col-12 mt-5">
                                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-0 text-uppercase fw-bold py-3">Create Account</button>
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <p class="text-black mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
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

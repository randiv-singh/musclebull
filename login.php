<?php
session_start();
require_once 'classes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = new User();
        $authenticatedUser = $user->authenticate($email, $password);
        
        if ($authenticatedUser) {
            $_SESSION['user_id'] = $authenticatedUser['id'];
            $_SESSION['user_name'] = $authenticatedUser['name'];
            $_SESSION['user_email'] = $authenticatedUser['email'];
            $_SESSION['user_role'] = $authenticatedUser['role'];
            
            // Redirect based on role
            if ($authenticatedUser['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Muscle Bull</title>
    
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
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">Login</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Login Section -->
        <section class="login-page py-5 bg-white">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-5">
                        <div class="bg-white p-4 p-md-5 border border-dark">
                            <div class="text-center mb-5">
                                <h2 class="fw-bold text-uppercase mb-2 text-black">Welcome Back</h2>
                                <p class="text-black">Sign in to access your account</p>
                            </div>
                            <?php if ($error): ?>
                                <div class="alert alert-danger rounded-0" role="alert">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email" class="form-label fw-bold text-uppercase small text-black">Email Address</label>
                                        <input type="email" class="form-control rounded-0 py-3 border-dark text-black" id="email" name="email" placeholder="Enter your email" required>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="password" class="form-label fw-bold text-uppercase small text-black mb-0">Password</label>
                                            <a href="#" class="small text-primary text-decoration-none fw-bold">Forgot Password?</a>
                                        </div>
                                        <input type="password" class="form-control rounded-0 py-3 border-dark text-black" id="password" name="password" placeholder="Enter your password" required>
                                    </div>
                                    <div class="col-12 mt-5">
                                        <button type="submit" class="btn btn-dark btn-lg w-100 rounded-0 text-uppercase fw-bold py-3">Sign In</button>
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <p class="text-black mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Create one</a></p>
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

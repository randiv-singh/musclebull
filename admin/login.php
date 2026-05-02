<?php
session_start();
require_once '../classes/User.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = new User();
    $authenticatedUser = $user->authenticate($email, $password);
    
    if ($authenticatedUser && $authenticatedUser['role'] === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $authenticatedUser;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password, or insufficient privileges';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href=".../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <!-- Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fa-solid fa-cog me-2"></i>Admin Login</h1>
            <p>Muscle Bull E-commerce</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fa-solid fa-sign-in-alt me-2"></i>Login
            </button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">
                <i class="fa-solid fa-arrow-left me-1"></i>Back to Store
            </a>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                Default Admin: admin@musclebull.com / password
            </small>
        </div>
    </div>
    
</body>
</html>

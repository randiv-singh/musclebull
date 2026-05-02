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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        body {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: #fff;
            border: 3px solid #000;
            border-radius: 0;
            padding: 3rem;
            max-width: 400px;
            width: 100%;
            margin: 2rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
            margin: 0;
        }
        
        .form-control {
            border: 2px solid #000;
            border-radius: 0;
            padding: 0.75rem;
            font-weight: 500;
        }
        
        .form-control:focus {
            border-color: #000;
            box-shadow: none;
        }
        
        .btn-login {
            background: #000;
            color: #fff;
            border: 2px solid #000;
            border-radius: 0;
            padding: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: #fff;
            color: #000;
        }
        
        .alert {
            border: 2px solid #000;
            border-radius: 0;
            font-weight: 500;
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: #000;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
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
    
    <!-- Bootstrap JS -->
    <script src=".../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

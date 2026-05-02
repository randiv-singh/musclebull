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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin-bootstrap.css" rel="stylesheet" />
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            margin: 1rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #D5552E;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .login-header p {
            color: #666;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            border-color: #D5552E;
            box-shadow: 0 0 0 4px rgba(213, 85, 46, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #D5552E 0%, #c44a28 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.875rem;
            font-weight: 600;
            width: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(213, 85, 46, 0.3);
        }
        
        .back-link a {
            color: #666;
            text-decoration: none;
        }
        
        .back-link a:hover {
            color: #D5552E;
        }
    </style>
</head>
<body>
    <div class="login-card">
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

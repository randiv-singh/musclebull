<?php
session_start();
require_once 'classes/User.php';
require_once 'classes/Order.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = new User();
$order = new Order();

$currentUser = $user->getById($_SESSION['user_id']);
$userOrders = $order->getByUserId($_SESSION['user_id']);

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($name) || empty($email)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        if ($user->update($_SESSION['user_id'], $name, $email)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = 'Profile updated successfully!';
            $currentUser = $user->getById($_SESSION['user_id']);
        } else {
            $error = 'Failed to update profile. Email may already be in use.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all password fields';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters long';
    } else {
        // Verify current password
        $authenticated = $user->authenticate($_SESSION['user_email'], $currentPassword);
        if ($authenticated) {
            if ($user->updatePassword($_SESSION['user_id'], $newPassword)) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password';
            }
        } else {
            $error = 'Current password is incorrect';
        }
    }
}

// Get order status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'shipped': return 'bg-info text-dark';
        case 'delivered': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="./assets/css/style.css" rel="stylesheet" />
    <link href="./assets/css/header.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .profile-sidebar {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 2rem;
        }
        .profile-nav-link {
            color: #333;
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .profile-nav-link:hover,
        .profile-nav-link.active {
            background: #000;
            color: #fff;
        }
        .profile-content {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .order-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .avatar-circle {
            width: 80px;
            height: 80px;
            background: #000;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <?php 
    $type = 'white';
    $active_page = 'profile';
    include 'components/header.php'; 
    ?>

    <main>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-white">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-black">Home</a></li>
                        <li class="breadcrumb-item active text-black fw-bold" aria-current="page">My Profile</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Profile Section -->
        <section class="profile-section py-5 bg-light">
            <div class="container">
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-0" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success rounded-0" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Sidebar -->
                    <div class="col-lg-3">
                        <div class="profile-sidebar text-center">
                            <div class="avatar-circle">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            
                            <nav class="text-start mt-4">
                                <a href="#profile" class="profile-nav-link active" onclick="showSection('profile', this)">
                                    <i class="fa-solid fa-user me-2"></i> Profile Info
                                </a>
                                <a href="#orders" class="profile-nav-link" onclick="showSection('orders', this)">
                                    <i class="fa-solid fa-box me-2"></i> My Orders
                                    <?php if (count($userOrders) > 0): ?>
                                        <span class="badge bg-dark float-end"><?php echo count($userOrders); ?></span>
                                    <?php endif; ?>
                                </a>
                                <a href="#password" class="profile-nav-link" onclick="showSection('password', this)">
                                    <i class="fa-solid fa-lock me-2"></i> Change Password
                                </a>
                                <a href="logout.php" class="profile-nav-link text-danger">
                                    <i class="fa-solid fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </nav>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-lg-9">
                        <!-- Profile Info Section -->
                        <div id="profile" class="profile-content">
                            <h4 class="fw-bold text-uppercase mb-4">Profile Information</h4>
                            <form method="POST" action="">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <input type="text" name="name" class="form-control py-3" 
                                            value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" name="email" class="form-control py-3" 
                                            value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Member Since</label>
                                        <input type="text" class="form-control py-3" 
                                            value="<?php echo htmlspecialchars($currentUser['created_at'] ?? ''); ?>" readonly>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-dark px-5 py-3 text-uppercase fw-bold">
                                            Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Orders Section -->
                        <div id="orders" class="profile-content" style="display: none;">
                            <h4 class="fw-bold text-uppercase mb-4">My Orders</h4>
                            
                            <?php if (empty($userOrders)): ?>
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5>No orders yet</h5>
                                    <p class="text-muted">You haven't placed any orders yet.</p>
                                    <a href="shop.php" class="btn btn-dark px-4 py-2 text-uppercase fw-bold">Start Shopping</a>
                                </div>
                            <?php else: ?>
                                <?php foreach (array_reverse($userOrders) as $userOrder): ?>
                                    <div class="order-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Order #<?php echo $userOrder['id']; ?></strong></p>
                                                <p class="text-muted small mb-0">
                                                    <?php echo date('M d, Y', strtotime($userOrder['order_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <span class="badge <?php echo getStatusBadgeClass($userOrder['status']); ?> px-3 py-2">
                                                    <?php echo ucfirst($userOrder['status']); ?>
                                                </span>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <p class="fw-bold mb-1">LKR <?php echo number_format($userOrder['total_amount']); ?></p>
                                                <p class="small text-muted mb-0"><?php echo count($userOrder['items']); ?> item(s)</p>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($userOrder['items'])): ?>
                                            <hr class="my-3">
                                            <div class="row g-2">
                                                <?php foreach ($userOrder['items'] as $item): ?>
                                                    <div class="col-auto">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="<?php echo htmlspecialchars($item['image'] ?? './assets/images/products/placeholder.jpg'); ?>" 
                                                                alt="<?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>" 
                                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                            <small><?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?> 
                                                                (<?php echo $item['quantity'] ?? 0; ?>)</small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($userOrder['tracking_number']): ?>
                                            <hr class="my-3">
                                            <p class="small mb-0">
                                                <i class="fa-solid fa-truck me-2"></i>
                                                Tracking: <strong><?php echo htmlspecialchars($userOrder['tracking_number']); ?></strong>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Change Password Section -->
                        <div id="password" class="profile-content" style="display: none;">
                            <h4 class="fw-bold text-uppercase mb-4">Change Password</h4>
                            <form method="POST" action="">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Current Password</label>
                                        <input type="password" name="current_password" class="form-control py-3" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">New Password</label>
                                        <input type="password" name="new_password" class="form-control py-3" required minlength="6">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control py-3" required minlength="6">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="change_password" class="btn btn-dark px-5 py-3 text-uppercase fw-bold">
                                            Change Password
                                        </button>
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
    <script>
        function showSection(sectionId, linkElement) {
            // Hide all sections
            document.getElementById('profile').style.display = 'none';
            document.getElementById('orders').style.display = 'none';
            document.getElementById('password').style.display = 'none';
            
            // Remove active class from all links
            document.querySelectorAll('.profile-nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).style.display = 'block';
            
            // Add active class to clicked link
            linkElement.classList.add('active');
            
            // Prevent default anchor behavior
            return false;
        }
    </script>
</body>
</html>

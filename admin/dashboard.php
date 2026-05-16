<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Product.php';
require_once '../classes/GiftCard.php';
require_once '../classes/Category.php';
require_once '../classes/Order.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$product = new Product();
$user = new User();
$giftCard = new GiftCard();
$category = new Category();
$order = new Order();

// Get statistics
$stats = [
    'total_products' => count($product->getAll()),
    'total_users' => count($user->getAll()),
    'total_orders' => count($order->getAll()),
    'total_gift_cards' => count($giftCard->getAll()),
    'pending_orders' => count($order->getPending()),
    'total_revenue' => $order->getTotalRevenue()
];

// Get recent orders
$recentOrders = array_slice($order->getAll(), -5);
$recentOrders = array_reverse($recentOrders);

// Get recent users
$recentUsers = array_slice($user->getAll(), -3);
$recentUsers = array_reverse($recentUsers);

// Get low stock products (mock - since we don't have stock in product class)
$lowStockProducts = []; // Could be implemented later
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin-bootstrap.css" rel="stylesheet" />
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fa-solid fa-cog me-2"></i>Muscle Bull</h3>
            <small>Admin Panel</small>
        </div>
        
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="nav-link active">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="products.php" class="nav-link">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="orders.php" class="nav-link">
                <i class="fa-solid fa-shopping-cart"></i> Orders
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="categories.php" class="nav-link">
                <i class="fa-solid fa-tags"></i> Categories
            </a>
            <a href="gift-cards.php" class="nav-link">
                <i class="fa-solid fa-gift"></i> Gift Cards
            </a>
            <a href="messages.php" class="nav-link">
                <i class="fa-solid fa-envelope"></i> Messages
            </a>
            <a href="reviews.php" class="nav-link">
                <i class="fa-solid fa-star"></i> Reviews
            </a>
        </nav>
        
        <div class="user-info">
            <div class="text-white">
                <i class="fa-solid fa-user me-2"></i>
                <?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?>
            </div>
            <small class="text-muted">Administrator</small>
        </div>
        
        <div class="logout-btn">
            <a href="logout.php" class="btn btn-outline-light btn-sm w-100">
                <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1><i class="fa-solid fa-dashboard me-2"></i>Dashboard</h1>
            <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?>!</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-shopping-cart"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div class="stat-number">LKR <?php echo number_format($stats['total_revenue']); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="recent-section">
                    <h3><i class="fa-solid fa-shopping-cart me-2"></i>Recent Orders</h3>
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted">No orders found.</p>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="item-title">Order #<?php echo $order['id']; ?></div>
                                        <div class="item-meta">
                                            <?php echo date('M j, Y', strtotime($order['order_date'])); ?> • 
                                            LKR <?php echo number_format($order['total_amount']); ?>
                                        </div>
                                    </div>
                                    <span class="badge badge-status bg-<?php echo $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info'); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="orders.php" class="btn btn-dark btn-sm">View All Orders</a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="col-md-6">
                <div class="recent-section">
                    <h3><i class="fa-solid fa-users me-2"></i>Recent Users</h3>
                    <?php if (empty($recentUsers)): ?>
                        <p class="text-muted">No users found.</p>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="item-title"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <div class="item-meta">
                                            <?php echo htmlspecialchars($user['email']); ?> • 
                                            <?php echo ucfirst($user['role']); ?>
                                        </div>
                                    </div>
                                    <span class="badge badge-status bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="users.php" class="btn btn-dark btn-sm">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fa-solid fa-bars"></i>
    </button>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    
    <script>
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.mobile-menu-overlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
</body>
</html>

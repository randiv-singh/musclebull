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
    <link href=".../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        
        .sidebar {
            background: #000;
            color: #fff;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        
        .sidebar-header h3 {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .nav-link {
            color: #fff;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: #333;
            color: #fff;
        }
        
        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .content-header {
            background: #fff;
            border: 2px solid #000;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .content-header h1 {
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        
        .stat-card {
            background: #fff;
            border: 2px solid #000;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            color: #000;
            margin-bottom: 1rem;
        }
        
        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-label {
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 0.875rem;
        }
        
        .recent-section {
            background: #fff;
            border: 2px solid #000;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .recent-section h3 {
            font-size: 1.25rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1rem;
            color: #000;
        }
        
        .recent-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        .recent-item .item-title {
            font-weight: bold;
            color: #000;
        }
        
        .recent-item .item-meta {
            color: #666;
            font-size: 0.875rem;
        }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .logout-btn {
            position: absolute;
            bottom: 1rem;
            left: 1.5rem;
            right: 1.5rem;
        }
        
        .user-info {
            padding: 1rem 1.5rem;
            border-top: 1px solid #333;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .logout-btn {
                position: relative;
                margin-top: 1rem;
            }
        }
    </style>
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
        
        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-12">
                <div class="recent-section">
                    <h3><i class="fa-solid fa-chart-line me-2"></i>Quick Stats</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-warning"><?php echo $stats['pending_orders']; ?></div>
                                <div class="text-muted">Pending Orders</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-info"><?php echo $stats['total_gift_cards']; ?></div>
                                <div class="text-muted">Total Gift Cards</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success"><?php echo count($category->getActive()); ?></div>
                                <div class="text-muted">Active Categories</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-danger"><?php echo count($order->getCancelled()); ?></div>
                                <div class="text-muted">Cancelled Orders</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src=".../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

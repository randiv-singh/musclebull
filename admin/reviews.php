<?php
session_start();
require_once '../classes/Review.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$reviewModel = new Review();
$message = '';
$error = '';
$filter = $_GET['status'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    switch ($action) {
        case 'approve':
            if ($reviewModel->approve($id)) {
                $message = 'Review approved and is now public.';
            } else {
                $error = 'Failed to approve review.';
            }
            break;
        case 'reject':
            if ($reviewModel->reject($id)) {
                $message = 'Review rejected.';
            } else {
                $error = 'Failed to reject review.';
            }
            break;
        case 'delete':
            if ($reviewModel->delete($id)) {
                $message = 'Review deleted.';
            } else {
                $error = 'Failed to delete review.';
            }
            break;
    }
}

$reviews = $reviewModel->getAllForAdmin($filter !== '' ? $filter : null);
$pendingCount = $reviewModel->getPendingCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Reviews - Muscle Bull</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="../assets/css/admin-bootstrap.css" rel="stylesheet" />
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fa-solid fa-cog me-2"></i>Muscle Bull</h3>
            <small>Admin Panel</small>
        </div>
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-dashboard"></i> Dashboard</a>
            <a href="products.php" class="nav-link"><i class="fa-solid fa-box"></i> Products</a>
            <a href="orders.php" class="nav-link"><i class="fa-solid fa-shopping-cart"></i> Orders</a>
            <a href="users.php" class="nav-link"><i class="fa-solid fa-users"></i> Users</a>
            <a href="categories.php" class="nav-link"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="gift-cards.php" class="nav-link"><i class="fa-solid fa-gift"></i> Gift Cards</a>
            <a href="messages.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="reviews.php" class="nav-link active">
                <i class="fa-solid fa-star"></i> Reviews
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-danger ms-1"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <div class="user-info">
            <div class="text-white"><i class="fa-solid fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?></div>
            <small class="text-muted">Administrator</small>
        </div>
        <div class="logout-btn">
            <a href="logout.php" class="btn btn-outline-light btn-sm w-100"><i class="fa-solid fa-sign-out-alt me-2"></i>Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h1><i class="fa-solid fa-star me-2"></i>Product Reviews</h1>
            <p class="mb-0">Approve or reject customer reviews before they appear on product pages</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="mb-4 d-flex flex-wrap gap-2">
            <a href="reviews.php" class="btn btn-sm <?php echo $filter === '' ? 'btn-dark' : 'btn-outline-dark'; ?>">All</a>
            <a href="reviews.php?status=pending" class="btn btn-sm <?php echo $filter === 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                Pending <?php if ($pendingCount > 0): ?>(<?php echo $pendingCount; ?>)<?php endif; ?>
            </a>
            <a href="reviews.php?status=approved" class="btn btn-sm <?php echo $filter === 'approved' ? 'btn-success' : 'btn-outline-success'; ?>">Approved</a>
            <a href="reviews.php?status=rejected" class="btn btn-sm <?php echo $filter === 'rejected' ? 'btn-secondary' : 'btn-outline-secondary'; ?>">Rejected</a>
        </div>

        <div class="table-responsive mb-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">Reviews (<?php echo count($reviews); ?>)</h3>
            <?php if (empty($reviews)): ?>
                <p class="text-muted">No reviews found.</p>
            <?php else: ?>
                <table class="table user-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $rev): ?>
                            <tr class="<?php echo $rev['status'] === 'pending' ? 'table-warning' : ''; ?>">
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($rev['product_name']); ?></div>
                                    <small class="text-muted">ID: <?php echo $rev['product_id']; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($rev['reviewer_name']); ?></div>
                                    <small><?php echo htmlspecialchars($rev['reviewer_email']); ?></small>
                                    <?php if ($rev['user_id']): ?>
                                        <br><span class="badge bg-primary badge-status">Registered</span>
                                    <?php else: ?>
                                        <br><span class="badge bg-secondary badge-status">Guest</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo Review::renderStars((int) $rev['rating']); ?></td>
                                <td style="max-width: 280px;">
                                    <?php if (!empty($rev['title'])): ?>
                                        <strong><?php echo htmlspecialchars($rev['title']); ?></strong><br>
                                    <?php endif; ?>
                                    <small><?php echo htmlspecialchars(mb_strimwidth($rev['body'], 0, 120, '…')); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badge = match ($rev['status']) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-secondary',
                                        default => 'bg-warning text-dark',
                                    };
                                    ?>
                                    <span class="badge <?php echo $badge; ?> badge-status"><?php echo ucfirst($rev['status']); ?></span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($rev['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if ($rev['status'] !== 'approved'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                                <button type="submit" class="btn btn-outline-success btn-action" title="Approve"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($rev['status'] !== 'rejected'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                                <button type="submit" class="btn btn-outline-warning btn-action" title="Reject"><i class="fa-solid fa-ban"></i></button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this review?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-action" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()"><i class="fa-solid fa-bars"></i></button>
    <div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.mobile-menu-overlay').classList.toggle('show');
        }
    </script>
</body>
</html>

<?php
session_start();
require_once '../classes/ContactMessage.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$contactMessage = new ContactMessage();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    switch ($action) {
        case 'mark_read':
            if ($contactMessage->markAsRead($id)) {
                $message = 'Message marked as read.';
            } else {
                $error = 'Failed to update message status.';
            }
            break;

        case 'delete':
            if ($contactMessage->delete($id)) {
                $message = 'Message deleted successfully.';
            } else {
                $error = 'Failed to delete message.';
            }
            break;
    }
}

$messages = $contactMessage->getAll();
$unreadCount = $contactMessage->getUnreadCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Messages - Muscle Bull</title>
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
            <a href="dashboard.php" class="nav-link">
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
            <a href="messages.php" class="nav-link active">
                <i class="fa-solid fa-envelope"></i> Messages
                <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-danger ms-1"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
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

    <div class="main-content">
        <div class="content-header">
            <h1><i class="fa-solid fa-envelope me-2"></i>Contact Messages</h1>
            <p class="mb-0">View and manage messages from the contact form</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive mb-5 mt-4">
            <h3 class="h4 text-uppercase fw-bold mb-4">
                All Messages (<?php echo count($messages); ?>)
                <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-warning text-dark ms-2"><?php echo $unreadCount; ?> unread</span>
                <?php endif; ?>
            </h3>

            <?php if (empty($messages)): ?>
                <p class="text-muted">No contact messages yet.</p>
            <?php else: ?>
                <table class="table user-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Email</th>
                            <th>Order #</th>
                            <th>Preview</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $item): ?>
                            <tr class="<?php echo $item['status'] === 'new' ? 'table-warning' : ''; ?>">
                                <td>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?>
                                    </div>
                                    <small class="text-muted">ID: <?php echo $item['id']; ?></small>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>">
                                        <?php echo htmlspecialchars($item['email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($item['order_number'])): ?>
                                        <?php echo htmlspecialchars($item['order_number']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars(mb_strimwidth($item['message'], 0, 60, '…')); ?></small>
                                </td>
                                <td>
                                    <?php if ($item['status'] === 'new'): ?>
                                        <span class="badge bg-warning text-dark badge-status">New</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary badge-status">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-action view-msg-btn"
                                                data-first="<?php echo htmlspecialchars($item['first_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-last="<?php echo htmlspecialchars($item['last_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-email="<?php echo htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-order="<?php echo htmlspecialchars($item['order_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-date="<?php echo htmlspecialchars($item['created_at'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-message-b64="<?php echo base64_encode($item['message']); ?>"
                                                title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if ($item['status'] === 'new'): ?>
                                            <form method="POST" action="messages.php" class="d-inline">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-outline-success btn-action" title="Mark as read">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="messages.php" class="d-inline"
                                              onsubmit="return confirm('Delete this message?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-action" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
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

    <div class="modal fade" id="viewMessageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-uppercase">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <strong>Name</strong>
                            <p id="modalName" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Email</strong>
                            <p id="modalEmail" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Order Number</strong>
                            <p id="modalOrder" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Received</strong>
                            <p id="modalDate" class="mb-0"></p>
                        </div>
                    </div>
                    <strong>Message</strong>
                    <p id="modalMessage" class="mt-2 mb-0" style="white-space: pre-wrap;"></p>
                </div>
                <div class="modal-footer">
                    <a id="modalReply" href="#" class="btn btn-dark text-uppercase fw-bold">
                        <i class="fa-solid fa-reply me-2"></i>Reply via Email
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMobileMenu() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.mobile-menu-overlay').classList.toggle('show');
        }

        document.querySelectorAll('.view-msg-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const first = btn.dataset.first;
                const last = btn.dataset.last;
                const email = btn.dataset.email;
                document.getElementById('modalName').textContent = first + ' ' + last;
                document.getElementById('modalEmail').textContent = email;
                document.getElementById('modalOrder').textContent = btn.dataset.order || '—';
                document.getElementById('modalDate').textContent = new Date(btn.dataset.date).toLocaleString();
                document.getElementById('modalMessage').textContent = atob(btn.dataset.messageB64);
                document.getElementById('modalReply').href = 'mailto:' + encodeURIComponent(email)
                    + '?subject=' + encodeURIComponent('Re: Your message to Muscle Bull');
                new bootstrap.Modal(document.getElementById('viewMessageModal')).show();
            });
        });
    </script>
</body>
</html>

<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Order.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$order = new Order();
$user = new User();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_status':
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $notes = $_POST['notes'] ?? '';

            if ($order->updateStatus($id, $status, $notes)) {
                $message = 'Order status updated successfully!';
            } else {
                $error = 'Failed to update order status.';
            }
            break;

        case 'update_payment':
            $id = intval($_POST['id'] ?? 0);
            $paymentStatus = $_POST['payment_status'] ?? '';

            if ($order->updatePaymentStatus($id, $paymentStatus)) {
                $message = 'Payment status updated successfully!';
            } else {
                $error = 'Failed to update payment status.';
            }
            break;

        case 'update_tracking':
            $id = intval($_POST['id'] ?? 0);
            $trackingNumber = $_POST['tracking_number'] ?? '';

            if ($order->updateTracking($id, $trackingNumber)) {
                $message = 'Tracking number updated successfully!';
            } else {
                $error = 'Failed to update tracking number.';
            }
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($order->delete($id)) {
                $message = 'Order deleted successfully!';
            } else {
                $error = 'Failed to delete order.';
            }
            break;
    }
}

// Link past guest orders to user accounts
$order->backfillMissingUserIds();

// Get all orders for display
$orders = $order->getAll();
$stats = $order->getStatistics();

function orderCustomerName(array $orderItem, ?array $orderUser): string {
    if ($orderUser) {
        return $orderUser['name'];
    }
    $ship = $orderItem['shipping_address'] ?? [];
    $name = trim(($ship['first_name'] ?? '') . ' ' . ($ship['last_name'] ?? ''));
    return $name !== '' ? $name : 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders Management - Muscle Bull</title>

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
            <a href="dashboard.php" class="nav-link">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="products.php" class="nav-link">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="orders.php" class="nav-link active">
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
            <h1><i class="fa-solid fa-shopping-cart me-2"></i>Orders Management</h1>
            <p class="mb-0">Manage customer orders and fulfillment</p>
        </div>

        <!-- Messages -->
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

        <!-- Statistics Cards -->
        <div class="d-flex row justify-content-between">
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-warning"><?php echo $stats['pending']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-info"><?php echo $stats['shipped']; ?></div>
                    <div class="stat-label">Shipped</div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-success"><?php echo $stats['delivered']; ?></div>
                    <div class="stat-label">Delivered</div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-danger"><?php echo $stats['cancelled']; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>

            </div>

            <div class="col-md-2 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number">LKR <?php echo number_format($stats['total_revenue']); ?></div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>

        </div>

        <!-- Orders Table -->
        <div class="table-responsive mb-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">All Orders (<?php echo count($orders); ?> orders)</h3>
            <table class="table order-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order_item): ?>
                        <tr>
                            <td>
                                <div class="fw-bold">#<?php echo $order_item['id']; ?></div>
                                <small
                                    class="text-muted"><?php echo date('M j, Y', strtotime($order_item['order_date'])); ?></small>
                            </td>
                            <td>
                                <?php
                                $orderUser = $user->getById($order_item['user_id']);
                                echo htmlspecialchars(orderCustomerName($order_item, $orderUser));
                                ?>
                            </td>
                            <td>
                                <div class="order-items">
                                    <?php foreach (array_slice($order_item['items'], 0, 2) as $item): ?>
                                        <div class="order-item">
                                            <?php echo htmlspecialchars($item['name']); ?> ×
                                            <?php echo $item['quantity']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($order_item['items']) > 2): ?>
                                        <div class="text-muted">+<?php echo count($order_item['items']) - 2; ?> more</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">LKR <?php echo number_format($order_item['total_amount']); ?></div>
                                <small class="text-muted"><?php echo ucfirst($order_item['payment_method']); ?></small>
                            </td>
                            <td>
                                <?php
                                $statusClass = $order_item['status'] === 'delivered' ? 'success' :
                                    ($order_item['status'] === 'shipped' ? 'info' :
                                        ($order_item['status'] === 'pending' ? 'warning' : 'danger'));
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?> badge-status">
                                    <?php echo ucfirst($order_item['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $paymentClass = $order_item['payment_status'] === 'paid' ? 'success' : 'warning';
                                ?>
                                <span class="badge bg-<?php echo $paymentClass; ?> badge-status">
                                    <?php echo ucfirst($order_item['payment_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order_item['order_date'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-action"
                                        onclick="viewOrder(<?php echo $order_item['id']; ?>)" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-action"
                                        onclick="editOrder(<?php echo $order_item['id']; ?>)" title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <form method="POST" action="orders.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $order_item['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-action"
                                            onclick="return confirm('Are you sure you want to delete this order?')"
                                            title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Details Modal (Hidden by default) -->
        <div id="orderModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="orderDetails">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Order Form (Hidden by default) -->
        <div id="editForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Edit Order</h3>
            <form method="POST" action="orders.php" id="editOrderForm">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Order Status</label>
                        <select class="form-select" name="status" id="editStatus">
                            <option value="pending">Pending</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Payment Status</label>
                        <select class="form-select" name="payment_status" id="editPaymentStatus">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" id="editTrackingNumber">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Order Notes</label>
                        <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Update Order
                        </button>
                        <button type="button" class="btn btn-outline-warning text-uppercase fw-bold ms-2"
                            onclick="updatePaymentOnly()">
                            <i class="fa-solid fa-credit-card me-2"></i>Update Payment Only
                        </button>
                        <button type="button" class="btn btn-outline-info text-uppercase fw-bold ms-2"
                            onclick="updateTrackingOnly()">
                            <i class="fa-solid fa-truck me-2"></i>Update Tracking Only
                        </button>
                        <button type="button" class="btn btn-outline-dark text-uppercase fw-bold ms-2"
                            onclick="cancelEdit()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store orders data for JavaScript
        const orders = <?php echo json_encode($orders); ?>;
        const users = <?php echo json_encode($user->getAll()); ?>;

        function orderCustomerLabel(order, orderUser) {
            if (orderUser) {
                return {
                    name: orderUser.name,
                    email: orderUser.email,
                };
            }
            const ship = order.shipping_address || {};
            return {
                name: [ship.first_name, ship.last_name].filter(Boolean).join(' ') || 'Guest',
                email: ship.email || 'N/A',
                phone: ship.phone || '',
            };
        }

        function viewOrder(id) {
            const order = orders.find(o => Number(o.id) === Number(id));
            if (!order) {
                return;
            }

            const orderUser = order.user_id
                ? users.find(u => Number(u.id) === Number(order.user_id))
                : null;
            const customer = orderCustomerLabel(order, orderUser);

            let itemsHtml = '';
                order.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.name}</td>
                            <td>LKR ${item.price}</td>
                            <td>${item.quantity}</td>
                            <td>LKR ${item.subtotal}</td>
                        </tr>
                    `;
                });

                const detailsHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p><strong>Order #:</strong> ${order.id}</p>
                            <p><strong>Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</p>
                            <p><strong>Status:</strong> ${order.status}</p>
                            <p><strong>Payment Status:</strong> ${order.payment_status}</p>
                            <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                            ${order.tracking_number ? `<p><strong>Tracking:</strong> ${order.tracking_number}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p><strong>Name:</strong> ${customer.name}</p>
                            <p><strong>Email:</strong> ${customer.email}</p>
                            ${customer.phone ? `<p><strong>Phone:</strong> ${customer.phone}</p>` : ''}
                            <h6 class="mt-3">Shipping Address</h6>
                            <p>${order.shipping_address.address || order.shipping_address.street}<br>
                            ${order.shipping_address.city}, ${order.shipping_address.postal_code}<br>
                            ${order.shipping_address.country}</p>
                        </div>
                    </div>
                    <h6 class="mt-4">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total Amount:</th>
                                    <th>LKR ${order.total_amount}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    ${order.notes ? `<h6 class="mt-3">Notes</h6><p>${order.notes}</p>` : ''}
                `;

            document.getElementById('orderDetails').innerHTML = detailsHtml;
            const modalEl = document.getElementById('orderModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function editOrder(id) {
            const order = orders.find(o => Number(o.id) === Number(id));
            if (order) {
                document.getElementById('editId').value = order.id;
                document.getElementById('editStatus').value = order.status;
                document.getElementById('editPaymentStatus').value = order.payment_status;
                document.getElementById('editTrackingNumber').value = order.tracking_number || '';
                document.getElementById('editNotes').value = order.notes || '';

                document.getElementById('editForm').style.display = 'block';
                document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
            }
        }

        function cancelEdit() {
            document.getElementById('editOrderForm').reset();
            document.getElementById('editForm').style.display = 'none';
        }

        function updatePaymentOnly() {
            const form = document.getElementById('editOrderForm');
            const id = document.getElementById('editId').value;
            const paymentStatus = document.getElementById('editPaymentStatus').value;

            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = 'orders.php';

            tempForm.innerHTML = `
                <input type="hidden" name="action" value="update_payment">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="payment_status" value="${paymentStatus}">
            `;

            document.body.appendChild(tempForm);
            tempForm.submit();
        }

        function updateTrackingOnly() {
            const form = document.getElementById('editOrderForm');
            const id = document.getElementById('editId').value;
            const trackingNumber = document.getElementById('editTrackingNumber').value;

            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = 'orders.php';

            tempForm.innerHTML = `
                <input type="hidden" name="action" value="update_tracking">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="tracking_number" value="${trackingNumber}">
            `;

            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    </script>
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
<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/GiftCard.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$giftCard = new GiftCard();
$user = new User();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $code = $_POST['code'] ?? '';
            $amount = floatval($_POST['amount'] ?? 0);
            $recipientEmail = $_POST['recipient_email'] ?? '';
            $message_text = $_POST['message'] ?? '';
            $expiryDate = $_POST['expiry_date'] ?? '';
            
            if ($giftCard->add($code, $amount, $recipientEmail, $message_text, $expiryDate)) {
                $message = 'Gift card added successfully!';
            } else {
                $error = 'Failed to add gift card. Code might already exist.';
            }
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $code = $_POST['code'] ?? '';
            $amount = floatval($_POST['amount'] ?? 0);
            $recipientEmail = $_POST['recipient_email'] ?? '';
            $message_text = $_POST['message'] ?? '';
            $expiryDate = $_POST['expiry_date'] ?? '';
            $status = $_POST['status'] ?? '';
            
            if ($giftCard->update($id, $code, $amount, $recipientEmail, $message_text, $expiryDate, $status)) {
                $message = 'Gift card updated successfully!';
            } else {
                $error = 'Failed to update gift card.';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($giftCard->delete($id)) {
                $message = 'Gift card deleted successfully!';
            } else {
                $error = 'Failed to delete gift card.';
            }
            break;
    }
}

// Get all gift cards for display
$giftCards = $giftCard->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gift Cards Management - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <!-- FontAwesome for icons -->
    <!-- Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet" />
    <!-- FontAwesome for icons -->
    <!-- Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
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
            <a href="orders.php" class="nav-link">
                <i class="fa-solid fa-shopping-cart"></i> Orders
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="categories.php" class="nav-link">
                <i class="fa-solid fa-tags"></i> Categories
            </a>
            <a href="gift-cards.php" class="nav-link active">
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
            <h1><i class="fa-solid fa-gift me-2"></i>Gift Cards Management</h1>
            <p class="mb-0">Manage gift cards and vouchers</p>
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

        <!-- Add Gift Card Form -->
        <div class="form-section">
            <h3 class="h4 text-uppercase fw-bold mb-4">Add New Gift Card</h3>
            <form method="POST" action="gift-cards.php">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gift Card Code</label>
                        <input type="text" class="form-control" name="code" required 
                               placeholder="e.g., GC12345678">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Amount (LKR)</label>
                        <input type="number" class="form-control" name="amount" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recipient Email</label>
                        <input type="email" class="form-control" name="recipient_email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="2" 
                                  placeholder="Personal message for the recipient"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-plus me-2"></i>Add Gift Card
                        </button>
                        <button type="button" class="btn btn-outline-secondary text-uppercase fw-bold ms-2" 
                                onclick="generateCode()">
                            <i class="fa-solid fa-magic me-2"></i>Generate Code
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Gift Cards Table -->
        <div class="table-responsive mb-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">Manage Gift Cards (<?php echo count($giftCards); ?> cards)</h3>
            <table class="table gift-card-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($giftCards as $card): ?>
                        <tr>
                            <td><span class="code-display"><?php echo htmlspecialchars($card['code']); ?></span></td>
                            <td>LKR <?php echo number_format($card['amount']); ?></td>
                            <td>LKR <?php echo number_format($card['balance']); ?></td>
                            <td><?php echo htmlspecialchars($card['recipient_email']); ?></td>
                            <td>
                                <?php
                                $statusClass = $card['status'] === 'active' ? 'success' : 
                                             ($card['status'] === 'used' ? 'danger' : 'secondary');
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?> badge-status">
                                    <?php echo ucfirst($card['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($card['expiry_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($card['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-action" 
                                            onclick="editGiftCard(<?php echo $card['id']; ?>)"
                                            title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <form method="POST" action="gift-cards.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-action" 
                                                onclick="return confirm('Are you sure you want to delete this gift card?')"
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

        <!-- Edit Gift Card Form (Hidden by default) -->
        <div id="editForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Edit Gift Card</h3>
            <form method="POST" action="gift-cards.php" id="editGiftCardForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gift Card Code</label>
                        <input type="text" class="form-control" name="code" id="editCode" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Amount (LKR)</label>
                        <input type="number" class="form-control" name="amount" id="editAmount" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recipient Email</label>
                        <input type="email" class="form-control" name="recipient_email" id="editRecipientEmail" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" id="editExpiryDate" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Message</label>
                        <textarea class="form-control" name="message" id="editMessage" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="editStatus">
                            <option value="active">Active</option>
                            <option value="used">Used</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Current Balance</label>
                        <input type="text" class="form-control" value="LKR <?php echo number_format($card['balance'] ?? 0); ?>" readonly>
                        <small class="text-muted">Balance changes when card is used</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Update Gift Card
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

    
    <script>
        // Store gift cards data for JavaScript
        const giftCards = <?php echo json_encode($giftCards); ?>;
        
        function editGiftCard(id) {
            const card = giftCards.find(c => c.id === id);
            if (card) {
                document.getElementById('editId').value = card.id;
                document.getElementById('editCode').value = card.code;
                document.getElementById('editAmount').value = card.amount;
                document.getElementById('editRecipientEmail').value = card.recipient_email;
                document.getElementById('editExpiryDate').value = card.expiry_date;
                document.getElementById('editMessage').value = card.message;
                document.getElementById('editStatus').value = card.status;
                
                document.getElementById('editForm').style.display = 'block';
                document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function cancelEdit() {
            document.getElementById('editGiftCardForm').reset();
            document.getElementById('editForm').style.display = 'none';
        }
        
        function generateCode() {
            const code = 'GC' + Math.random().toString(36).substr(2, 8).toUpperCase();
            document.querySelector('input[name="code"]').value = code;
        }
        
        // Set minimum date to today for expiry date
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="expiry_date"]').setAttribute('min', today);
        document.querySelector('#editExpiryDate').setAttribute('min', today);
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

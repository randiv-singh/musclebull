<?php
session_start();
require_once '../classes/User.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$user = new User();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'customer';
            
            if ($user->add($name, $email, $password, $role)) {
                $message = 'User added successfully!';
            } else {
                $error = 'Failed to add user. Email might already exist.';
            }
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'customer';
            $status = $_POST['status'] ?? 'active';
            
            if ($user->update($id, $name, $email, $role, $status)) {
                $message = 'User updated successfully!';
            } else {
                $error = 'Failed to update user.';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($user->delete($id)) {
                $message = 'User deleted successfully!';
            } else {
                $error = 'Failed to delete user.';
            }
            break;
            
        case 'reset_password':
            $id = intval($_POST['id'] ?? 0);
            $newPassword = $_POST['new_password'] ?? '';
            
            if ($user->updatePassword($id, $newPassword)) {
                $message = 'Password reset successfully!';
            } else {
                $error = 'Failed to reset password.';
            }
            break;
    }
}

// Get all users for display
$users = $user->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Users Management - Muscle Bull</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin-bootstrap.css" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom Admin CSS -->
    
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
            <a href="users.php" class="nav-link active">
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
            <h1><i class="fa-solid fa-users me-2"></i>Users Management</h1>
            <p class="mb-0">Manage user accounts and permissions</p>
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

        <!-- Add User Form -->
        <div class="form-section">
            <h3 class="h4 text-uppercase fw-bold mb-4">Add New User</h3>
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="add">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-user-plus me-2"></i>Add User
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="table-responsive mb-5 mt-5">
            <h3 class="h4 text-uppercase fw-bold mb-4">Manage Users (<?php echo count($users); ?> users)</h3>
            <table class="table user-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user_item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2">
                                        <?php echo substr($user_item['name'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($user_item['name']); ?></div>
                                        <small class="text-muted">ID: <?php echo $user_item['id']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user_item['email']); ?></td>
                            <td>
                                <?php if ($user_item['role'] === 'admin'): ?>
                                    <span class="badge bg-danger badge-status">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-primary badge-status">Customer</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user_item['status'] === 'active'): ?>
                                    <span class="badge bg-success badge-status">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-status">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user_item['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-action" 
                                            onclick="editUser(<?php echo $user_item['id']; ?>)"
                                            title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-action" 
                                            onclick="resetPassword(<?php echo $user_item['id']; ?>)"
                                            title="Reset Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                    <?php if ($user_item['id'] != $_SESSION['admin_user']['id']): ?>
                                        <form method="POST" action="users.php" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $user_item['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-action" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit User Form (Hidden by default) -->
        <div id="editForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Edit User</h3>
            <form method="POST" action="users.php" id="editUserForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Role</label>
                        <select class="form-select" name="role" id="editRole" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark text-uppercase fw-bold">
                            <i class="fa-solid fa-save me-2"></i>Update User
                        </button>
                        <button type="button" class="btn btn-outline-dark text-uppercase fw-bold ms-2" 
                                onclick="cancelEdit()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reset Password Form (Hidden by default) -->
        <div id="passwordForm" class="form-section" style="display: none;">
            <h3 class="h4 text-uppercase fw-bold mb-4">Reset Password</h3>
            <form method="POST" action="users.php" id="resetPasswordForm">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="id" id="passwordId">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="newPassword" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning text-uppercase fw-bold">
                            <i class="fa-solid fa-key me-2"></i>Reset Password
                        </button>
                        <button type="button" class="btn btn-outline-dark text-uppercase fw-bold ms-2" 
                                onclick="cancelPasswordReset()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <script>
        // Store users data for JavaScript
        const users = <?php echo json_encode($users); ?>;
        
        function editUser(id) {
            const user = users.find(u => u.id === id);
            if (user) {
                document.getElementById('editId').value = user.id;
                document.getElementById('editName').value = user.name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editRole').value = user.role;
                document.getElementById('editStatus').value = user.status;
                
                document.getElementById('editForm').style.display = 'block';
                document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function cancelEdit() {
            document.getElementById('editUserForm').reset();
            document.getElementById('editForm').style.display = 'none';
        }
        
        function resetPassword(id) {
            const user = users.find(u => u.id === id);
            if (user) {
                document.getElementById('passwordId').value = user.id;
                document.getElementById('passwordForm').style.display = 'block';
                document.getElementById('passwordForm').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function cancelPasswordReset() {
            document.getElementById('resetPasswordForm').reset();
            document.getElementById('passwordForm').style.display = 'none';
        }
        
        // Validate password confirmation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
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

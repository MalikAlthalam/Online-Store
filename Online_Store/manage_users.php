<?php
require "config.php";
require "auth.php";

// Require admin access
requireAdmin();

$message = "";

// Handle user actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'delete':
                    $user_id = (int)$_POST['user_id'];
                    if ($user_id !== $_SESSION['user_id']) { // Prevent self-deletion
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
                        $stmt->execute([$user_id]);
                        $message = "✅ User deleted successfully!";
                    } else {
                        $message = "❌ Cannot delete your own account!";
                    }
                    break;
                    
                case 'toggle_status':
                    $user_id = (int)$_POST['user_id'];
                    if ($user_id !== $_SESSION['user_id']) { // Prevent self-deactivation
                        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ? AND role != 'admin'");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetch();
                        
                        if ($user) {
                            $new_status = $user['is_active'] ? 0 : 1;
                            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                            $stmt->execute([$new_status, $user_id]);
                            $message = $new_status ? "✅ User activated!" : "✅ User deactivated!";
                        }
                    } else {
                        $message = "❌ Cannot modify your own account!";
                    }
                    break;
                    
                case 'change_role':
                    $user_id = (int)$_POST['user_id'];
                    $new_role = $_POST['new_role'];
                    
                    if ($user_id !== $_SESSION['user_id'] && $new_role !== 'admin') {
                        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
                        $stmt->execute([$new_role, $user_id]);
                        $message = "✅ User role updated!";
                    } else {
                        $message = "❌ Cannot change admin roles or your own account!";
                    }
                    break;
            }
        } catch (PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
        }
    }
}

// Get all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $message = "❌ Error loading users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emad Store - Manage Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f5f5f5 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white !important;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .nav-links {
            margin-top: 20px;
        }
        
        .nav-links a {
            margin-right: 15px;
            padding: 10px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .nav-links a:hover {
            background: #5a6fd8;
        }
        
        .logout-link {
            color: #dc3545 !important;
            background: transparent !important;
            border: 1px solid #dc3545 !important;
        }
        
        .logout-link:hover {
            background: #dc3545 !important;
            color: white !important;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .users-table {
            background: white !important;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        
        .users-table h2 {
            padding: 20px 30px;
            margin: 0;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px 30px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .role {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .role.admin {
            background: #fff3cd;
            color: #856404;
        }
        
        .role.user {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Manage Users</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
            
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="home.php">View Store</a>
                <a href="manage_products.php">Manage Products</a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div class="users-table">
            <h2>All Users (<?= count($users) ?> total)</h2>
            
            <?php if (count($users) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="role admin">Admin</span>
                            <?php else: ?>
                                <span class="role user">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="status active">Active</span>
                            <?php else: ?>
                                <span class="status inactive">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id'] && $user['role'] !== 'admin'): ?>
                                <!-- Toggle Status -->
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-warning">
                                        <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                                
                                <!-- Delete User -->
                                <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">
                                    <?= $user['id'] === $_SESSION['user_id'] ? 'Your Account' : 'Protected' ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: #666;">
                    <p>No users found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

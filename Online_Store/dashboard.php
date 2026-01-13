<?php
require "config.php";
require "auth.php";

// Require admin access
requireAdmin();

// Get statistics
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $total_products = $stmt->fetch()['total'];
    
    // Total orders/cart items
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cart");
    $total_orders = $stmt->fetch()['total'];
    
    // Recent users
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll();
    
    // Recent products
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
    $recent_products = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $total_users = $total_products = $total_orders = 0;
    $recent_users = $recent_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emad Store - Admin Dashboard</title>
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
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background: white !important;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
        }
        
        .dashboard-header h1 {
            margin: 0;
            color: #333;
        }
        
        .dashboard-header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white !important;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            text-align: center;
            border: 1px solid #e0e0e0;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 2rem;
        }
        
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 1.1rem;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        
        .content-card {
            background: white !important;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
        }
        
        .content-card h3 {
            margin: 0 0 20px 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .user-item, .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .user-item:last-child, .product-item:last-child {
            border-bottom: none;
        }
        
        .user-info, .product-info {
            flex: 1;
        }
        
        .user-info h4, .product-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .user-info p, .product-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .nav-links {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>🛍️ Admin Dashboard</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
            
            <div class="nav-links">
                <a href="home.php">View Store</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="manage_products.php">Manage Products</a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $total_users ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_products ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_orders ?></h3>
                <p>Cart Items</p>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="content-card">
                <h3>Recent Users</h3>
                <?php if (count($recent_users) > 0): ?>
                    <?php foreach ($recent_users as $user): ?>
                    <div class="user-item">
                        <div class="user-info">
                            <h4><?= htmlspecialchars($user['username']) ?></h4>
                            <p><?= htmlspecialchars($user['email']) ?> • <?= ucfirst($user['role']) ?></p>
                        </div>
                        <div>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="btn btn-secondary">Admin</span>
                            <?php else: ?>
                                <a href="toggle_user.php?id=<?= $user['id'] ?>" class="btn btn-primary">
                                    <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </div>
            
            <div class="content-card">
                <h3>Recent Products</h3>
                <?php if (count($recent_products) > 0): ?>
                    <?php foreach ($recent_products as $product): ?>
                    <div class="product-item">
                        <div class="product-info">
                            <h4><?= htmlspecialchars($product['name']) ?></h4>
                            <p>$<?= number_format($product['price'], 2) ?> • <?= ucfirst($product['category']) ?></p>
                        </div>
                        <div>
                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary">Edit</a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

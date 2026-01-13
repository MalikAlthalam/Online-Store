<?php
require "config.php";
require "auth.php";

requireAdmin();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $name = trim($_POST['name']);
                    $description = trim($_POST['description']);
                    $price = floatval($_POST['price']);
                    $discount = floatval($_POST['discount']);
                    $category = $_POST['category'];
                    $image = trim($_POST['image']);
                    
                    if (empty($name) || empty($price) || empty($category) || empty($image)) {
                        $message = "❌ Please fill in all required fields!";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, discount, category, image) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $description, $price, $discount, $category, $image]);
                        $message = "✅ Product added successfully!";
                    }
                    break;
                    
                case 'edit':
                    $id = (int)$_POST['product_id'];
                    $name = trim($_POST['name']);
                    $description = trim($_POST['description']);
                    $price = floatval($_POST['price']);
                    $discount = floatval($_POST['discount']);
                    $category = $_POST['category'];
                    $image = trim($_POST['image']);
                    
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, discount = ?, category = ?, image = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $discount, $category, $image, $id]);
                    $message = "✅ Product updated successfully!";
                    break;
                    
                case 'delete':
                    $id = (int)$_POST['product_id'];
                    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = "✅ Product deleted successfully!";
                    break;
            }
        } catch (PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
        }
    }
}

// Get all products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    $message = "❌ Error loading products: " . $e->getMessage();
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emad Store - Manage Products</title>
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
            max-width: 1400px;
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
        
        .form-section {
            background: white !important;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
        }
        
        .form-section h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .products-table {
            background: white !important;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        
        .products-table h2 {
            padding: 20px 30px;
            margin: 0;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .product-category {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 18px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .product-discount {
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .product-actions {
            margin-top: 15px;
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Manage Products</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
            
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="home.php">View Store</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <!-- Add/Edit Product Form -->
        <div class="form-section">
            <h2><?= $edit_product ? 'Edit Product' : 'Add New Product' ?></h2>
            <form method="post">
                <input type="hidden" name="action" value="<?= $edit_product ? 'edit' : 'add' ?>">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="product_id" value="<?= $edit_product['id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" value="<?= $edit_product ? htmlspecialchars($edit_product['name']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="men" <?= $edit_product && $edit_product['category'] === 'men' ? 'selected' : '' ?>>Men</option>
                            <option value="women" <?= $edit_product && $edit_product['category'] === 'women' ? 'selected' : '' ?>>Women</option>
                            <option value="kids" <?= $edit_product && $edit_product['category'] === 'kids' ? 'selected' : '' ?>>Kids</option>
                            <option value="accessories" <?= $edit_product && $edit_product['category'] === 'accessories' ? 'selected' : '' ?>>Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $edit_product ? $edit_product['price'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="discount">Discount (%)</label>
                        <input type="number" id="discount" name="discount" step="0.01" min="0" max="100" value="<?= $edit_product ? $edit_product['discount'] : '0' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image">Image Path *</label>
                    <input type="text" id="image" name="image" value="<?= $edit_product ? htmlspecialchars($edit_product['image']) : '' ?>" required placeholder="e.g., images/men/1.png">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Product description..."><?= $edit_product ? htmlspecialchars($edit_product['description']) : '' ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?= $edit_product ? 'Update Product' : 'Add Product' ?>
                </button>
                
                <?php if ($edit_product): ?>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Products List -->
        <div class="products-table">
            <h2>All Products (<?= count($products) ?> total)</h2>
            
            <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-category"><?= ucfirst($product['category']) ?></div>
                        
                        <div class="product-price">
                            $<?= number_format($product['price'] * (1 - $product['discount']/100), 2) ?>
                            <?php if($product['discount'] > 0): ?>
                                <span class="product-discount">-<?= $product['discount'] ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <a href="manage_products.php?edit=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: #666;">
                    <p>No products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

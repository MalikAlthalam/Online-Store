<?php
require "config.php";

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo "✅ Admin user already exists!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Email: admin@emadstore.com<br>";
    } else {
        // Create admin user with pre-hashed password
        $admin_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // admin123
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@emadstore.com', $admin_password, 'admin', 1]);
        
        echo "✅ Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Email: admin@emadstore.com<br>";
        echo "<br><a href='login.php'>Go to Login Page</a>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error creating admin user: " . $e->getMessage();
}
?>

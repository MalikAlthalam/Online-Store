<?php
require "config.php";
require "auth.php";

// Require admin access
requireAdmin();

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    try {
        // Get current user status
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();
        
        if ($user && $user['role'] !== 'admin') {
            // Toggle user status
            $new_status = $user['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = :status WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $user_id]);
            
            $message = $new_status ? "User activated successfully!" : "User deactivated successfully!";
        } else {
            $message = "Cannot modify admin user or user not found!";
        }
    } catch (PDOException $e) {
        $message = "Error updating user status!";
        error_log("Toggle user error: " . $e->getMessage());
    }
} else {
    $message = "Invalid user ID!";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit;
?>

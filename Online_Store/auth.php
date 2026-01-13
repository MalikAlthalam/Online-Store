<?php
require "config.php";

// Start session
session_start();

// Function to check if user is logged in
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        // Check for remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            global $pdo;
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = :token AND is_active = 1");
                $stmt->execute(['token' => $_COOKIE['remember_token']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Recreate session from remember token
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    return true;
                } else {
                    // Invalid remember token, clear cookie
                    setcookie('remember_token', '', time() - 3600, '/');
                }
            } catch (PDOException $e) {
                error_log("Remember me check error: " . $e->getMessage());
            }
        }
        
        // Redirect to main page if not logged in
        header("Location: index.php");
        exit;
    }
    return true;
}

// Function to check if user is admin
function checkAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: home.php");
        exit;
    }
    return true;
}

// Function to require authentication (call this at top of protected pages)
function requireAuth() {
    checkAuth();
}

// Function to require admin access
function requireAdmin() {
    requireAuth();
    checkAdmin();
}

// Function to generate remember token
function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

// Function to set remember me cookie
function setRememberCookie($token) {
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
    setcookie('remember_token', $token, $expiry, '/', '', false, true); // HttpOnly for security
}

// Function to clear remember me cookie
function clearRememberCookie() {
    setcookie('remember_token', '', time() - 3600, '/');
}
?>

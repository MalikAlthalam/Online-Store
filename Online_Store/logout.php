<?php
require "auth.php";

// Clear remember me cookie
clearRememberCookie();

// Clear remember token from database if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = :user_id");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
    } catch (PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// If sessions are being used, destroy them
session_unset();
session_destroy();

// Redirect to main page
header("Location: index.php");
exit;
?>

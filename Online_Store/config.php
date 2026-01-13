<?php
// Database configuration
$host = "localhost";
$dbname = "project_db";
$username = "root";   // change if needed
$password = "";       // change if needed

try {
    // Simple PDO connection with basic options
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Database helper class for common operations


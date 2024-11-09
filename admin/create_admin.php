<?php
require_once '../config/database.php';

// Create a new admin account
$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // First, clear existing admin accounts
    $pdo->exec("DELETE FROM admin");
    
    // Insert new admin account
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);
    
    echo "Admin account created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "You can now delete this file for security.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
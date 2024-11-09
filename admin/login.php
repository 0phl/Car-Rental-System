<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/database.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Debug information (remove in production)
    if (!$admin) {
        $error = "Username not found";
    } elseif (!password_verify($password, $admin['password'])) {
        $error = "Invalid password";
    } else {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CarRental</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            overflow: hidden; /* Hide the scrollbar */
        }
    </style>
</head>
<body>
    <!-- Include the navbar -->
    <?php require_once 'includes/navbar.php'; ?>

    <div class="admin-login" style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div class="login-container">
            <div class="login-card" style="margin-top: -100px;">
                <h2>Admin Login</h2>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 
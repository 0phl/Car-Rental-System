<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

function isCurrentPage($page) {
    return strpos($_SERVER['PHP_SELF'], $page) !== false ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - CarRental Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <a href="dashboard.php" class="sidebar-logo">CarRental Admin</a>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="<?php echo isCurrentPage('dashboard.php'); ?>">Dashboard</a>
                <a href="cars.php" class="<?php echo isCurrentPage('cars.php'); ?>">Cars</a>
                <a href="bookings.php" class="<?php echo isCurrentPage('bookings.php'); ?>">Bookings</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <?php echo $content; ?>
        </main>
    </div>
</body>
</html> 
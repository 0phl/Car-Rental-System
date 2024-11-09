<nav class="navbar">
    <div class="container">
        <a href="../index.php" class="logo">CarRental</a>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../check-booking.php">Check Booking</a>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
                <a href="admin/login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
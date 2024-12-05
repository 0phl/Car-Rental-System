<?php
require_once 'config/database.php';

if (!isset($_GET['ref'])) {
    header('Location: index.php');
    exit();
}

$ref = $_GET['ref'];
$stmt = $pdo->prepare("SELECT b.*, c.brand, c.model, c.cost_per_day 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE b.reference_no = ?");
$stmt->execute([$ref]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">CarRental</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="check-booking.php">Check Booking</a>
            </div>
        </div>
    </nav>

    <main class="container confirmation-page">
        <div class="confirmation-card">
            <div class="success-icon">✓</div>
            <h2>Booking Confirmed!</h2>
            <p class="reference">Reference Number: <strong><?php echo htmlspecialchars($ref); ?></strong></p>
            
            <div class="booking-details">
                <h3>Booking Details</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="label">Car:</span>
                        <span class="value"><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Pickup:</span>
                        <span class="value"><?php echo date('M d, Y h:i A', strtotime($booking['pickup_datetime'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Return:</span>
                        <span class="value"><?php echo date('M d, Y h:i A', strtotime($booking['return_datetime'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Location:</span>
                        <span class="value"><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
            </div>

            <p class="note"><strong>Please save your reference number to check your booking status later.</strong></p>
            
            <div class="actions">
                <a href="index.php" class="btn-secondary">Back to Home</a>
                <a href="check-booking.php?ref=<?php echo urlencode($ref); ?>" class="btn-primary">View Booking</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CarRental. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 
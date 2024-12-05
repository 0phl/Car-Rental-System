<?php
require_once 'config/database.php';

$booking = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['ref'])) {
    $ref = isset($_GET['ref']) ? $_GET['ref'] : $_POST['reference_no'];
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    
    $stmt = $pdo->prepare("SELECT b.*, c.brand, c.model, c.image, c.cost_per_day 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.id 
        WHERE b.reference_no = ? " . 
        (isset($_POST['email']) ? "AND b.email = ?" : ""));
    
    $params = isset($_POST['email']) ? [$ref, $email] : [$ref];
    $stmt->execute($params);
    $booking = $stmt->fetch();
    
    if (!$booking && isset($_POST['email'])) {
        $error = "No booking found with the provided details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Booking - CarRental</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/check-booking-style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="index.php" class="logo">
                    DGMT CarRental
                    <i class="fas fa-car"></i>
                </a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="check-booking.php">Check Booking</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="confirmation-page">
        <div class="container">
            <div class="confirmation-card">
                <?php if (!$booking): ?>
                    <div class="search-booking">
                        <h2>Check Your Booking</h2>
                        <?php if ($error): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" class="booking-form">
                            <div class="form-group">
                                <label for="reference_no">Reference Number</label>
                                <input type="text" id="reference_no" name="reference_no" required 
                                    placeholder="Enter your booking reference">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required 
                                    placeholder="Enter your email address">
                            </div>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-search"></i>
                                Check Booking
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="booking-details">
                        <div class="car-info">
                            <img src="<?php echo htmlspecialchars($booking['image']); ?>" 
                                alt="<?php echo htmlspecialchars($booking['model']); ?>">
                            <div class="info">
                                <h2><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h2>
                                <p class="status <?php echo strtolower($booking['status']); ?>">
                                    <i class="fas fa-circle"></i>
                                    <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="label">Reference Number</span>
                                <span class="value">
                                    <i class="fas fa-hashtag"></i>
                                    <?php echo htmlspecialchars($booking['reference_no']); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Client Name</span>
                                <span class="value">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($booking['client_name']); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Pickup Date</span>
                                <span class="value">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($booking['pickup_datetime'])); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Return Date</span>
                                <span class="value">
                                    <i class="fas fa-calendar-check"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($booking['return_datetime'])); ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Location</span>
                                <span class="value">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($booking['location']); ?>
                                </span>
                            </div>
                        </div>
                    
                        <div class="actions">
                            <a href="check-booking.php" class="btn-secondary">
                                <i class="fas fa-search"></i>
                                Check Another Booking
                            </a>
                            <a href="generate-invoice.php?ref=<?php echo urlencode($booking['reference_no']); ?>" 
                               class="btn-primary" target="_blank">
                                <i class="fas fa-file-invoice"></i>
                                Generate Invoice
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 DGMT CarRental. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
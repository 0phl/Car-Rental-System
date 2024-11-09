<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$car_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND status = 'available'");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate reference number
    $reference_no = 'RNT' . date('Ymd') . rand(1000, 9999);
    
    $stmt = $pdo->prepare("INSERT INTO bookings (car_id, client_name, email, contact_no, pickup_datetime, 
        return_datetime, location, reference_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $car_id,
        $_POST['client_name'],
        $_POST['email'],
        $_POST['contact_no'],
        $_POST['pickup_datetime'],
        $_POST['return_datetime'],
        $_POST['location'],
        $reference_no
    ]);

    // Update car status
    $stmt = $pdo->prepare("UPDATE cars SET status = 'booked' WHERE id = ?");
    $stmt->execute([$car_id]);

    header("Location: booking-confirmation.php?ref=" . $reference_no);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car - <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></title>
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

    <main class="container booking-page">
        <div class="booking-container">
            <div class="car-summary">
                <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>">
                <div class="car-info">
                    <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                    <p class="price">$<?php echo htmlspecialchars($car['cost_per_day']); ?> per day</p>
                    <div class="features">
                        <p>Seats: <?php echo htmlspecialchars($car['seats']); ?></p>
                        <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                        <p>Fuel Type: <?php echo htmlspecialchars($car['fuel_type']); ?></p>
                    </div>
                </div>
            </div>

            <form method="POST" class="booking-form">
                <h3>Booking Details</h3>
                
                <div class="form-group">
                    <label for="client_name">Full Name</label>
                    <input type="text" id="client_name" name="client_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="tel" id="contact_no" name="contact_no" required>
                </div>

                <div class="form-group">
                    <label for="pickup_datetime">Pickup Date & Time</label>
                    <input type="datetime-local" id="pickup_datetime" name="pickup_datetime" required>
                </div>

                <div class="form-group">
                    <label for="return_datetime">Return Date & Time</label>
                    <input type="datetime-local" id="return_datetime" name="return_datetime" required>
                </div>

                <div class="form-group">
                    <label for="location">Pickup Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <button type="submit" class="btn-submit">Confirm Booking</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CarRental. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 
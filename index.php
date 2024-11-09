<?php
require_once 'config/database.php';

// Fetch available cars
$stmt = $pdo->query("SELECT * FROM cars WHERE status = 'available'");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Call the Navbar -->
<?php require_once("admin/includes/navbar.php")?>

    <header class="hero">
        <div class="container">
            <h1>Find Your Perfect Rental Car</h1>
            <p>Choose from our wide selection of vehicles</p>
        </div>
    </header>

    <main class="container">
        <section class="cars-grid">
            <?php foreach($cars as $car): ?>
            <div class="car-card">
                <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>">
                <div class="car-details">
                    <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                    <p class="price">$<?php echo htmlspecialchars($car['cost_per_day']); ?> per day</p>
                    <ul class="features">
                        <li>Seats: <?php echo htmlspecialchars($car['seats']); ?></li>
                        <li>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></li>
                        <li>Fuel Type: <?php echo htmlspecialchars($car['fuel_type']); ?></li>
                    </ul>
                    <a href="book.php?id=<?php echo $car['id']; ?>" class="btn-book">Book Now</a>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CarRental. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 
<?php
require_once 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch available cars
$stmt = $pdo->prepare("SELECT * FROM cars WHERE status = 'available' AND (brand LIKE :search OR model LIKE :search)");
$stmt->execute(['search' => '%' . $search . '%']);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DGMT CarRental</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Call the Navbar -->
<?php require_once("admin/includes/navbar.php")?>

    <header class="hero">
        <div class="container">
            <h1>Affordable Rates, Reliable Cars.</h1>
            <p>Choose from a wide selection of vehicles for all occasions.</p>
            <div class="hero-buttons">
                <a href="#cars" class="btn-primary">View Our Fleet</a>
                <a href="check-booking.php" class="btn-secondary">Check Booking</a>
            </div>
        </div>
    </header>

    <div class="search-section">
        <div class="container">
            <form action="" method="GET" class="search-form">
                <div class="search-wrapper">
                    <input type="text" 
                           name="search" 
                           placeholder="Search by brand or model..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="search-input">
                    <button type="submit" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <main class="container">
        <section id="cars" class="cars-grid">
            <?php foreach($cars as $car): ?>
            <div class="car-card">
                <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['model']); ?>">
                <div class="car-details">
                    <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                    <p class="price">₱<?php echo htmlspecialchars($car['cost_per_day']); ?> per day</p>
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
            <p>&copy; 2024 DGMT CarRental. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelector('.search-input').addEventListener('input', function(e) {
            if (this.value === '') {
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>
<?php
require_once '../config/database.php';

// Get statistics
$stats = [
    'total_cars' => $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn(),
    'available_cars' => $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'")->fetchColumn(),
    'active_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
    'pending_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn()
];

// Get recent bookings
$recentBookings = $pdo->query("
    SELECT b.*, c.brand, c.model 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
")->fetchAll();

$pageTitle = "Dashboard";
ob_start();
?>

<div class="admin-header">
    <h1 class="admin-title">Dashboard</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Cars</h3>
        <p class="stat-number"><?php echo $stats['total_cars']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Available Cars</h3>
        <p class="stat-number"><?php echo $stats['available_cars']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Active Bookings</h3>
        <p class="stat-number"><?php echo $stats['active_bookings']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Pending Bookings</h3>
        <p class="stat-number"><?php echo $stats['pending_bookings']; ?></p>
    </div>
</div>

<div class="recent-bookings">
    <h2>Recent Bookings</h2>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Car</th>
                    <th>Client</th>
                    <th>Pickup Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentBookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></td>
                    <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($booking['pickup_datetime'])); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?> 
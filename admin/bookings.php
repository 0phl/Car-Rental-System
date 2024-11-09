<?php
require_once '../config/database.php';

// Handle status updates
if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['booking_id']]);
    
    // If booking is completed/cancelled, make car available again
    if (in_array($_POST['status'], ['completed', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE cars SET status = 'available' WHERE id = (SELECT car_id FROM bookings WHERE id = ?)");
        $stmt->execute([$_POST['booking_id']]);
    }
    
    header('Location: bookings.php');
    exit();
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT b.*, c.brand, c.model 
          FROM bookings b 
          JOIN cars c ON b.car_id = c.id 
          WHERE 1=1";

if ($status_filter !== 'all') {
    $query .= " AND b.status = :status";
}

if ($search) {
    $query .= " AND (b.reference_no LIKE :search 
                OR b.client_name LIKE :search 
                OR b.email LIKE :search)";
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);

if ($status_filter !== 'all') {
    $stmt->bindValue(':status', $status_filter);
}

if ($search) {
    $stmt->bindValue(':search', "%$search%");
}

$stmt->execute();
$bookings = $stmt->fetchAll();

$pageTitle = "Bookings Management";
ob_start();
?>

<div class="admin-header">
    <h1 class="admin-title">Bookings Management</h1>
</div>

<div class="filters">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label for="status">Status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Bookings</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" 
                   placeholder="Reference no, name or email">
            <button type="submit" class="btn-search">Search</button>
        </div>
    </form>
</div>

<div class="data-table bookings-table">
    <table>
        <thead>
            <tr>
                <th>Reference No</th>
                <th>Car</th>
                <th>Client Details</th>
                <th>Dates</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['reference_no']); ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></strong>
                </td>
                <td>
                    <div class="client-details">
                        <p><strong><?php echo htmlspecialchars($booking['client_name']); ?></strong></p>
                        <p><?php echo htmlspecialchars($booking['email']); ?></p>
                        <p><?php echo htmlspecialchars($booking['contact_no']); ?></p>
                    </div>
                </td>
                <td>
                    <div class="booking-dates">
                        <p><strong>Pickup:</strong> <?php echo date('M d, Y h:i A', strtotime($booking['pickup_datetime'])); ?></p>
                        <p><strong>Return:</strong> <?php echo date('M d, Y h:i A', strtotime($booking['return_datetime'])); ?></p>
                    </div>
                </td>
                <td>
                    <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </td>
                <td>
                    <div class="booking-actions">
                        <button type="button" 
                                class="btn-view-details"
                                onclick="viewDetails('<?php echo htmlspecialchars(json_encode($booking), ENT_QUOTES); ?>')">
                            View Details
                        </button>
                        <?php if ($booking['status'] === 'pending'): ?>
                            <form method="POST" class="status-form">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" name="update_status" class="btn-confirm">Confirm</button>
                            </form>
                        <?php endif; ?>
                        <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                            <form method="POST" class="status-form" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" name="update_status" class="btn-cancel">Cancel</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($booking['status'] === 'confirmed'): ?>
                            <form method="POST" class="status-form">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" name="update_status" class="btn-complete">Complete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Booking Details</h2>
        <div id="modalContent"></div>
    </div>
</div>

<script>
function viewDetails(bookingData) {
    const booking = JSON.parse(bookingData);
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('modalContent');
    
    content.innerHTML = `
        <div class="booking-details-grid">
            <div class="detail-row">
                <span class="label">Reference Number:</span>
                <span class="value">${booking.reference_no}</span>
            </div>
            <div class="detail-row">
                <span class="label">Client Name:</span>
                <span class="value">${booking.client_name}</span>
            </div>
            <div class="detail-row">
                <span class="label">Email:</span>
                <span class="value">${booking.email}</span>
            </div>
            <div class="detail-row">
                <span class="label">Contact Number:</span>
                <span class="value">${booking.contact_no}</span>
            </div>
            <div class="detail-row">
                <span class="label">Car:</span>
                <span class="value">${booking.brand} ${booking.model}</span>
            </div>
            <div class="detail-row">
                <span class="label">Pickup Location:</span>
                <span class="value">${booking.location}</span>
            </div>
            <div class="detail-row">
                <span class="label">Pickup Date:</span>
                <span class="value">${new Date(booking.pickup_datetime).toLocaleString()}</span>
            </div>
            <div class="detail-row">
                <span class="label">Return Date:</span>
                <span class="value">${new Date(booking.return_datetime).toLocaleString()}</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value status-badge ${booking.status.toLowerCase()}">${booking.status}</span>
            </div>
        </div>
    `;
    
    modal.style.display = "block";
}

// Close modal when clicking the X or outside the modal
document.querySelector('.close').onclick = function() {
    document.getElementById('detailsModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?> 
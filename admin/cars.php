<?php
require_once '../config/database.php';

// Handle car deletion
if (isset($_POST['delete_car'])) {
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$_POST['car_id']]);
    header('Location: cars.php');
    exit();
}

// Fetch all cars
$cars = $pdo->query("SELECT * FROM cars ORDER BY created_at DESC")->fetchAll();

$pageTitle = "Cars Management";
ob_start();
?>

<div class="admin-header">
    <h1 class="admin-title">Cars Management</h1>
    <a href="add-car.php" class="btn-add">Add New Car</a>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Brand & Model</th>
                <th>Seats</th>
                <th>Cost/Day</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($cars as $car): ?>
            <tr>
                <td>
                    <img src="<?php echo htmlspecialchars($car['image']); ?>" 
                         alt="<?php echo htmlspecialchars($car['model']); ?>"
                         class="car-thumbnail">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($car['brand']); ?></strong><br>
                    <?php echo htmlspecialchars($car['model']); ?>
                </td>
                <td><?php echo htmlspecialchars($car['seats']); ?></td>
                <td>$<?php echo htmlspecialchars($car['cost_per_day']); ?></td>
                <td>
                    <span class="status-badge <?php echo strtolower($car['status']); ?>">
                        <?php echo ucfirst($car['status']); ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="edit-car.php?id=<?php echo $car['id']; ?>" class="btn-edit">Edit</a>
                    <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this car?');">
                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                        <button type="submit" name="delete_car" class="btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?> 
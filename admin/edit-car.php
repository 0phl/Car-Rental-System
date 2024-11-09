<?php
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: cars.php');
    exit();
}

$car_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: cars.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_path = $car['image'];
    
    // Handle new image upload if provided
    if ($_FILES['image']['size'] > 0) {
        $target_dir = "../uploads/";
        $image = $_FILES['image'];
        $image_path = $target_dir . time() . '_' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
        $image_path = str_replace('../', '', $image_path);
    }
    
    $stmt = $pdo->prepare("UPDATE cars SET 
        brand = ?, model = ?, seats = ?, transmission = ?, 
        fuel_type = ?, cost_per_day = ?, features = ?, 
        description = ?, image = ? WHERE id = ?");
        
    $stmt->execute([
        $_POST['brand'],
        $_POST['model'],
        $_POST['seats'],
        $_POST['transmission'],
        $_POST['fuel_type'],
        $_POST['cost_per_day'],
        $_POST['features'],
        $_POST['description'],
        $image_path,
        $car_id
    ]);
    
    header('Location: cars.php');
    exit();
}

$pageTitle = "Edit Car";
ob_start();
?>

<div class="admin-header">
    <h1 class="admin-title">Edit Car</h1>
</div>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="car-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
            </div>

            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
            </div>

            <div class="form-group">
                <label for="seats">Number of Seats</label>
                <input type="number" id="seats" name="seats" value="<?php echo htmlspecialchars($car['seats']); ?>" required min="1">
            </div>

            <div class="form-group">
                <label for="transmission">Transmission</label>
                <select id="transmission" name="transmission" required>
                    <option value="Automatic" <?php echo $car['transmission'] === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                    <option value="Manual" <?php echo $car['transmission'] === 'Manual' ? 'selected' : ''; ?>>Manual</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fuel_type">Fuel Type</label>
                <select id="fuel_type" name="fuel_type" required>
                    <?php
                    $fuel_types = ['Petrol', 'Diesel', 'Hybrid', 'Electric'];
                    foreach ($fuel_types as $type) {
                        echo '<option value="' . $type . '"' . 
                             ($car['fuel_type'] === $type ? ' selected' : '') . 
                             '>' . $type . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="cost_per_day">Cost per Day ($)</label>
                <input type="number" id="cost_per_day" name="cost_per_day" 
                       value="<?php echo htmlspecialchars($car['cost_per_day']); ?>" 
                       required min="0" step="0.01">
            </div>
        </div>

        <div class="form-group">
            <label for="features">Features (one per line)</label>
            <textarea id="features" name="features" rows="4" required><?php echo htmlspecialchars($car['features']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($car['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Car Image (leave empty to keep current image)</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if ($car['image']): ?>
                <div class="current-image">
                    <img src="../<?php echo htmlspecialchars($car['image']); ?>" alt="Current car image">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a href="cars.php" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Car</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?> 
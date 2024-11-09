<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image = $_FILES['image'];
    $image_path = $target_dir . time() . '_' . basename($image['name']);
    
    if (move_uploaded_file($image['tmp_name'], $image_path)) {
        $stmt = $pdo->prepare("INSERT INTO cars (brand, model, seats, transmission, 
            fuel_type, cost_per_day, features, description, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
        $stmt->execute([
            $_POST['brand'],
            $_POST['model'],
            $_POST['seats'],
            $_POST['transmission'],
            $_POST['fuel_type'],
            $_POST['cost_per_day'],
            $_POST['features'],
            $_POST['description'],
            str_replace('../', '', $image_path)
        ]);
        
        header('Location: cars.php');
        exit();
    }
}

$pageTitle = "Add New Car";
ob_start();
?>

<div class="admin-header">
    <h1 class="admin-title">Add New Car</h1>
</div>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="car-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" required>
            </div>

            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" required>
            </div>

            <div class="form-group">
                <label for="seats">Number of Seats</label>
                <input type="number" id="seats" name="seats" required min="1">
            </div>

            <div class="form-group">
                <label for="transmission">Transmission</label>
                <select id="transmission" name="transmission" required>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fuel_type">Fuel Type</label>
                <select id="fuel_type" name="fuel_type" required>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Electric">Electric</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cost_per_day">Cost per Day ($)</label>
                <input type="number" id="cost_per_day" name="cost_per_day" required min="0" step="0.01">
            </div>
        </div>

        <div class="form-group">
            <label for="features">Features (one per line)</label>
            <textarea id="features" name="features" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Car Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>

        <div class="form-actions">
            <a href="cars.php" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Add Car</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once 'includes/layout.php';
?> 
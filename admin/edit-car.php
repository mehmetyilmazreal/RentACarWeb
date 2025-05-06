<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

if (!isset($_GET['id'])) {
    header("Location: cars.php");
    exit();
}

$car_id = $_GET['id'];
$sql = "SELECT * FROM cars WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $car_id, PDO::PARAM_INT);
$stmt->execute();
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: cars.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $plate = $_POST['plate'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $mileage = $_POST['mileage'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    
    // Resim yükleme işlemi
    $image = $car['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/cars/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Eski resmi sil
            if ($car['image'] && file_exists("../assets/images/cars/" . $car['image'])) {
                unlink("../assets/images/cars/" . $car['image']);
            }
            $image = $new_filename;
        }
    }
    
    $sql = "UPDATE cars SET brand = :brand, model = :model, year = :year, plate = :plate,
            transmission = :transmission, fuel_type = :fuel_type, mileage = :mileage,
            price = :price, status = :status, description = :description, image = :image 
            WHERE id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':plate', $plate);
    $stmt->bindParam(':transmission', $transmission);
    $stmt->bindParam(':fuel_type', $fuel_type);
    $stmt->bindParam(':mileage', $mileage);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':id', $car_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Araç başarıyla güncellendi.";
        header("Location: cars.php");
        exit();
    } else {
        $_SESSION['error'] = "Araç güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Düzenle - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Araç Düzenle</h2>
                    <a href="cars.php" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Geri Dön
                    </a>
                </div>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand" class="form-label">Marka</label>
                                    <input type="text" class="form-control" id="brand" name="brand" 
                                           value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="model" name="model" 
                                           value="<?php echo htmlspecialchars($car['model']); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="year" class="form-label">Yıl</label>
                                    <input type="number" class="form-control" id="year" name="year" 
                                           value="<?php echo htmlspecialchars($car['year']); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="plate" class="form-label">Plaka</label>
                                    <input type="text" class="form-control" id="plate" name="plate" 
                                           value="<?php echo htmlspecialchars($car['plate'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="transmission" class="form-label">Vites</label>
                                    <select class="form-select" id="transmission" name="transmission" required>
                                        <option value="automatic" <?php echo ($car['transmission'] ?? '') == 'automatic' ? 'selected' : ''; ?>>Otomatik</option>
                                        <option value="manual" <?php echo ($car['transmission'] ?? '') == 'manual' ? 'selected' : ''; ?>>Manuel</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="fuel_type" class="form-label">Yakıt Tipi</label>
                                    <select class="form-select" id="fuel_type" name="fuel_type" required>
                                        <option value="petrol" <?php echo ($car['fuel_type'] ?? '') == 'petrol' ? 'selected' : ''; ?>>Benzin</option>
                                        <option value="diesel" <?php echo ($car['fuel_type'] ?? '') == 'diesel' ? 'selected' : ''; ?>>Dizel</option>
                                        <option value="hybrid" <?php echo ($car['fuel_type'] ?? '') == 'hybrid' ? 'selected' : ''; ?>>Hibrit</option>
                                        <option value="electric" <?php echo ($car['fuel_type'] ?? '') == 'electric' ? 'selected' : ''; ?>>Elektrik</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="mileage" class="form-label">Kilometre</label>
                                    <input type="number" class="form-control" id="mileage" name="mileage" 
                                           value="<?php echo htmlspecialchars($car['mileage'] ?? 0); ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Fiyat</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                           value="<?php echo htmlspecialchars($car['price']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Durum</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="available" <?php echo $car['status'] == 'available' ? 'selected' : ''; ?>>Müsait</option>
                                        <option value="rented" <?php echo $car['status'] == 'rented' ? 'selected' : ''; ?>>Kirada</option>
                                        <option value="maintenance" <?php echo $car['status'] == 'maintenance' ? 'selected' : ''; ?>>Bakımda</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">Resim</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <?php if ($car['image']): ?>
                                        <div class="mt-2">
                                            <img src="../assets/images/cars/<?php echo htmlspecialchars($car['image']); ?>" 
                                                 alt="Mevcut Resim" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4"><?php echo htmlspecialchars($car['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save'></i> Değişiklikleri Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
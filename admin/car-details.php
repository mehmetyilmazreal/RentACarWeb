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
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Detayları - Admin Panel</title>
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
                    <h2>Araç Detayları</h2>
                    <div>
                        <a href="edit-car.php?id=<?php echo $car['id']; ?>" class="btn btn-warning me-2">
                            <i class='bx bxs-edit'></i> Düzenle
                        </a>
                        <a href="cars.php" class="btn btn-secondary">
                            <i class='bx bx-arrow-back'></i> Geri Dön
                        </a>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Araç Bilgileri</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th>Marka:</th>
                                            <td><?php echo htmlspecialchars($car['brand']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Model:</th>
                                            <td><?php echo htmlspecialchars($car['model']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Yıl:</th>
                                            <td><?php echo htmlspecialchars($car['year']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Plaka:</th>
                                            <td><?php echo htmlspecialchars($car['plate'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Vites:</th>
                                            <td><?php echo htmlspecialchars($car['transmission'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Yakıt Tipi:</th>
                                            <td><?php echo htmlspecialchars($car['fuel_type'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Kilometre:</th>
                                            <td><?php echo number_format($car['mileage'] ?? 0); ?> km</td>
                                        </tr>
                                        <tr>
                                            <th>Fiyat:</th>
                                            <td>₺<?php echo number_format($car['price'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Durum:</th>
                                            <td>
                                                <span class="badge bg-<?php echo $car['status'] == 'available' ? 'success' : 'warning'; ?>">
                                                    <?php echo $car['status'] == 'available' ? 'Müsait' : 'Kirada'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Araç Resmi</h5>
                                <?php if ($car['image']): ?>
                                    <img src="../assets/images/cars/<?php echo htmlspecialchars($car['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($car['model']); ?>"
                                         class="img-fluid rounded">
                                <?php else: ?>
                                    <p class="text-muted">Resim bulunmuyor</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Açıklama</h5>
                        <p><?php echo nl2br(htmlspecialchars($car['description'] ?? '')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
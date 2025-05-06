<?php
// Veritabanı bağlantısını dahil et
require_once 'config/db.php';

// Filtreleme parametrelerini al
$brand = $_GET['brand'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$transmission = $_GET['transmission'] ?? '';
$fuel_type = $_GET['fuel_type'] ?? '';

// SQL sorgusunu oluştur
$sql = "SELECT * FROM cars WHERE status = 'available'";
$params = [];

if ($brand) {
    $sql .= " AND brand LIKE ?";
    $params[] = "%$brand%";
}

if ($min_price) {
    $sql .= " AND daily_price >= ?";
    $params[] = $min_price;
}

if ($max_price) {
    $sql .= " AND daily_price <= ?";
    $params[] = $max_price;
}

if ($transmission) {
    $sql .= " AND transmission = ?";
    $params[] = $transmission;
}

if ($fuel_type) {
    $sql .= " AND fuel_type = ?";
    $params[] = $fuel_type;
}

$sql .= " ORDER BY created_at DESC";

// Araçları getir
$cars = query($sql, $params)->fetchAll();

// Markaları getir (filtreleme için)
$brands = query("SELECT DISTINCT brand FROM cars WHERE status = 'available' ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araçlarımız | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <!-- Filtreler -->
                    <div class="col-lg-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Filtreler</h5>
                                <form method="GET" id="filterForm">
                                    <div class="mb-3">
                                        <label class="form-label">Marka</label>
                                        <select name="brand" class="form-select">
                                            <option value="">Tümü</option>
                                            <?php foreach ($brands as $b): ?>
                                                <option value="<?php echo htmlspecialchars($b); ?>" 
                                                        <?php echo $brand === $b ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($b); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Günlük Fiyat Aralığı</label>
                                        <div class="input-group mb-2">
                                            <input type="number" name="min_price" class="form-control" 
                                                   placeholder="Min" value="<?php echo $min_price; ?>">
                                            <input type="number" name="max_price" class="form-control" 
                                                   placeholder="Max" value="<?php echo $max_price; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Vites</label>
                                        <select name="transmission" class="form-select">
                                            <option value="">Tümü</option>
                                            <option value="manual" <?php echo $transmission === 'manual' ? 'selected' : ''; ?>>Manuel</option>
                                            <option value="automatic" <?php echo $transmission === 'automatic' ? 'selected' : ''; ?>>Otomatik</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Yakıt Tipi</label>
                                        <select name="fuel_type" class="form-select">
                                            <option value="">Tümü</option>
                                            <option value="petrol" <?php echo $fuel_type === 'petrol' ? 'selected' : ''; ?>>Benzin</option>
                                            <option value="diesel" <?php echo $fuel_type === 'diesel' ? 'selected' : ''; ?>>Dizel</option>
                                            <option value="hybrid" <?php echo $fuel_type === 'hybrid' ? 'selected' : ''; ?>>Hibrit</option>
                                            <option value="electric" <?php echo $fuel_type === 'electric' ? 'selected' : ''; ?>>Elektrik</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-custom">
                                            <i class="fas fa-filter me-2"></i>Filtrele
                                        </button>
                                        <a href="cars.php" class="btn btn-outline-custom">
                                            <i class="fas fa-times me-2"></i>Filtreleri Temizle
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Araç Listesi -->
                    <div class="col-lg-9">
                        <div class="row">
                            <?php if (count($cars) > 0): ?>
                                <?php foreach ($cars as $car): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100">
                                            <div class="car-image">
                                                <img src="assets/images/cars/<?php echo htmlspecialchars($car['image']); ?>" 
                                                     class="img-fluid" 
                                                     alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                                                <div class="car-overlay">
                                                    <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-light">
                                                        Detayları Gör <i class="fas fa-arrow-right ms-2"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="car-content">
                                                    <div class="car-header">
                                                        <h3 class="car-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                                                        <div class="car-price">
                                                            <?php echo number_format($car['price'], 2); ?> ₺<span>/ gün</span>
                                                        </div>
                                                    </div>
                                                    <div class="car-features">
                                                        <span><i class="fas fa-gas-pump"></i> <?php echo ucfirst(htmlspecialchars($car['fuel_type'])); ?></span>
                                                        <span><i class="fas fa-cog"></i> <?php echo ucfirst(htmlspecialchars($car['transmission'])); ?></span>
                                                        <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage']); ?> KM</span>
                                                    </div>
                                                    <div class="car-footer mt-3">
                                                        <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-custom w-100">
                                                            Detayları Gör
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Seçtiğiniz kriterlere uygun araç bulunamadı.
                                        <a href="cars.php" class="alert-link">Filtreleri temizleyin</a>.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 
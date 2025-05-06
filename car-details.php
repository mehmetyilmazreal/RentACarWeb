<?php
session_start();
require_once 'config/db.php';

// Araç ID'sini al
$car_id = $_GET['id'] ?? null;
if (!$car_id) {
    header('Location: cars.php');
    exit;
}

// Araç bilgilerini getir
$stmt = $db->prepare("
    SELECT c.*, 
           GROUP_CONCAT(DISTINCT cf.feature_name) as additional_features
    FROM cars c
    LEFT JOIN car_features cf ON c.id = cf.car_id
    WHERE c.id = ?
    GROUP BY c.id
");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: cars.php');
    exit;
}

// Durum badge'inin rengini belirle
$status_badge = [
    'available' => ['bg-success', 'Müsait'],
    'rented' => ['bg-warning', 'Kirada'],
    'maintenance' => ['bg-danger', 'Bakımda']
][$car['status']] ?? ['bg-secondary', 'Bilinmiyor'];

// Yakıt tipini Türkçeleştir
$fuel_types = [
    'petrol' => 'Benzin',
    'diesel' => 'Dizel',
    'hybrid' => 'Hibrit',
    'electric' => 'Elektrik'
];

// Vites tipini Türkçeleştir
$transmission_types = [
    'automatic' => 'Otomatik',
    'manual' => 'Manuel'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?> | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <!-- Araç Başlık ve Özet Bilgiler -->
                <div class="row mb-5">
                    <div class="col-lg-8">
                        <h1 class="car-title mb-3"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
                        <div class="d-flex gap-3 mb-4">
                            <span class="badge bg-<?php echo $status_badge[0]; ?>"><?php echo $status_badge[1]; ?></span>
                            <span class="badge bg-light text-dark"><?php echo $transmission_types[$car['transmission']] ?? 'Bilinmiyor'; ?></span>
                            <span class="badge bg-light text-dark"><?php echo $fuel_types[$car['fuel_type']] ?? 'Bilinmiyor'; ?></span>
                            <span class="badge bg-light text-dark"><?php echo $car['seats']; ?> Kişilik</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="price-tag">
                            <span class="h2 mb-0"><?php echo number_format($car['price'], 2); ?>₺</span>
                            <small class="text-muted">/gün</small>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($car['status'] == 'available'): ?>
                                <a href="#booking" class="btn btn-custom btn-lg w-100 mt-3">Hemen Kirala</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg w-100 mt-3" disabled>
                                    <?php echo $status_badge[1]; ?>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-custom btn-lg w-100 mt-3">Kiralamak için Giriş Yapın</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Araç Galerisi -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <img src="assets/images/cars/<?php echo htmlspecialchars($car['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" 
                                     class="img-fluid rounded-3">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Araç Detayları -->
                    <div class="col-lg-8">
                        <!-- Özellikler -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Araç Özellikleri</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <div>
                                                <h6>Motor Hacmi</h6>
                                                <p><?php echo htmlspecialchars($car['engine_size']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-horse"></i>
                                            <div>
                                                <h6>Beygir Gücü</h6>
                                                <p><?php echo htmlspecialchars($car['horsepower']); ?> HP</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-cog"></i>
                                            <div>
                                                <h6>Vites</h6>
                                                <p><?php echo $transmission_types[$car['transmission']] ?? 'Bilinmiyor'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-gas-pump"></i>
                                            <div>
                                                <h6>Yakıt Tipi</h6>
                                                <p><?php echo $fuel_types[$car['fuel_type']] ?? 'Bilinmiyor'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Donanım -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Donanım</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <?php if ($car['air_conditioning']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Klima</li>
                                            <?php endif; ?>
                                            <?php if ($car['abs']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>ABS</li>
                                            <?php endif; ?>
                                            <?php if ($car['esp']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>ESP</li>
                                            <?php endif; ?>
                                            <?php if ($car['cruise_control']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Hız Sabitleyici</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <?php if ($car['parking_sensors']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Park Sensörü</li>
                                            <?php endif; ?>
                                            <?php if ($car['reverse_camera']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Geri Görüş Kamerası</li>
                                            <?php endif; ?>
                                            <?php if ($car['bluetooth']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bluetooth</li>
                                            <?php endif; ?>
                                            <?php if ($car['navigation']): ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Navigasyon</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ek Özellikler -->
                        <?php if ($car['additional_features']): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Ek Özellikler</h3>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <ul class="list-unstyled row">
                                            <?php foreach (explode(',', $car['additional_features']) as $feature): ?>
                                                <li class="col-md-6 mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <?php echo htmlspecialchars(trim($feature)); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Açıklama -->
                        <?php if ($car['description']): ?>
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Araç Hakkında</h3>
                                <p class="card-text">
                                    <?php echo nl2br(htmlspecialchars($car['description'])); ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Kiralama Formu -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">Kiralama Formu</h5>
                                <?php if (!isset($_SESSION['user_id'])): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Kiralama işlemi yapabilmek için lütfen giriş yapın.
                                        <div class="mt-3">
                                            <a href="login.php" class="btn btn-custom btn-sm me-2">
                                                <i class="fas fa-sign-in-alt me-1"></i>Giriş Yap
                                            </a>
                                            <a href="register.php" class="btn btn-outline-custom btn-sm">
                                                <i class="fas fa-user-plus me-1"></i>Kayıt Ol
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form id="bookingForm" class="needs-validation" novalidate>
                                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Alış Tarihi</label>
                                                <input type="date" class="form-control" name="start_date" required 
                                                       min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Dönüş Tarihi</label>
                                                <input type="date" class="form-control" name="end_date" required
                                                       min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Alış Saati</label>
                                                <input type="time" class="form-control" name="start_time" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Dönüş Saati</label>
                                                <input type="time" class="form-control" name="end_time" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-custom w-100" 
                                                        <?php echo $car['status'] != 'available' ? 'disabled' : ''; ?>>
                                                    Rezervasyon Yap
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        // Tarih kontrolü
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener('change', function() {
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;
                
                if (startDate && endDate && startDate > endDate) {
                    alert('Dönüş tarihi alış tarihinden önce olamaz!');
                    this.value = '';
                }
            });
        });

        // Form gönderimi
        document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/create-rental.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Bir hata oluştu!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        });
    </script>
</body>
</html> 
<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once 'config/db.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Filtreleme parametrelerini al
$status = $_GET['status'] ?? 'all';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

// SQL sorgusunu oluştur
$sql = "SELECT r.*, c.brand, c.model, c.plate, c.image 
        FROM rentals r 
        JOIN cars c ON r.car_id = c.id 
        WHERE r.user_id = ?";

$params = [$_SESSION['user_id']];

if ($status !== 'all') {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}

if ($date_start && $date_end) {
    $sql .= " AND r.start_date BETWEEN ? AND ?";
    $params[] = $date_start;
    $params[] = $date_end;
}

$sql .= " ORDER BY r.created_at DESC";

// Kiralamaları getir
$rentals = query($sql, $params)->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiralamalarım | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <!-- Filtreler -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Tümü</option>
                                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>İptal Edildi</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tarih Aralığı</label>
                                <div class="input-group">
                                    <input type="text" name="date_start" class="form-control datepicker" 
                                           placeholder="Başlangıç" value="<?php echo $date_start; ?>">
                                    <input type="text" name="date_end" class="form-control datepicker" 
                                           placeholder="Bitiş" value="<?php echo $date_end; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-custom">
                                        <i class="fas fa-filter me-2"></i>Filtrele
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Kiralamalar -->
                <div class="row">
                    <?php if (count($rentals) > 0): ?>
                        <?php foreach ($rentals as $rental): 
                            $statusClass = [
                                'pending' => 'warning',
                                'active' => 'success',
                                'completed' => 'info',
                                'cancelled' => 'danger'
                            ][$rental['status']];
                            
                            $statusText = [
                                'pending' => 'Beklemede',
                                'active' => 'Aktif',
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi'
                            ][$rental['status']];
                        ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <?php if ($rental['image']): ?>
                                        <img src="<?php echo htmlspecialchars($rental['image']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?>">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?>
                                        </h5>
                                        
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Plaka: <?php echo htmlspecialchars($rental['plate']); ?>
                                            </small>
                                        </p>
                                        
                                        <div class="rental-details">
                                            <div class="mb-2">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                <?php echo date('d.m.Y', strtotime($rental['start_date'])); ?> - 
                                                <?php echo date('d.m.Y', strtotime($rental['end_date'])); ?>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <i class="fas fa-clock me-2"></i>
                                                <?php echo date('H:i', strtotime($rental['start_time'])); ?> - 
                                                <?php echo date('H:i', strtotime($rental['end_time'])); ?>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <i class="fas fa-money-bill-wave me-2"></i>
                                                Toplam: ₺<?php echo number_format($rental['total_price'], 2); ?>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                                
                                                <?php if ($rental['status'] === 'active'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelRental(<?php echo $rental['id']; ?>)">
                                                        <i class="fas fa-times me-1"></i>İptal Et
                                                    </button>
                                                <?php endif; ?>
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
                                Henüz kiralama işleminiz bulunmuyor.
                                <a href="cars.php" class="alert-link">Araçları inceleyin</a>.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Tarih seçici
        flatpickr(".datepicker", {
            locale: "tr",
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Kiralamayı iptal et
        function cancelRental(id) {
            if (confirm('Kiralamayı iptal etmek istediğinize emin misiniz?')) {
                fetch('api/cancel-rental.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ rental_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</body>
</html> 
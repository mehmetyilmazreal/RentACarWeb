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

// Kullanıcının rezervasyonlarını getir
$stmt = $db->prepare("
    SELECT r.*, 
           c.brand, c.model, c.plate, c.image,
           CASE 
               WHEN r.status = 'pending' THEN 'Beklemede'
               WHEN r.status = 'confirmed' THEN 'Onaylandı'
               WHEN r.status = 'cancelled' THEN 'İptal Edildi'
               WHEN r.status = 'completed' THEN 'Tamamlandı'
           END as status_text,
           CASE 
               WHEN r.status = 'pending' THEN 'warning'
               WHEN r.status = 'confirmed' THEN 'success'
               WHEN r.status = 'cancelled' THEN 'danger'
               WHEN r.status = 'completed' THEN 'info'
           END as status_class
    FROM rentals r
    JOIN cars c ON r.car_id = c.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$rentals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyonlarım | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <h1 class="mb-4">Rezervasyonlarım</h1>

                <?php if (empty($rentals)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Henüz rezervasyonunuz bulunmuyor.
                        <a href="cars.php" class="alert-link ms-2">Araçları İncele</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($rentals as $rental): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <?php if ($rental['image']): ?>
                                        <img src="assets/images/cars/<?php echo htmlspecialchars($rental['image']); ?>" 
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
                                            <p>
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                Alış: <?php echo date('d.m.Y H:i', strtotime($rental['start_date'] . ' ' . $rental['start_time'])); ?>
                                            </p>
                                            <p>
                                                <i class="fas fa-calendar-check me-2"></i>
                                                Dönüş: <?php echo date('d.m.Y H:i', strtotime($rental['end_date'] . ' ' . $rental['end_time'])); ?>
                                            </p>
                                            <p>
                                                <i class="fas fa-money-bill-wave me-2"></i>
                                                Toplam: <?php echo number_format($rental['total_price'], 2); ?> ₺
                                            </p>
                                        </div>

                                        <div class="mt-3">
                                            <span class="badge bg-<?php echo $rental['status_class']; ?>">
                                                <?php echo $rental['status_text']; ?>
                                            </span>
                                        </div>

                                        <?php if ($rental['status'] === 'pending'): ?>
                                            <div class="mt-3">
                                                <button class="btn btn-danger btn-sm" 
                                                        onclick="cancelRental(<?php echo $rental['id']; ?>)">
                                                    <i class="fas fa-times me-1"></i>İptal Et
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cancelRental(rentalId) {
            if (!confirm('Bu rezervasyonu iptal etmek istediğinizden emin misiniz?')) {
                return;
            }

            fetch('api/cancel-rental.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rental_id: rentalId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı iptal mesajını göster
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <h5 class="alert-heading">Rezervasyon İptal Edildi!</h5>
                        <p>${data.message}</p>
                        <p class="mb-0"><strong>Ödeme İadesi:</strong> Ödemeniz en kısa sürede hesabınıza aktarılacaktır.</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('main').insertAdjacentElement('afterbegin', alertDiv);

                    // Sayfayı yenile
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    alert(data.message || 'Bir hata oluştu!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu!');
            });
        }
    </script>
</body>
</html> 
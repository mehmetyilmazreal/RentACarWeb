<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Kiralama ID'sini al
$rental_id = $_GET['id'] ?? null;

if (!$rental_id) {
    header('Location: rentals.php');
    exit;
}

try {
    // Kiralama detaylarını getir
    $stmt = $db->prepare("
        SELECT r.*, 
               u.name as user_name, u.email as user_email, u.phone as user_phone,
               c.brand, c.model, c.plate, c.image as car_image,
               p.status as payment_status, p.payment_method, p.transaction_id
        FROM rentals r
        JOIN users u ON r.user_id = u.id
        JOIN cars c ON r.car_id = c.id
        LEFT JOIN payments p ON r.id = p.rental_id
        WHERE r.id = ?
    ");
    
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch();

    if (!$rental) {
        header('Location: rentals.php');
        exit;
    }

    // Durum metinlerini tanımla
    $statusMap = [
        'pending' => [
            'class' => 'warning',
            'text' => 'Beklemede'
        ],
        'confirmed' => [
            'class' => 'success',
            'text' => 'Onaylandı'
        ],
        'completed' => [
            'class' => 'info',
            'text' => 'Tamamlandı'
        ],
        'cancelled' => [
            'class' => 'danger',
            'text' => 'İptal Edildi'
        ]
    ];

    $currentStatus = isset($rental['status']) && array_key_exists($rental['status'], $statusMap) 
        ? $rental['status'] 
        : 'pending';
    
    $statusClass = $statusMap[$currentStatus]['class'];
    $statusText = $statusMap[$currentStatus]['text'];

} catch (Exception $e) {
    header('Location: rentals.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiralama Detayları - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Kiralama Detayları #<?php echo $rental['id']; ?></h4>
                    <a href="rentals.php" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Geri Dön
                    </a>
                </div>

                <div class="row">
                    <!-- Kiralama Bilgileri -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Kiralama Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Durum:</th>
                                        <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Başlangıç:</th>
                                        <td><?php echo date('d.m.Y', strtotime($rental['start_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Bitiş:</th>
                                        <td><?php echo date('d.m.Y', strtotime($rental['end_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Toplam Tutar:</th>
                                        <td>₺<?php echo number_format($rental['total_price'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Oluşturulma:</th>
                                        <td><?php echo date('d.m.Y H:i', strtotime($rental['created_at'])); ?></td>
                                    </tr>
                                </table>

                                <?php if ($currentStatus === 'pending'): ?>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-success" onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'completed')">
                                        <i class='bx bx-check'></i> Onayla
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'cancelled')">
                                        <i class='bx bx-x'></i> İptal Et
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Araç Bilgileri -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Araç Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="../assets/images/cars/<?php echo $rental['car_image']; ?>" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                <table class="table">
                                    <tr>
                                        <th>Marka:</th>
                                        <td><?php echo htmlspecialchars($rental['brand']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Model:</th>
                                        <td><?php echo htmlspecialchars($rental['model']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Plaka:</th>
                                        <td><?php echo htmlspecialchars($rental['plate']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Müşteri Bilgileri -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Müşteri Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Ad Soyad:</th>
                                        <td><?php echo htmlspecialchars($rental['user_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>E-posta:</th>
                                        <td><?php echo htmlspecialchars($rental['user_email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telefon:</th>
                                        <td><?php echo htmlspecialchars($rental['user_phone']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Bilgileri -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ödeme Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Durum:</th>
                                        <td><?php echo $rental['payment_status'] ?? 'Beklemede'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ödeme Yöntemi:</th>
                                        <td><?php echo $rental['payment_method'] ?? '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>İşlem ID:</th>
                                        <td><?php echo $rental['transaction_id'] ?? '-'; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kiralama durumunu güncelle
        function updateRentalStatus(id, status) {
            const statusText = status === 'confirmed' ? 'onaylamak' : 
                             status === 'completed' ? 'tamamlamak' : 'iptal etmek';
            
            if (confirm(`Kiralamayı ${statusText} istediğinize emin misiniz?`)) {
                fetch('../api/update-rental-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ rental_id: id, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('İşlem başarıyla tamamlandı');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Bir hata oluştu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                });
            }
        }
    </script>
</body>
</html> 
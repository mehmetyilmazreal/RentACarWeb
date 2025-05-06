<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Filtreleme parametrelerini al
$status = $_GET['status'] ?? 'all';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';
$search = $_GET['search'] ?? '';
$car_search = $_GET['car_search'] ?? '';

// SQL sorgusunu oluştur
$sql = "SELECT r.*, u.name as user_name, u.email as user_email, c.brand, c.model, c.plate 
        FROM rentals r 
        JOIN users u ON r.user_id = u.id 
        JOIN cars c ON r.car_id = c.id 
        WHERE 1=1";

$params = [];

if ($status !== 'all') {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}

if ($date_start && $date_end) {
    $sql .= " AND r.start_date BETWEEN ? AND ?";
    $params[] = $date_start;
    $params[] = $date_end;
}

if ($search) {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($car_search) {
    $sql .= " AND (c.model LIKE ? OR c.plate LIKE ?)";
    $params[] = "%$car_search%";
    $params[] = "%$car_search%";
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
    <title>Kiralamalar - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <!-- Filtreler -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Tümü</option>
                                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>İptal Edildi</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Tarih Aralığı</label>
                                <div class="input-group">
                                    <input type="text" name="date_start" class="form-control datepicker" 
                                           placeholder="Başlangıç" value="<?php echo $date_start; ?>">
                                    <input type="text" name="date_end" class="form-control datepicker" 
                                           placeholder="Bitiş" value="<?php echo $date_end; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Müşteri</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="İsim veya E-posta" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Araç</label>
                                <input type="text" name="car_search" class="form-control" 
                                       placeholder="Model veya Plaka" value="<?php echo htmlspecialchars($car_search); ?>">
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-filter'></i> Filtrele
                                </button>
                                <a href="rentals.php" class="btn btn-secondary">
                                    <i class='bx bx-reset'></i> Sıfırla
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Kiralamalar Tablosu -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Kiralamalar</h5>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportRentals('excel')">
                                <i class='bx bx-file'></i> Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportRentals('pdf')">
                                <i class='bx bx-file-pdf'></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Müşteri</th>
                                        <th>Araç</th>
                                        <th>Başlangıç</th>
                                        <th>Bitiş</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($rentals) > 0): ?>
                                        <?php foreach ($rentals as $rental): 
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'active' => 'success',
                                                'completed' => 'info',
                                                'cancelled' => 'danger'
                                            ][$rental['status']];
                                        ?>
                                        <tr>
                                            <td>#<?php echo $rental['id']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($rental['user_name']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($rental['user_email']); ?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($rental['plate']); ?></small>
                                            </td>
                                            <td><?php echo date('d.m.Y', strtotime($rental['start_date'])); ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($rental['end_date'])); ?></td>
                                            <td>₺<?php echo number_format($rental['total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($rental['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewRental(<?php echo $rental['id']; ?>)">
                                                        <i class='bx bxs-detail'></i>
                                                    </button>
                                                    <?php if ($rental['status'] === 'active'): ?>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="completeRental(<?php echo $rental['id']; ?>)">
                                                        <i class='bx bx-check'></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if (in_array($rental['status'], ['pending', 'active'])): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="cancelRental(<?php echo $rental['id']; ?>)">
                                                        <i class='bx bx-x'></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Kayıt bulunamadı</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Kiralama Detay Modalı -->
    <div class="modal fade" id="rentalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kiralama Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Detaylar AJAX ile yüklenecek -->
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Tarih seçici
        flatpickr(".datepicker", {
            locale: "tr",
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Kiralama detaylarını görüntüle
        function viewRental(id) {
            fetch(`api/rental-detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('#rentalModal .modal-body').innerHTML = data.html;
                        new bootstrap.Modal(document.getElementById('rentalModal')).show();
                    } else {
                        alert(data.message);
                    }
                });
        }
        
        // Kiralamayı tamamla
        function completeRental(id) {
            if (confirm('Kiralamayı tamamlamak istediğinize emin misiniz?')) {
                fetch('api/complete-rental.php', {
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
        
        // Kiralamaları dışa aktar
        function exportRentals(format) {
            const params = new URLSearchParams(window.location.search);
            params.append('format', format);
            window.location.href = `api/export-rentals.php?${params.toString()}`;
        }
    </script>
</body>
</html> 
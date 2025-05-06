<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Kiralamaları getir
$rentals = $db->query("
    SELECT r.*, 
           u.name as user_name, 
           u.email as user_email,
           c.model as car_model,
           c.plate as car_plate
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN cars c ON r.car_id = c.id
    ORDER BY r.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiralamalar | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <?php include 'includes/header.php'; ?>

            <!-- Content -->
            <div class="admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Kiralamalar</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" onclick="exportRentals('excel')">
                            <i class='bx bxs-file-excel'></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="exportRentals('pdf')">
                            <i class='bx bxs-file-pdf'></i> PDF
                        </button>
                    </div>
                </div>

                <!-- Filtreler -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="status">
                                    <option value="">Tümü</option>
                                    <option value="pending">Beklemede</option>
                                    <option value="active">Aktif</option>
                                    <option value="completed">Tamamlandı</option>
                                    <option value="cancelled">İptal Edildi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tarih Aralığı</label>
                                <input type="text" class="form-control date-range-picker" name="date_range">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Müşteri</label>
                                <input type="text" class="form-control" name="customer" placeholder="İsim veya E-posta">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Araç</label>
                                <input type="text" class="form-control" name="car" placeholder="Model veya Plaka">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-filter'></i> Filtrele
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class='bx bx-reset'></i> Sıfırla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kiralama Listesi -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
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
                                    <tr>
                                        <td>#<?php echo $rental['id']; ?></td>
                                        <td>
                                            <div>
                                                <strong><?php echo $rental['user_name']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $rental['user_email']; ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo $rental['car_model']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $rental['car_plate']; ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo date('d.m.Y', strtotime($rental['start_date'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($rental['start_time'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo date('d.m.Y', strtotime($rental['end_date'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($rental['end_time'])); ?>
                                            </small>
                                        </td>
                                        <td>₺<?php echo number_format($rental['total_price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        onclick="viewRental(<?php echo $rental['id']; ?>)">
                                                    <i class='bx bxs-detail'></i>
                                                </button>
                                                <?php if ($rental['status'] == 'active'): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-success"
                                                        onclick="completeRental(<?php echo $rental['id']; ?>)">
                                                    <i class='bx bxs-check-circle'></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="cancelRental(<?php echo $rental['id']; ?>)">
                                                    <i class='bx bxs-x-circle'></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Kiralama Detay Modal -->
    <div class="modal fade" id="rentalDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kiralama Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="rentalDetailContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Date Range Picker
        flatpickr('.date-range-picker', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            locale: 'tr'
        });

        // Form Submit
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Filtreleme işlemleri burada yapılacak
        });

        // Kiralama Detayı
        function viewRental(id) {
            fetch(`api/rental-detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('rentalDetailContent').innerHTML = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6>Müşteri Bilgileri</h6>
                                <p>
                                    <strong>Ad Soyad:</strong> ${data.user_name}<br>
                                    <strong>E-posta:</strong> ${data.user_email}<br>
                                    <strong>Telefon:</strong> ${data.user_phone}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Araç Bilgileri</h6>
                                <p>
                                    <strong>Model:</strong> ${data.car_model}<br>
                                    <strong>Plaka:</strong> ${data.car_plate}<br>
                                    <strong>Yıl:</strong> ${data.car_year}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Kiralama Detayları</h6>
                                <p>
                                    <strong>Başlangıç:</strong> ${data.start_date} ${data.start_time}<br>
                                    <strong>Bitiş:</strong> ${data.end_date} ${data.end_time}<br>
                                    <strong>Toplam Gün:</strong> ${data.total_days}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Ödeme Bilgileri</h6>
                                <p>
                                    <strong>Toplam Tutar:</strong> ₺${data.total_price}<br>
                                    <strong>Ödeme Yöntemi:</strong> ${data.payment_method}<br>
                                    <strong>Ödeme Durumu:</strong> ${data.payment_status}
                                </p>
                            </div>
                            <div class="col-12">
                                <h6>Ekstra Hizmetler</h6>
                                <ul>
                                    ${data.extra_services.map(service => `
                                        <li>${service.name} - ₺${service.price}</li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('rentalDetailModal')).show();
                });
        }

        // Kiralama Tamamlama
        function completeRental(id) {
            if (confirm('Bu kiralamayı tamamlamak istediğinizden emin misiniz?')) {
                fetch('api/complete-rental.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ rental_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Kiralama başarıyla tamamlandı.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('danger', data.message || 'Bir hata oluştu.');
                    }
                });
            }
        }

        // Dışa Aktarma
        function exportRentals(format) {
            const filters = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(filters);
            
            window.location.href = `api/export-rentals.php?format=${format}&${params.toString()}`;
        }
    </script>
</body>
</html> 
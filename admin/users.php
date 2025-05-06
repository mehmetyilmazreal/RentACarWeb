<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Kullanıcıları getir
$users = $db->query("
    SELECT u.*, 
           COUNT(r.id) as rental_count,
           SUM(r.total_price) as total_spent
    FROM users u
    LEFT JOIN rentals r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcılar | Admin Panel</title>
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
                    <h2>Kullanıcılar</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" onclick="exportUsers('excel')">
                            <i class='bx bxs-file-excel'></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="exportUsers('pdf')">
                            <i class='bx bxs-file-pdf'></i> PDF
                        </button>
                    </div>
                </div>

                <!-- Filtreler -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Kullanıcı Tipi</label>
                                <select class="form-select" name="type">
                                    <option value="">Tümü</option>
                                    <option value="individual">Bireysel</option>
                                    <option value="corporate">Kurumsal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kayıt Tarihi</label>
                                <input type="text" class="form-control date-range-picker" name="date_range">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Arama</label>
                                <input type="text" class="form-control" name="search" placeholder="İsim, E-posta veya Telefon">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="status">
                                    <option value="">Tümü</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Pasif</option>
                                </select>
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

                <!-- Kullanıcı Listesi -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kullanıcı</th>
                                        <th>İletişim</th>
                                        <th>Tip</th>
                                        <th>Kiralama</th>
                                        <th>Toplam Harcama</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['id']; ?></td>
                                        <td>
                                            <div>
                                                <strong><?php echo $user['name']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    Kayıt: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <i class='bx bx-envelope'></i> <?php echo $user['email']; ?>
                                                <br>
                                                <i class='bx bx-phone'></i> <?php echo $user['phone']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['type'] == 'individual' ? 'info' : 'primary'; ?>">
                                                <?php echo $user['type'] == 'individual' ? 'Bireysel' : 'Kurumsal'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo $user['rental_count']; ?></strong> kiralama
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>₺<?php echo number_format($user['total_spent'], 2); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo $user['status'] == 'active' ? 'Aktif' : 'Pasif'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        onclick="viewUser(<?php echo $user['id']; ?>)">
                                                    <i class='bx bxs-detail'></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning"
                                                        onclick="editUser(<?php echo $user['id']; ?>)">
                                                    <i class='bx bxs-edit'></i>
                                                </button>
                                                <?php if ($user['status'] == 'active'): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="deactivateUser(<?php echo $user['id']; ?>)">
                                                    <i class='bx bxs-user-x'></i>
                                                </button>
                                                <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-success"
                                                        onclick="activateUser(<?php echo $user['id']; ?>)">
                                                    <i class='bx bxs-user-check'></i>
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

    <!-- Kullanıcı Detay Modal -->
    <div class="modal fade" id="userDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kullanıcı Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userDetailContent"></div>
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

        // Kullanıcı Detayı
        function viewUser(id) {
            fetch(`api/user-detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('userDetailContent').innerHTML = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6>Kişisel Bilgiler</h6>
                                <p>
                                    <strong>Ad Soyad:</strong> ${data.name}<br>
                                    <strong>E-posta:</strong> ${data.email}<br>
                                    <strong>Telefon:</strong> ${data.phone}<br>
                                    <strong>Kullanıcı Tipi:</strong> ${data.type == 'individual' ? 'Bireysel' : 'Kurumsal'}<br>
                                    <strong>Kayıt Tarihi:</strong> ${data.created_at}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Kiralama İstatistikleri</h6>
                                <p>
                                    <strong>Toplam Kiralama:</strong> ${data.rental_count}<br>
                                    <strong>Toplam Harcama:</strong> ₺${data.total_spent}<br>
                                    <strong>Son Kiralama:</strong> ${data.last_rental_date || 'Henüz kiralama yapılmadı'}
                                </p>
                            </div>
                            <div class="col-12">
                                <h6>Son Kiralamalar</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Araç</th>
                                                <th>Tutar</th>
                                                <th>Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.recent_rentals.map(rental => `
                                                <tr>
                                                    <td>${rental.date}</td>
                                                    <td>${rental.car}</td>
                                                    <td>₺${rental.amount}</td>
                                                    <td>
                                                        <span class="badge bg-${rental.status_class}">
                                                            ${rental.status}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('userDetailModal')).show();
                });
        }

        // Kullanıcı Aktifleştirme/Pasifleştirme
        function activateUser(id) {
            if (confirm('Bu kullanıcıyı aktifleştirmek istediğinizden emin misiniz?')) {
                fetch('api/activate-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Kullanıcı başarıyla aktifleştirildi.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('danger', data.message || 'Bir hata oluştu.');
                    }
                });
            }
        }

        function deactivateUser(id) {
            if (confirm('Bu kullanıcıyı pasifleştirmek istediğinizden emin misiniz?')) {
                fetch('api/deactivate-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Kullanıcı başarıyla pasifleştirildi.');
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
        function exportUsers(format) {
            const filters = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(filters);
            
            window.location.href = `api/export-users.php?format=${format}&${params.toString()}`;
        }
    </script>
</body>
</html> 
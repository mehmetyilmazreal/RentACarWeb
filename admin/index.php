<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// İstatistikleri al
$totalCars = $db->query("SELECT COUNT(*) as count FROM cars")->fetch()['count'] ?? 0;
$activeRentals = $db->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'confirmed'")->fetch()['count'] ?? 0;
$totalUsers = $db->query("SELECT COUNT(*) as count FROM users")->fetch()['count'] ?? 0;
$totalRevenue = $db->query("SELECT COALESCE(SUM(total_price), 0) as total FROM rentals WHERE status = 'completed'")->fetch()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/logo.png" alt="OOF Logo" class="logo">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="index.php">
                            <i class='bx bxs-dashboard'></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="rentals.php">
                            <i class='bx bxs-car'></i>
                            <span>Kiralamalar</span>
                        </a>
                    </li>
                    <li>
                        <a href="cars.php">
                            <i class='bx bxs-car-garage'></i>
                            <span>Araçlar</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class='bx bxs-user'></i>
                            <span>Kullanıcılar</span>
                        </a>
                    </li>
                    <li>
                        <a href="locations.php">
                            <i class='bx bxs-map'></i>
                            <span>Araç Konumları</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class='bx bxs-cog'></i>
                            <span>Ayarlar</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class='bx bx-menu'></i>
                    </button>
                    <h2>Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="logout.php" class="btn btn-sm btn-outline-danger">
                            <i class='bx bx-log-out'></i> Çıkış
                        </a>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class='bx bxs-car'></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalCars; ?></h3>
                                <p>Toplam Araç</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class='bx bxs-car-garage'></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $activeRentals; ?></h3>
                                <p>Aktif Kiralama</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class='bx bxs-user'></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalUsers; ?></h3>
                                <p>Toplam Kullanıcı</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class='bx bxs-wallet'></i>
                            </div>
                            <div class="stat-info">
                                <h3>₺<?php echo number_format($totalRevenue, 2); ?></h3>
                                <p>Toplam Gelir</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Rentals -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Son Kiralamalar</h5>
                        <a href="rentals.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
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
                                    <?php
                                    $recentRentals = $db->query("
                                        SELECT r.*, u.name as user_name, c.model as car_model,
                                               COALESCE(r.status, 'pending') as status
                                        FROM rentals r 
                                        JOIN users u ON r.user_id = u.id 
                                        JOIN cars c ON r.car_id = c.id 
                                        ORDER BY r.created_at DESC 
                                        LIMIT 5
                                    ")->fetchAll();

                                    if (count($recentRentals) > 0):
                                        foreach ($recentRentals as $rental): 
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
                                        ?>
                                        <tr>
                                            <td>#<?php echo $rental['id']; ?></td>
                                            <td><?php echo htmlspecialchars($rental['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($rental['car_model']); ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($rental['start_date'])); ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($rental['end_date'])); ?></td>
                                            <td>₺<?php echo number_format($rental['total_price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="rental-details.php?id=<?php echo $rental['id']; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class='bx bxs-detail'></i>
                                                    </a>
                                                    <?php if ($currentStatus === 'confirmed'): ?>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'completed')">
                                                        <i class='bx bx-check'></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if (in_array($currentStatus, ['pending', 'confirmed'])): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'cancelled')">
                                                        <i class='bx bx-x'></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Car Locations -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Araç Konumları</h5>
                        <a href="locations.php" class="btn btn-primary btn-sm">Tümünü Gör</a>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 400px;"></div>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Kiralama detaylarını görüntüle
        function viewRental(id) {
            fetch(`admin/api/rental-detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('#rentalModal .modal-body').innerHTML = data.html;
                        new bootstrap.Modal(document.getElementById('rentalModal')).show();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                });
        }
        
        // Kiralama durumunu güncelle
        function updateRentalStatus(id, status) {
            const statusText = status === 'completed' ? 'tamamlamak' : 'iptal etmek';
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
                        location.reload();
                    } else {
                        alert(data.message);
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
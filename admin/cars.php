<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Araçları getir
$cars = $db->query("
    SELECT c.*, 
           COUNT(r.id) as rental_count,
           GROUP_CONCAT(DISTINCT f.name) as features
    FROM cars c
    LEFT JOIN rentals r ON c.id = r.car_id
    LEFT JOIN car_features cf ON c.id = cf.car_id
    LEFT JOIN features f ON cf.feature_id = f.id
    GROUP BY c.id
    ORDER BY c.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araçlar | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
                    <h2>Araçlar</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                        <i class='bx bx-plus'></i> Yeni Araç Ekle
                    </button>
                </div>

                <!-- Araç Listesi -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Resim</th>
                                        <th>Marka</th>
                                        <th>Model</th>
                                        <th>Plaka</th>
                                        <th>Fiyat</th>
                                        <th>Durum</th>
                                        <th>Kiralama Sayısı</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cars as $car): ?>
                                    <tr>
                                        <td>#<?php echo $car['id']; ?></td>
                                        <td>
                                            <img src="../assets/images/cars/<?php echo $car['image']; ?>" 
                                                 alt="<?php echo $car['model']; ?>"
                                                 class="car-thumbnail">
                                        </td>
                                        <td><?php echo $car['brand']; ?></td>
                                        <td><?php echo $car['model']; ?></td>
                                        <td><?php echo $car['plate']; ?></td>
                                        <td>₺<?php echo number_format($car['price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $car['status'] == 'available' ? 'success' : 'warning'; ?>">
                                                <?php echo $car['status'] == 'available' ? 'Müsait' : 'Kirada'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $car['rental_count']; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        onclick="viewCar(<?php echo $car['id']; ?>)">
                                                    <i class='bx bxs-detail'></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning"
                                                        onclick="editCar(<?php echo $car['id']; ?>)">
                                                    <i class='bx bxs-edit'></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="deleteCar(<?php echo $car['id']; ?>)">
                                                    <i class='bx bxs-trash'></i>
                                                </button>
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

    <!-- Yeni Araç Ekleme Modal -->
    <div class="modal fade" id="addCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Araç Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCarForm" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Marka</label>
                                <input type="text" class="form-control" name="brand" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Plaka</label>
                                <input type="text" class="form-control" name="plate" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yıl</label>
                                <input type="number" class="form-control" name="year" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fiyat (Günlük)</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="status" required>
                                    <option value="available">Müsait</option>
                                    <option value="rented">Kirada</option>
                                    <option value="maintenance">Bakımda</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Özellikler</label>
                                <select class="form-select select2" name="features[]" multiple>
                                    <?php
                                    $features = $db->query("SELECT * FROM features ORDER BY name")->fetchAll();
                                    foreach ($features as $feature):
                                    ?>
                                    <option value="<?php echo $feature['id']; ?>">
                                        <?php echo $feature['name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Açıklama</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Araç Resmi</label>
                                <input type="file" class="form-control" name="image" accept="image/*" required>
                                <div class="mt-2">
                                    <img id="imagePreview" src="" alt="" style="max-width: 200px; display: none;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddCar()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Select2 Initialization
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        // Image Preview
        document.querySelector('input[name="image"]').addEventListener('change', function(e) {
            previewImage(this, 'imagePreview');
        });

        // Form Submit
        function submitAddCar() {
            const form = document.getElementById('addCarForm');
            if (!validateForm(form)) return;

            const formData = new FormData(form);
            addCar(formData);
        }
    </script>
</body>
</html> 
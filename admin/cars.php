<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Araçları getir
$cars = $db->query("
    SELECT c.*, 
           COUNT(r.id) as rental_count,
           GROUP_CONCAT(DISTINCT cf.feature_name) as features
    FROM cars c
    LEFT JOIN rentals r ON c.id = r.car_id
    LEFT JOIN car_features cf ON c.id = cf.car_id
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
                                        <th>Öne Çıkan</th>
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
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input featured-toggle" type="checkbox" 
                                                       data-car-id="<?php echo $car['id']; ?>"
                                                       <?php echo $car['is_featured'] ? 'checked' : ''; ?>>
                                            </div>
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
                                <label class="form-label">Vites</label>
                                <select class="form-select" name="transmission" required>
                                    <option value="automatic">Otomatik</option>
                                    <option value="manual">Manuel</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yakıt Tipi</label>
                                <select class="form-select" name="fuel_type" required>
                                    <option value="petrol">Benzin</option>
                                    <option value="diesel">Dizel</option>
                                    <option value="hybrid">Hibrit</option>
                                    <option value="electric">Elektrik</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kilometre</label>
                                <input type="number" class="form-control" name="mileage" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Günlük Fiyat</label>
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
                                <textarea class="form-control" id="features" name="features" rows="3" 
                                        placeholder="Örnek:&#10;Yağmur Sensörü&#10;Gündüz Farları&#10;Anahtarsız Giriş"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Araç Resmi</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label for="engine_size" class="form-label">Motor Hacmi</label>
                                <input type="text" class="form-control" id="engine_size" name="engine_size" required>
                            </div>

                            <div class="mb-3">
                                <label for="horsepower" class="form-label">Beygir Gücü</label>
                                <input type="number" class="form-control" id="horsepower" name="horsepower" required>
                            </div>

                            <div class="mb-3">
                                <label for="seats" class="form-label">Koltuk Sayısı</label>
                                <input type="number" class="form-control" id="seats" name="seats" required>
                            </div>

                            <div class="mb-3">
                                <label for="doors" class="form-label">Kapı Sayısı</label>
                                <input type="number" class="form-control" id="doors" name="doors" required>
                            </div>

                            <div class="mb-3">
                                <label for="luggage_capacity" class="form-label">Bagaj Hacmi</label>
                                <input type="text" class="form-control" id="luggage_capacity" name="luggage_capacity" required>
                            </div>

                            <h5 class="mt-4 mb-3">Donanım</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="air_conditioning" name="air_conditioning" checked>
                                        <label class="form-check-label" for="air_conditioning">Klima</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="abs" name="abs" checked>
                                        <label class="form-check-label" for="abs">ABS</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="esp" name="esp" checked>
                                        <label class="form-check-label" for="esp">ESP</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="cruise_control" name="cruise_control">
                                        <label class="form-check-label" for="cruise_control">Hız Sabitleyici</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="parking_sensors" name="parking_sensors">
                                        <label class="form-check-label" for="parking_sensors">Park Sensörü</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="reverse_camera" name="reverse_camera">
                                        <label class="form-check-label" for="reverse_camera">Geri Görüş Kamerası</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="bluetooth" name="bluetooth" checked>
                                        <label class="form-check-label" for="bluetooth">Bluetooth</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="navigation" name="navigation">
                                        <label class="form-check-label" for="navigation">Navigasyon</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="sunroof" name="sunroof">
                                        <label class="form-check-label" for="sunroof">Sunroof</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="leather_seats" name="leather_seats">
                                        <label class="form-check-label" for="leather_seats">Deri Koltuk</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="airbag_count" class="form-label">Hava Yastığı Sayısı</label>
                                <input type="number" class="form-control" id="airbag_count" name="airbag_count" value="2" required>
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

        // Form Validation
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Özel validasyonlar
            const year = form.querySelector('[name="year"]');
            if (year.value) {
                const yearNum = parseInt(year.value);
                if (yearNum < 1900 || yearNum > new Date().getFullYear()) {
                    year.classList.add('is-invalid');
                    isValid = false;
                }
            }

            const price = form.querySelector('[name="price"]');
            if (price.value) {
                const priceNum = parseFloat(price.value);
                if (priceNum <= 0) {
                    price.classList.add('is-invalid');
                    isValid = false;
                }
            }

            const mileage = form.querySelector('[name="mileage"]');
            if (mileage.value) {
                const mileageNum = parseInt(mileage.value);
                if (mileageNum < 0) {
                    mileage.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (!isValid) {
                showAlert('error', 'Lütfen tüm zorunlu alanları doğru şekilde doldurun.');
            }

            return isValid;
        }

        // Form Submit
        function submitAddCar() {
            const form = document.getElementById('addCarForm');
            if (!validateForm(form)) return;

            const formData = new FormData(form);
            addCar(formData);
        }

        // Add Car Function
        function addCar(formData) {
            // Submit butonunu devre dışı bırak
            const submitBtn = document.querySelector('#addCarModal .btn-primary');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Kaydediliyor...';

            fetch('api/add-car.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Sunucu yanıtı geçersiz: ' + text);
                }
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', error.message || 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
            })
            .finally(() => {
                // Submit butonunu tekrar aktif et
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Kaydet';
            });
        }

        // Input validation on change
        document.querySelectorAll('#addCarForm input, #addCarForm select').forEach(input => {
            input.addEventListener('change', function() {
                if (this.hasAttribute('required')) {
                    if (!this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                }
            });
        });

        // Delete Car Function
        function deleteCar(id) {
            if (confirm('Bu aracı silmek istediğinizden emin misiniz?')) {
                fetch('../api/delete-car.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.error || 'Bir hata oluştu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu');
                });
            }
        }

        // Öne çıkan araç durumunu güncelle
        document.querySelectorAll('.featured-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const carId = this.dataset.carId;
                const isFeatured = this.checked;

                fetch('api/update-featured.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        car_id: carId,
                        is_featured: isFeatured
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Bir hata oluştu!');
                        this.checked = !isFeatured; // Toggle'ı eski haline getir
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu!');
                    this.checked = !isFeatured; // Toggle'ı eski haline getir
                });
            });
        });

        // Araç Detaylarını Görüntüleme Fonksiyonu
        function viewCar(id) {
            window.location.href = `car-details.php?id=${id}`;
        }

        // Araç Düzenleme Sayfasına Yönlendirme Fonksiyonu
        function editCar(id) {
            window.location.href = `edit-car.php?id=${id}`;
        }
    </script>
</body>
</html> 
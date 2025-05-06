<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araçlarımız | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <!-- Filtreler -->
                    <div class="col-lg-3">
                        <div class="filter-container p-4 bg-white rounded-3 shadow-sm">
                            <h4 class="mb-4">Filtreler</h4>
                            
                            <!-- Araç Tipi -->
                            <div class="mb-4">
                                <h6 class="mb-3">Araç Tipi</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sedan">
                                    <label class="form-check-label" for="sedan">Sedan</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="suv">
                                    <label class="form-check-label" for="suv">SUV</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="hatchback">
                                    <label class="form-check-label" for="hatchback">Hatchback</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="station">
                                    <label class="form-check-label" for="station">Station</label>
                                </div>
                            </div>

                            <!-- Fiyat Aralığı -->
                            <div class="mb-4">
                                <h6 class="mb-3">Fiyat Aralığı</h6>
                                <div class="range-slider">
                                    <input type="range" class="form-range" min="0" max="1000" step="50" id="priceRange">
                                    <div class="d-flex justify-content-between mt-2">
                                        <span>0₺</span>
                                        <span id="priceValue">500₺</span>
                                        <span>1000₺</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Vites Tipi -->
                            <div class="mb-4">
                                <h6 class="mb-3">Vites Tipi</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="transmission" id="manual">
                                    <label class="form-check-label" for="manual">Manuel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="transmission" id="automatic">
                                    <label class="form-check-label" for="automatic">Otomatik</label>
                                </div>
                            </div>

                            <!-- Yakıt Tipi -->
                            <div class="mb-4">
                                <h6 class="mb-3">Yakıt Tipi</h6>
                                <select class="form-select">
                                    <option selected>Tümü</option>
                                    <option>Benzin</option>
                                    <option>Dizel</option>
                                    <option>Elektrik</option>
                                    <option>Hibrit</option>
                                </select>
                            </div>

                            <button class="btn btn-custom w-100">Filtreleri Uygula</button>
                        </div>
                    </div>

                    <!-- Araç Listesi -->
                    <div class="col-lg-9">
                        <!-- Sıralama ve Görünüm -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <select class="form-select">
                                    <option selected>En Yeni</option>
                                    <option>Fiyat (Düşükten Yükseğe)</option>
                                    <option>Fiyat (Yüksekten Düşüğe)</option>
                                    <option>Popülerlik</option>
                                </select>
                            </div>
                            <div class="view-options">
                                <button class="btn btn-outline-custom active">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button class="btn btn-outline-custom">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Araç Kartları -->
                        <div class="row g-4">
                            <?php
                            // Örnek araç verileri
                            $cars = [
                                [
                                    'name' => 'Mercedes C200',
                                    'type' => 'Sedan',
                                    'price' => '850',
                                    'image' => 'assets/images/cars/mercedes-c200.jpg',
                                    'features' => ['Otomatik', 'Benzin', '5 Kişilik']
                                ],
                                [
                                    'name' => 'BMW X5',
                                    'type' => 'SUV',
                                    'price' => '1200',
                                    'image' => 'assets/images/cars/bmw-x5.jpg',
                                    'features' => ['Otomatik', 'Dizel', '5 Kişilik']
                                ],
                                // Diğer araçlar buraya eklenecek
                            ];

                            foreach ($cars as $car): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="car-card h-100">
                                    <div class="position-relative">
                                        <img src="<?php echo $car['image']; ?>" class="card-img-top" alt="<?php echo $car['name']; ?>">
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-3"><?php echo $car['type']; ?></span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $car['name']; ?></h5>
                                        <div class="d-flex gap-2 mb-3">
                                            <?php foreach ($car['features'] as $feature): ?>
                                                <span class="badge bg-light text-dark"><?php echo $feature; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="h5 mb-0"><?php echo $car['price']; ?>₺</span>
                                                <small class="text-muted">/gün</small>
                                            </div>
                                            <a href="car-details.php" class="btn btn-custom">Detaylar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Sayfalama -->
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Önceki</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Sonraki</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 
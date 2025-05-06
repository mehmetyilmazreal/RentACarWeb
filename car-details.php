<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercedes C200 | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <!-- Araç Başlık ve Özet Bilgiler -->
                <div class="row mb-5">
                    <div class="col-lg-8">
                        <h1 class="car-title mb-3">Mercedes C200</h1>
                        <div class="d-flex gap-3 mb-4">
                            <span class="badge bg-primary">Sedan</span>
                            <span class="badge bg-light text-dark">Otomatik</span>
                            <span class="badge bg-light text-dark">Benzin</span>
                            <span class="badge bg-light text-dark">5 Kişilik</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="price-tag">
                            <span class="h2 mb-0">850₺</span>
                            <small class="text-muted">/gün</small>
                        </div>
                        <a href="#booking" class="btn btn-custom btn-lg w-100 mt-3">Hemen Kirala</a>
                    </div>
                </div>

                <!-- 360 Derece Görüntüleme -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">360° Araç Görünümü</h3>
                                <div id="panorama" class="panorama-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Araç Galerisi -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="swiper car-gallery">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img src="assets/images/cars/mercedes-c200-1.jpg" alt="Mercedes C200" class="img-fluid rounded-3">
                                </div>
                                <div class="swiper-slide">
                                    <img src="assets/images/cars/mercedes-c200-2.jpg" alt="Mercedes C200" class="img-fluid rounded-3">
                                </div>
                                <div class="swiper-slide">
                                    <img src="assets/images/cars/mercedes-c200-3.jpg" alt="Mercedes C200" class="img-fluid rounded-3">
                                </div>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Araç Detayları -->
                    <div class="col-lg-8">
                        <!-- Özellikler -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Araç Özellikleri</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <div>
                                                <h6>Motor Gücü</h6>
                                                <p>184 HP</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-gas-pump"></i>
                                            <div>
                                                <h6>Yakıt Tüketimi</h6>
                                                <p>6.5L/100km</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-cog"></i>
                                            <div>
                                                <h6>Vites</h6>
                                                <p>9 İleri Otomatik</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-item">
                                            <i class="fas fa-car"></i>
                                            <div>
                                                <h6>0-100 km/s</h6>
                                                <p>7.3 saniye</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Donanım -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Donanım</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>LED Farlar</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Deri Döşeme</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Navigasyon</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bluetooth</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Park Sensörü</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Geri Görüş Kamerası</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Yağmur Sensörü</li>
                                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Klima</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Açıklama -->
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title h4 mb-4">Araç Hakkında</h3>
                                <p class="card-text">
                                    Mercedes C200, lüks ve konforu bir arada sunan premium bir sedan modelidir. 
                                    Modern tasarımı, gelişmiş teknolojik özellikleri ve üstün performansı ile 
                                    öne çıkan bu model, hem şehir içi hem de uzun yol kullanımı için ideal bir seçimdir.
                                </p>
                                <p class="card-text">
                                    Geniş iç mekanı, yüksek konfor seviyesi ve güvenlik özellikleri ile 
                                    sürücü ve yolcularına unutulmaz bir sürüş deneyimi sunar.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Kiralama Formu -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">Kiralama Formu</h5>
                                <?php if (!isset($_SESSION['user_id'])): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Kiralama işlemi yapabilmek için lütfen giriş yapın.
                                        <div class="mt-3">
                                            <a href="login.php" class="btn btn-custom btn-sm me-2">
                                                <i class="fas fa-sign-in-alt me-1"></i>Giriş Yap
                                            </a>
                                            <a href="register.php" class="btn btn-outline-custom btn-sm">
                                                <i class="fas fa-user-plus me-1"></i>Kayıt Ol
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form id="bookingForm" class="needs-validation" novalidate>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Alış Tarihi</label>
                                                <input type="date" class="form-control" id="start-date" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Dönüş Tarihi</label>
                                                <input type="date" class="form-control" id="end-date" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Alış Saati</label>
                                                <input type="time" class="form-control" id="start-time" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Dönüş Saati</label>
                                                <input type="time" class="form-control" id="end-time" required>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h6 class="mb-3">Ekstra Hizmetler</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input extra-service" type="checkbox" id="insurance" data-price="50">
                                                <label class="form-check-label" for="insurance">
                                                    Ekstra Sigorta (+50₺/gün)
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input extra-service" type="checkbox" id="gps" data-price="30">
                                                <label class="form-check-label" for="gps">
                                                    GPS Navigasyon (+30₺/gün)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input extra-service" type="checkbox" id="childSeat" data-price="20">
                                                <label class="form-check-label" for="childSeat">
                                                    Çocuk Koltuğu (+20₺/gün)
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>Günlük Ücret:</span>
                                                <span id="daily-price">500₺</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>Toplam Gün:</span>
                                                <span id="total-days">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <strong>Toplam Tutar:</strong>
                                                <strong id="total-price">0₺</strong>
                                            </div>
                                            <button type="submit" class="btn btn-custom w-100">
                                                <i class="fas fa-check me-2"></i>Kiralamayı Onayla
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOF - Out Of Breath | Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="hero-section">
            <div class="container">
                <h1 class="animate-fade-in">OOF Araç Kiralama</h1>
                <p class="animate-fade-in">Güvenilir ve konforlu araç kiralama hizmetleri</p>
                <div class="mt-4 animate-fade-in">
                    <a href="cars.php" class="btn btn-light btn-lg">
                        Araçları Keşfet
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Öne Çıkan Araçlar -->
        <section class="featured-cars py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Öne Çıkan Araçlar</h2>
                    <p class="section-subtitle text-muted">En popüler ve en çok tercih edilen araçlarımız</p>
                </div>
                
                <div class="row g-4">
                    <!-- Mercedes C200 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="car-card">
                            <div class="car-badge">Popüler</div>
                            <div class="car-image">
                                <img src="assets/images/cars/mercedes-c200.jpg" alt="Mercedes C200" class="img-fluid">
                                <div class="car-overlay">
                                    <a href="car-details.php?id=1" class="btn btn-light btn-sm">Detayları Gör</a>
                                </div>
                            </div>
                            <div class="car-content">
                                <div class="car-header">
                                    <h3 class="car-title">Mercedes C200</h3>
                                    <div class="car-price">₺1.200<span>/gün</span></div>
                                </div>
                                <div class="car-features">
                                    <span><i class="fas fa-gas-pump"></i> Benzin</span>
                                    <span><i class="fas fa-cog"></i> Otomatik</span>
                                    <span><i class="fas fa-tachometer-alt"></i> 0 KM</span>
                                </div>
                                <div class="car-footer">
                                    <a href="car-details.php?id=1" class="btn btn-primary w-100">Hemen Kirala</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BMW 320i -->
                    <div class="col-md-6 col-lg-4">
                        <div class="car-card">
                            <div class="car-badge">Yeni</div>
                            <div class="car-image">
                                <img src="assets/images/cars/bmw-320i.jpg" alt="BMW 320i" class="img-fluid">
                                <div class="car-overlay">
                                    <a href="car-details.php?id=2" class="btn btn-light btn-sm">Detayları Gör</a>
                                </div>
                            </div>
                            <div class="car-content">
                                <div class="car-header">
                                    <h3 class="car-title">BMW 320i</h3>
                                    <div class="car-price">₺1.100<span>/gün</span></div>
                                </div>
                                <div class="car-features">
                                    <span><i class="fas fa-gas-pump"></i> Benzin</span>
                                    <span><i class="fas fa-cog"></i> Otomatik</span>
                                    <span><i class="fas fa-tachometer-alt"></i> 0 KM</span>
                                </div>
                                <div class="car-footer">
                                    <a href="car-details.php?id=2" class="btn btn-primary w-100">Hemen Kirala</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audi A4 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="car-card">
                            <div class="car-badge">Premium</div>
                            <div class="car-image">
                                <img src="assets/images/cars/audi-a4.jpg" alt="Audi A4" class="img-fluid">
                                <div class="car-overlay">
                                    <a href="car-details.php?id=3" class="btn btn-light btn-sm">Detayları Gör</a>
                                </div>
                            </div>
                            <div class="car-content">
                                <div class="car-header">
                                    <h3 class="car-title">Audi A4</h3>
                                    <div class="car-price">₺1.150<span>/gün</span></div>
                                </div>
                                <div class="car-features">
                                    <span><i class="fas fa-gas-pump"></i> Benzin</span>
                                    <span><i class="fas fa-cog"></i> Otomatik</span>
                                    <span><i class="fas fa-tachometer-alt"></i> 0 KM</span>
                                </div>
                                <div class="car-footer">
                                    <a href="car-details.php?id=3" class="btn btn-primary w-100">Hemen Kirala</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="cars.php" class="btn btn-outline-custom btn-lg">
                        Tüm Araçları Gör
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="section-title text-start">Neden Bizi Seçmelisiniz?</h2>
                        <div class="d-flex gap-3 mb-3">
                            <div class="feature-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div>
                                <h5>Geniş Araç Filosu</h5>
                                <p class="text-muted">Her ihtiyaca uygun araç seçenekleri</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mb-3">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h5>Güvenli Kiralama</h5>
                                <p class="text-muted">%100 güvenli ödeme ve kiralama süreci</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h5>7/24 Destek</h5>
                                <p class="text-muted">Her zaman yanınızda müşteri hizmetleri</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="assets/images/why-us.jpg" alt="Neden Biz" class="img-fluid rounded-3">
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
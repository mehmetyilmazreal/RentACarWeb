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
                    <a href="cars.php" class="btn btn-custom btn-lg">Araçları Keşfet</a>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <h2 class="section-title">Öne Çıkan Araçlar</h2>
                <div class="row g-4">
                    <?php include 'includes/featured-cars.php'; ?>
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
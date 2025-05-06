<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <!-- Hero Section -->
        <section class="hero-section py-5 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-4 text-white">İletişim</h1>
                        <p class="lead text-white mb-4">
                            Sorularınız, önerileriniz veya geri bildirimleriniz için bizimle iletişime geçebilirsiniz.
                            Size en kısa sürede dönüş yapacağız.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/images/contact-hero.jpg" alt="İletişim" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </section>

        <!-- İletişim Bilgileri -->
        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fas fa-map-marker-alt text-primary fa-2x"></i>
                                </div>
                                <h4 class="h5 mb-3">Adres</h4>
                                <p class="text-muted mb-0">
                                    Atatürk Caddesi No:123<br>
                                    Kadıköy, İstanbul
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fas fa-phone text-primary fa-2x"></i>
                                </div>
                                <h4 class="h5 mb-3">Telefon</h4>
                                <p class="text-muted mb-0">
                                    +90 (216) 123 45 67<br>
                                    +90 (532) 987 65 43
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fas fa-envelope text-primary fa-2x"></i>
                                </div>
                                <h4 class="h5 mb-3">E-posta</h4>
                                <p class="text-muted mb-0">
                                    info@oofkiralama.com<br>
                                    destek@oofkiralama.com
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- İletişim Formu ve Harita -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="h4 mb-4">Bize Ulaşın</h3>
                                <form id="contactForm" class="needs-validation" novalidate>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Adınız</label>
                                            <input type="text" class="form-control" name="name" required>
                                            <div class="invalid-feedback">Lütfen adınızı giriniz.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">E-posta</label>
                                            <input type="email" class="form-control" name="email" required>
                                            <div class="invalid-feedback">Lütfen geçerli bir e-posta adresi giriniz.</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Konu</label>
                                            <input type="text" class="form-control" name="subject" required>
                                            <div class="invalid-feedback">Lütfen bir konu giriniz.</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Mesajınız</label>
                                            <textarea class="form-control" name="message" rows="5" required></textarea>
                                            <div class="invalid-feedback">Lütfen mesajınızı giriniz.</div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-custom">
                                                <i class="fas fa-paper-plane me-2"></i>Gönder
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-0">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.6504900120997!2d29.02885831541467!3d40.99030797929957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab8679bfb3d31%3A0x7d75715e081dfa5c!2sKad%C4%B1k%C3%B6y%2F%C4%B0stanbul!5e0!3m2!1str!2str!4v1647881234567!5m2!1str!2str" 
                                    width="100%" 
                                    height="450" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Çalışma Saatleri -->
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="h4 text-center mb-4">Çalışma Saatleri</h3>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Pazartesi - Cuma:</span>
                                            <span class="fw-medium">09:00 - 18:00</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Cumartesi:</span>
                                            <span class="fw-medium">10:00 - 16:00</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Pazar:</span>
                                            <span class="fw-medium">Kapalı</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Resmi Tatiller:</span>
                                            <span class="fw-medium">Kapalı</span>
                                        </div>
                                    </div>
                                </div>
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
    <script src="assets/js/main.js"></script>
</body>
</html> 
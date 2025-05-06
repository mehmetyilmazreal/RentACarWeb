<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h1 class="text-center mb-4">Şifremi Unuttum</h1>
                                
                                <div class="text-center mb-4">
                                    <p class="text-muted">
                                        E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.
                                    </p>
                                </div>

                                <form id="forgotPasswordForm" class="needs-validation" novalidate>
                                    <div class="mb-4">
                                        <label class="form-label">E-posta</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                        <div class="invalid-feedback">Geçerli bir e-posta adresi giriniz.</div>
                                    </div>

                                    <button type="submit" class="btn btn-custom w-100 mb-3">
                                        <i class="fas fa-paper-plane me-2"></i>Şifre Sıfırlama Bağlantısı Gönder
                                    </button>

                                    <div class="text-center">
                                        <p class="mb-0">
                                            <a href="login.php" class="text-decoration-none">
                                                <i class="fas fa-arrow-left me-1"></i>Giriş sayfasına dön
                                            </a>
                                        </p>
                                    </div>
                                </form>

                                <!-- Bilgi Kutusu -->
                                <div class="alert alert-info mt-4 mb-0">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fas fa-info-circle me-2"></i>Bilgilendirme
                                    </h6>
                                    <p class="mb-0 small">
                                        Şifre sıfırlama bağlantısı e-posta adresinize gönderilecektir. 
                                        E-postanızı kontrol etmeyi unutmayın. Spam klasörünü de kontrol etmenizi öneririz.
                                    </p>
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
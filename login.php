<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | OOF Araç Kiralama</title>
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
                                <h1 class="text-center mb-4">Giriş Yap</h1>
                                
                                <form id="loginForm" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label">E-posta</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                        <div class="invalid-feedback">Geçerli bir e-posta adresi giriniz.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Şifre</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" name="password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Şifrenizi giriniz.</div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">
                                                Beni hatırla
                                            </label>
                                        </div>
                                        <a href="forgot-password.php" class="text-decoration-none">Şifremi unuttum</a>
                                    </div>

                                    <button type="submit" class="btn btn-custom w-100 mb-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                                    </button>

                                    <div class="text-center">
                                        <p class="mb-0">Hesabınız yok mu? <a href="register.php" class="text-decoration-none">Kayıt Ol</a></p>
                                    </div>
                                </form>

                                <!-- Sosyal Medya ile Giriş -->
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <hr class="flex-grow-1">
                                        <span class="mx-3 text-muted">veya</span>
                                        <hr class="flex-grow-1">
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-dark">
                                            <i class="fab fa-google me-2"></i>Google ile Giriş Yap
                                        </button>
                                        <button type="button" class="btn btn-outline-primary">
                                            <i class="fab fa-facebook-f me-2"></i>Facebook ile Giriş Yap
                                        </button>
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
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | OOF Araç Kiralama</title>
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
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h1 class="text-center mb-4">Kayıt Ol</h1>
                                
                                <!-- Kayıt Türü Seçimi -->
                                <div class="register-type mb-4">
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="registerType" id="individual" value="individual" checked>
                                        <label class="btn btn-outline-primary" for="individual">
                                            <i class="fas fa-user me-2"></i>Bireysel
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="registerType" id="corporate" value="corporate">
                                        <label class="btn btn-outline-primary" for="corporate">
                                            <i class="fas fa-building me-2"></i>Kurumsal
                                        </label>
                                    </div>
                                </div>

                                <form id="registerForm" class="needs-validation" novalidate>
                                    <!-- Bireysel Form Alanları -->
                                    <div id="individualFields">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Ad</label>
                                                <input type="text" class="form-control" name="firstName" required>
                                                <div class="invalid-feedback">Lütfen adınızı giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Soyad</label>
                                                <input type="text" class="form-control" name="lastName" required>
                                                <div class="invalid-feedback">Lütfen soyadınızı giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">E-posta</label>
                                                <input type="email" class="form-control" name="email" required>
                                                <div class="invalid-feedback">Geçerli bir e-posta adresi giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Telefon</label>
                                                <input type="tel" class="form-control" name="phone" required>
                                                <div class="invalid-feedback">Geçerli bir telefon numarası giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Şifre</label>
                                                <input type="password" class="form-control" name="password" required>
                                                <div class="invalid-feedback">Şifrenizi giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Şifre Tekrar</label>
                                                <input type="password" class="form-control" name="passwordConfirm" required>
                                                <div class="invalid-feedback">Şifrenizi tekrar giriniz.</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">TC Kimlik No</label>
                                                <input type="text" class="form-control" name="tcNo" required>
                                                <div class="invalid-feedback">Geçerli bir TC kimlik numarası giriniz.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kurumsal Form Alanları -->
                                    <div id="corporateFields" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Firma Adı</label>
                                                <input type="text" class="form-control" name="companyName">
                                                <div class="invalid-feedback">Firma adını giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Vergi Dairesi</label>
                                                <input type="text" class="form-control" name="taxOffice">
                                                <div class="invalid-feedback">Vergi dairesini giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Vergi Numarası</label>
                                                <input type="text" class="form-control" name="taxNumber">
                                                <div class="invalid-feedback">Vergi numarasını giriniz.</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Firma Adresi</label>
                                                <textarea class="form-control" name="companyAddress" rows="3"></textarea>
                                                <div class="invalid-feedback">Firma adresini giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Yetkili Adı</label>
                                                <input type="text" class="form-control" name="authorizedName">
                                                <div class="invalid-feedback">Yetkili adını giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Yetkili Soyadı</label>
                                                <input type="text" class="form-control" name="authorizedSurname">
                                                <div class="invalid-feedback">Yetkili soyadını giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">E-posta</label>
                                                <input type="email" class="form-control" name="corporateEmail">
                                                <div class="invalid-feedback">Geçerli bir e-posta adresi giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Telefon</label>
                                                <input type="tel" class="form-control" name="corporatePhone">
                                                <div class="invalid-feedback">Geçerli bir telefon numarası giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Şifre</label>
                                                <input type="password" class="form-control" name="corporatePassword">
                                                <div class="invalid-feedback">Şifrenizi giriniz.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Şifre Tekrar</label>
                                                <input type="password" class="form-control" name="corporatePasswordConfirm">
                                                <div class="invalid-feedback">Şifrenizi tekrar giriniz.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ortak Alanlar -->
                                    <div class="row g-3 mt-3">
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="terms" required>
                                                <label class="form-check-label" for="terms">
                                                    <a href="#" class="text-decoration-none">Kullanım koşullarını</a> ve 
                                                    <a href="#" class="text-decoration-none">gizlilik politikasını</a> okudum ve kabul ediyorum.
                                                </label>
                                                <div class="invalid-feedback">
                                                    Devam etmek için kullanım koşullarını kabul etmelisiniz.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="marketing" checked>
                                                <label class="form-check-label" for="marketing">
                                                    Kampanya ve fırsatlardan haberdar olmak istiyorum.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-custom w-100">Kayıt Ol</button>
                                        </div>
                                        <div class="col-12 text-center">
                                            <p class="mb-0">Zaten hesabınız var mı? <a href="login.php" class="text-decoration-none">Giriş Yap</a></p>
                                        </div>
                                    </div>
                                </form>
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
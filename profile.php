<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once 'config/db.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı bilgilerini getir
$stmt = query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
$user = $stmt->fetch();

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // İsim kontrolü
    if (empty($name)) {
        $errors[] = 'Ad Soyad alanı boş bırakılamaz.';
    }
    
    // Telefon kontrolü
    if (empty($phone)) {
        $errors[] = 'Telefon alanı boş bırakılamaz.';
    }
    
    // Şifre değişikliği yapılacaksa
    if (!empty($current_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Mevcut şifre yanlış.';
        }
        
        if (empty($new_password)) {
            $errors[] = 'Yeni şifre alanı boş bırakılamaz.';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Yeni şifre en az 6 karakter olmalıdır.';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'Yeni şifreler eşleşmiyor.';
        }
    }
    
    // Hata yoksa güncelle
    if (empty($errors)) {
        $params = [$name, $phone];
        $sql = "UPDATE users SET name = ?, phone = ?";
        
        // Şifre değişikliği varsa
        if (!empty($new_password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];
        
        query($sql, $params);
        
        // Oturum bilgilerini güncelle
        $_SESSION['user_name'] = $name;
        
        $success = 'Profil bilgileriniz başarıyla güncellendi.';
        
        // Güncel kullanıcı bilgilerini al
        $stmt = query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim | OOF Araç Kiralama</title>
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
                                <h1 class="text-center mb-4">Profilim</h1>
                                
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo htmlspecialchars($error); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($success)): ?>
                                    <div class="alert alert-success">
                                        <?php echo htmlspecialchars($success); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label">E-posta</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                        <small class="text-muted">E-posta adresi değiştirilemez.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ad Soyad</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Kullanıcı Tipi</label>
                                        <input type="text" class="form-control" value="<?php echo $user['type'] === 'individual' ? 'Bireysel' : 'Kurumsal'; ?>" disabled>
                                    </div>
                                    
                                    <?php if ($user['type'] === 'individual'): ?>
                                        <div class="mb-3">
                                            <label class="form-label">TC Kimlik No</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['tc_no']); ?>" disabled>
                                            <small class="text-muted">TC Kimlik No değiştirilemez.</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <hr class="my-4">
                                    
                                    <h5 class="mb-3">Şifre Değiştir</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Mevcut Şifre</label>
                                        <input type="password" class="form-control" name="current_password">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Yeni Şifre</label>
                                        <input type="password" class="form-control" name="new_password">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Yeni Şifre (Tekrar)</label>
                                        <input type="password" class="form-control" name="confirm_password">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-custom w-100">
                                        <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                                    </button>
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
<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Gelen ham veriyi al
$raw_input = file_get_contents('php://input');

// Debug için gelen veriyi logla
error_log('Gelen ham veri: ' . $raw_input);

// Form verilerini al
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'true';

// Boş alan kontrolü
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Lütfen tüm alanları doldurun.'
    ]);
    exit;
}

try {
    // Kullanıcıyı veritabanında ara
    $stmt = query("SELECT * FROM users WHERE email = ?", [$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'active') {
            // Oturum başlat
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Oturum bilgilerini kaydet
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $user['type'];
            
            // Beni hatırla seçeneği işaretliyse
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Token'ı veritabanına kaydet
                query("
                    INSERT INTO remember_tokens (user_id, token, expires_at)
                    VALUES (?, ?, ?)
                ", [$user['id'], $token, $expires]);
                
                // Cookie'ye kaydet
                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Giriş başarılı.',
                'redirect' => $user['type'] === 'corporate' ? 'admin/index.php' : 'index.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Hesabınız aktif değil.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Geçersiz e-posta veya şifre.'
        ]);
    }
} catch (Exception $e) {
    error_log('Giriş hatası: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.',
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} 
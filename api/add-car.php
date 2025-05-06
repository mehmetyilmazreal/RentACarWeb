<?php
// Çıktı tamponlamasını başlat
ob_start();

// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1); // Hataları logla
ini_set('error_log', '../logs/php-error.log'); // Log dosyası

// Oturum başlat
session_start();

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Test yanıtı
echo json_encode([
    'success' => true,
    'message' => 'Test başarılı'
]);
exit;

try {
    // Veritabanı bağlantısını dahil et
    require_once '../config/db.php';
    require_once '../admin/includes/auth_check.php';

    // Yetki kontrolü
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'corporate') {
        throw new Exception('Bu işlem için yetkiniz yok.');
    }

    // Veritabanı bağlantısını kontrol et
    if (!isset($db) || !($db instanceof PDO)) {
        throw new Exception('Veritabanı bağlantısı bulunamadı.');
    }

    // Gerekli alanları kontrol et
    $required_fields = ['brand', 'model', 'plate', 'year', 'price', 'status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception('Lütfen tüm zorunlu alanları doldurun.', $field);
        }
    }

    // Resim yükleme kontrolü
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Lütfen bir araç resmi yükleyin.');
    }

    // Resim dosyasını kontrol et
    $image = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($image['type'], $allowed_types)) {
        throw new Exception('Sadece JPG, JPEG ve PNG formatında resimler yükleyebilirsiniz.');
    }

    // Resim boyutunu kontrol et (max 5MB)
    if ($image['size'] > 5 * 1024 * 1024) {
        throw new Exception('Resim boyutu 5MB\'dan büyük olamaz.');
    }

    // Resim adını oluştur
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $image_name = uniqid() . '.' . $extension;
    $upload_path = '../assets/images/cars/' . $image_name;

    // Resmi yükle
    if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
        throw new Exception('Resim yüklenirken bir hata oluştu.');
    }

    // Araç bilgilerini veritabanına ekle
    $sql = "INSERT INTO cars (
                brand, model, plate, year, daily_price, status, 
                transmission, fuel_type, mileage, description, image, 
                created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                NOW(), NOW()
            )";

    $params = [
        $_POST['brand'],
        $_POST['model'],
        $_POST['plate'],
        $_POST['year'],
        $_POST['price'],
        $_POST['status'],
        $_POST['transmission'] ?? 'automatic',
        $_POST['fuel_type'] ?? 'petrol',
        $_POST['mileage'] ?? 0,
        $_POST['description'] ?? '',
        $image_name
    ];

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $car_id = $db->lastInsertId();

    // Özellikleri ekle
    if (!empty($_POST['features'])) {
        $feature_sql = "INSERT INTO car_features (car_id, feature_id) VALUES (?, ?)";
        $feature_stmt = $db->prepare($feature_sql);
        foreach ($_POST['features'] as $feature_id) {
            $feature_stmt->execute([$car_id, $feature_id]);
        }
    }

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Araç başarıyla eklendi.'
    ]);

} catch (Exception $e) {
    // Hata durumunda yüklenen resmi sil
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }

    // Hata logla
    error_log('Araç ekleme hatası: ' . $e->getMessage());

    // Hata yanıtı
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'field' => $e->getCode() ?: null
    ]);
}

// Çıktı tamponlamasını sonlandır ve gönder
ob_end_flush(); 
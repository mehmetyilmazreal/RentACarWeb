<?php
// Çıktı tamponlamasını başlat
ob_start();

// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1); // Hataları logla
ini_set('error_log', '../../logs/php-error.log'); // Log dosyası

// Oturum başlat
session_start();

// JSON yanıt için header ayarla
header('Content-Type: application/json');

try {
    // Veritabanı bağlantısını dahil et
    require_once '../../config/db.php';
    require_once '../includes/auth_check.php';

    // Yetki kontrolü
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'corporate') {
        throw new Exception('Bu işlem için yetkiniz yok.');
    }

    // Veritabanı bağlantısını kontrol et
    if (!isset($db) || !($db instanceof PDO)) {
        throw new Exception('Veritabanı bağlantısı bulunamadı.');
    }

    // Form verilerini al
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $plate = $_POST['plate'] ?? '';
    $year = $_POST['year'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $transmission = $_POST['transmission'] ?? 'automatic';
    $fuel_type = $_POST['fuel_type'] ?? 'petrol';
    $mileage = $_POST['mileage'] ?? 0;
    $description = $_POST['description'] ?? '';
    
    // Yeni özellikler
    $engine_size = $_POST['engine_size'] ?? '';
    $horsepower = $_POST['horsepower'] ?? 0;
    $seats = $_POST['seats'] ?? 5;
    $doors = $_POST['doors'] ?? 4;
    $luggage_capacity = $_POST['luggage_capacity'] ?? '';
    $air_conditioning = isset($_POST['air_conditioning']) ? 1 : 0;
    $abs = isset($_POST['abs']) ? 1 : 0;
    $esp = isset($_POST['esp']) ? 1 : 0;
    $airbag_count = $_POST['airbag_count'] ?? 2;
    $cruise_control = isset($_POST['cruise_control']) ? 1 : 0;
    $parking_sensors = isset($_POST['parking_sensors']) ? 1 : 0;
    $reverse_camera = isset($_POST['reverse_camera']) ? 1 : 0;
    $bluetooth = isset($_POST['bluetooth']) ? 1 : 0;
    $navigation = isset($_POST['navigation']) ? 1 : 0;
    $sunroof = isset($_POST['sunroof']) ? 1 : 0;
    $leather_seats = isset($_POST['leather_seats']) ? 1 : 0;
    $features = $_POST['features'] ?? '';

    // Zorunlu alanları kontrol et
    if (!$brand || !$model || !$plate || !$year || !$price || !$engine_size || !$horsepower || !$seats || !$doors || !$luggage_capacity) {
        throw new Exception('Lütfen tüm zorunlu alanları doldurun.');
    }

    // Resim yükleme kontrolü
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Lütfen bir araç resmi yükleyin.');
    }

    // Resim dosyasını kontrol et
    $image = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($image['type'], $allowed_types)) {
        throw new Exception('Sadece JPEG, PNG ve WEBP formatında resimler yüklenebilir.');
    }

    // Resim boyutunu kontrol et (max 5MB)
    if ($image['size'] > 5 * 1024 * 1024) {
        throw new Exception('Resim boyutu 5MB\'dan büyük olamaz.');
    }

    // Resim dosya adını oluştur
    $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $image_name = uniqid() . '.' . $image_extension;
    $upload_dir = '../../assets/images/cars/';
    
    // Klasör yoksa oluştur
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Resmi yükle
    if (!move_uploaded_file($image['tmp_name'], $upload_dir . $image_name)) {
        throw new Exception('Resim yüklenirken bir hata oluştu.');
    }

    // Veritabanına araç bilgilerini ekle
    $sql = "INSERT INTO cars (
                brand, model, plate, year, price, status, 
                transmission, fuel_type, mileage, description, image,
                engine_size, horsepower, seats, doors, luggage_capacity,
                air_conditioning, abs, esp, airbag_count, cruise_control,
                parking_sensors, reverse_camera, bluetooth, navigation,
                sunroof, leather_seats, created_at, updated_at
            ) VALUES (
                :brand, :model, :plate, :year, :price, :status,
                :transmission, :fuel_type, :mileage, :description, :image,
                :engine_size, :horsepower, :seats, :doors, :luggage_capacity,
                :air_conditioning, :abs, :esp, :airbag_count, :cruise_control,
                :parking_sensors, :reverse_camera, :bluetooth, :navigation,
                :sunroof, :leather_seats, NOW(), NOW()
            )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':brand' => $brand,
        ':model' => $model,
        ':plate' => $plate,
        ':year' => $year,
        ':price' => $price,
        ':status' => $status,
        ':transmission' => $transmission,
        ':fuel_type' => $fuel_type,
        ':mileage' => $mileage,
        ':description' => $description,
        ':image' => $image_name,
        ':engine_size' => $engine_size,
        ':horsepower' => $horsepower,
        ':seats' => $seats,
        ':doors' => $doors,
        ':luggage_capacity' => $luggage_capacity,
        ':air_conditioning' => $air_conditioning,
        ':abs' => $abs,
        ':esp' => $esp,
        ':airbag_count' => $airbag_count,
        ':cruise_control' => $cruise_control,
        ':parking_sensors' => $parking_sensors,
        ':reverse_camera' => $reverse_camera,
        ':bluetooth' => $bluetooth,
        ':navigation' => $navigation,
        ':sunroof' => $sunroof,
        ':leather_seats' => $leather_seats
    ]);

    $car_id = $db->lastInsertId();

    // Ek özellikleri ekle
    if ($features) {
        $feature_lines = explode("\n", $features);
        $feature_sql = "INSERT INTO car_features (car_id, feature_name, feature_value) VALUES (:car_id, :feature_name, :feature_value)";
        $feature_stmt = $db->prepare($feature_sql);
        
        foreach ($feature_lines as $feature) {
            $feature = trim($feature);
            if (!empty($feature)) {
                $feature_stmt->execute([
                    ':car_id' => $car_id,
                    ':feature_name' => $feature,
                    ':feature_value' => 'true'
                ]);
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Araç başarıyla eklendi.',
        'car_id' => $car_id
    ]);

} catch (Exception $e) {
    // Hata durumunda yüklenen resmi sil
    if (isset($image_name) && file_exists($upload_dir . $image_name)) {
        unlink($upload_dir . $image_name);
    }

    // Hata logla
    error_log('Araç ekleme hatası: ' . $e->getMessage());

    // Hata yanıtı
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Çıktı tamponlamasını sonlandır ve gönder
ob_end_flush(); 
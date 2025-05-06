<?php
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Yetki kontrolü
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'corporate') {
        throw new Exception('Bu işlem için yetkiniz yok.');
    }

    // Araç ID'sini al
    $car_id = $_POST['car_id'] ?? null;
    if (!$car_id) {
        throw new Exception('Araç ID\'si gerekli.');
    }

    // Önce araç bilgilerini al (resim silmek için)
    $stmt = $db->prepare("SELECT image FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();

    if (!$car) {
        throw new Exception('Araç bulunamadı.');
    }

    // Veritabanından araç özelliklerini sil
    $stmt = $db->prepare("DELETE FROM car_features WHERE car_id = ?");
    $stmt->execute([$car_id]);

    // Veritabanından araç donanımını sil
    $stmt = $db->prepare("DELETE FROM car_equipment WHERE car_id = ?");
    $stmt->execute([$car_id]);

    // Veritabanından aracı sil
    $stmt = $db->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);

    // Araç resmini sil
    $image_path = '../../assets/images/cars/' . $car['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Araç başarıyla silindi.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
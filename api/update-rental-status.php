<?php
// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log dosyası
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

// Çıktı tamponlamasını başlat
ob_start();

session_start();
require_once '../config/db.php';

// JSON yanıtı için header
header('Content-Type: application/json; charset=utf-8');

// Gelen veriyi logla
error_log("Received data: " . file_get_contents('php://input'));

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'corporate') {
    error_log("Unauthorized access attempt");
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Yetkisiz erişim'
    ]);
    exit;
}

// POST verilerini al
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// JSON decode hatası kontrolü
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz JSON verisi: ' . json_last_error_msg()
    ]);
    exit;
}

$rental_id = $data['rental_id'] ?? null;
$status = $data['status'] ?? null;

// Gerekli alanları kontrol et
if (!$rental_id || !$status) {
    error_log("Missing parameters - rental_id: $rental_id, status: $status");
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Eksik parametreler'
    ]);
    exit;
}

// Geçerli durumları kontrol et
$validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if (!in_array($status, $validStatuses)) {
    error_log("Invalid status: $status");
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz durum'
    ]);
    exit;
}

try {
    // Kiralama kaydını kontrol et
    $stmt = $db->prepare("SELECT * FROM rentals WHERE id = ?");
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch();
    
    if (!$rental) {
        error_log("Rental not found: $rental_id");
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Kiralama bulunamadı'
        ]);
        exit;
    }

    // Durumu güncelle
    $stmt = $db->prepare("UPDATE rentals SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $rental_id]);
    
    if ($result === false) {
        error_log("Database update failed for rental: $rental_id");
        throw new Exception("Veritabanı güncelleme hatası");
    }

    error_log("Successfully updated rental $rental_id to status: $status");
    
    // Başarılı yanıt
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Kiralama durumu güncellendi'
    ]);

} catch (Exception $e) {
    error_log("Exception occurred: " . $e->getMessage());
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
}

// Çıktı tamponlamasını sonlandır
ob_end_flush(); 
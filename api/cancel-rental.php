<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Kullanıcı giriş yapmamışsa hata döndür
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Oturum açmanız gerekiyor.'
    ]);
    exit;
}

// POST verilerini al
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = $data['rental_id'] ?? 0;

if (!$rental_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz kiralama ID.'
    ]);
    exit;
}

try {
    // Kiralamayı kontrol et
    $stmt = query("SELECT * FROM rentals WHERE id = ? AND user_id = ?", [$rental_id, $_SESSION['user_id']]);
    $rental = $stmt->fetch();
    
    if (!$rental) {
        echo json_encode([
            'success' => false,
            'message' => 'Kiralama bulunamadı.'
        ]);
        exit;
    }
    
    // Sadece aktif kiralamalar iptal edilebilir
    if ($rental['status'] !== 'active') {
        echo json_encode([
            'success' => false,
            'message' => 'Sadece aktif kiralamalar iptal edilebilir.'
        ]);
        exit;
    }
    
    // Kiralamayı iptal et
    query("UPDATE rentals SET status = 'cancelled' WHERE id = ?", [$rental_id]);
    
    // Aracın durumunu güncelle
    query("UPDATE cars SET status = 'available' WHERE id = ?", [$rental['car_id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kiralama başarıyla iptal edildi.'
    ]);
} catch (Exception $e) {
    error_log('Kiralama iptal hatası: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.'
    ]);
} 
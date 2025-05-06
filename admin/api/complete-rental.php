<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);
$rental_id = $data['rental_id'] ?? null;

if (!$rental_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Kiralama ID\'si gerekli.'
    ]);
    exit;
}

try {
    // Kiralama durumunu kontrol et
    $rental = query("SELECT status FROM rentals WHERE id = ?", [$rental_id])->fetch();
    
    if (!$rental) {
        echo json_encode([
            'success' => false,
            'message' => 'Kiralama bulunamadı.'
        ]);
        exit;
    }

    if ($rental['status'] !== 'active') {
        echo json_encode([
            'success' => false,
            'message' => 'Sadece aktif kiralamalar tamamlanabilir.'
        ]);
        exit;
    }

    // Kiralama durumunu güncelle
    query("
        UPDATE rentals 
        SET status = 'completed', 
            updated_at = NOW() 
        WHERE id = ?
    ", [$rental_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Kiralama başarıyla tamamlandı.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
} 
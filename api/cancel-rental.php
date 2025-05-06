<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

try {
    // Oturum kontrolü
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Bu işlem için giriş yapmanız gerekiyor.');
    }

    // JSON verisini al
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['rental_id'])) {
        throw new Exception('Geçersiz veri.');
    }

    // Rezervasyonu kontrol et
    $stmt = $db->prepare("
        SELECT r.*, p.status as payment_status, p.amount 
        FROM rentals r
        LEFT JOIN payments p ON r.id = p.rental_id
        WHERE r.id = ? AND r.user_id = ? AND r.status = 'pending'
    ");
    $stmt->execute([$data['rental_id'], $_SESSION['user_id']]);
    $rental = $stmt->fetch();

    if (!$rental) {
        throw new Exception('Rezervasyon bulunamadı veya iptal edilemez.');
    }

    // Transaction başlat
    $db->beginTransaction();

    try {
        // Rezervasyonu iptal et
        $stmt = $db->prepare("UPDATE rentals SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$data['rental_id']]);

        // Transaction'ı tamamla
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Rezervasyonunuz başarıyla iptal edildi.'
        ]);

    } catch (Exception $e) {
        // Hata durumunda transaction'ı geri al
        $db->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
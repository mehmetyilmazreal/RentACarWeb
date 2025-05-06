<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

try {
    // Admin kontrolü
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'corporate') {
        throw new Exception('Bu işlem için yetkiniz yok.');
    }

    // JSON verisini al
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['rental_id']) || !isset($data['status'])) {
        throw new Exception('Geçersiz veri.');
    }

    // Geçerli durum kontrolü
    $valid_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
    if (!in_array($data['status'], $valid_statuses)) {
        throw new Exception('Geçersiz durum.');
    }

    // Rezervasyonu kontrol et
    $stmt = $db->prepare("SELECT status FROM rentals WHERE id = ?");
    $stmt->execute([$data['rental_id']]);
    $rental = $stmt->fetch();

    if (!$rental) {
        throw new Exception('Rezervasyon bulunamadı.');
    }

    // Transaction başlat
    $db->beginTransaction();

    try {
        // Rezervasyon durumunu güncelle
        $stmt = $db->prepare("UPDATE rentals SET status = 'completed' WHERE id = ?");
        $result = $stmt->execute([$data['rental_id']]);

        if (!$result) {
            throw new Exception('Rezervasyon durumu güncellenemedi.');
        }

        // Ödeme durumunu da güncelle
        // Önce ödeme kaydını kontrol et
        $stmt = $db->prepare("SELECT * FROM payments WHERE rental_id = ?");
        $stmt->execute([$data['rental_id']]);
        $payment = $stmt->fetch();

        if ($payment) {
            // Ödeme kaydı varsa güncelle
            $stmt = $db->prepare("UPDATE payments SET status = 'completed' WHERE rental_id = ?");
            $result = $stmt->execute([$data['rental_id']]);
            
            if (!$result) {
                throw new Exception('Ödeme durumu güncellenemedi.');
            }
        } else {
            // Ödeme kaydı yoksa yeni kayıt oluştur
            $stmt = $db->prepare("INSERT INTO payments (rental_id, status, payment_method, transaction_id, created_at) 
                                VALUES (?, 'completed', 'credit_card', ?, NOW())");
            $result = $stmt->execute([$data['rental_id'], 'ADMIN_' . time()]);
            
            if (!$result) {
                throw new Exception('Ödeme kaydı oluşturulamadı.');
            }
        }

        // Transaction'ı tamamla
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Rezervasyon durumu başarıyla güncellendi.'
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
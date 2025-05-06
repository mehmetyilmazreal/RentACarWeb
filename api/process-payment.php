<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Oturum kontrolü
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Bu işlem için giriş yapmanız gerekiyor.');
    }

    // Geçici rezervasyon bilgilerini kontrol et
    if (!isset($_SESSION['temp_rental'])) {
        throw new Exception('Rezervasyon bilgileri bulunamadı.');
    }

    // Form verilerini al
    $payment_method = $_POST['payment_method'] ?? null;

    if (!$payment_method) {
        throw new Exception('Geçersiz veri.');
    }

    // Kredi kartı ödemesi için ek kontroller
    if ($payment_method === 'credit_card') {
        $card_holder = $_POST['card_holder'] ?? null;
        $card_number = $_POST['card_number'] ?? null;
        $expiry_date = $_POST['expiry_date'] ?? null;
        $cvv = $_POST['cvv'] ?? null;

        if (!$card_holder || !$card_number || !$expiry_date || !$cvv) {
            throw new Exception('Lütfen tüm kart bilgilerini doldurun.');
        }

        // Kart numarası kontrolü
        if (strlen($card_number) !== 16) {
            throw new Exception('Geçersiz kart numarası.');
        }

        // Son kullanma tarihi kontrolü
        if (!preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
            throw new Exception('Geçersiz son kullanma tarihi.');
        }

        // CVV kontrolü
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            throw new Exception('Geçersiz CVV.');
        }
    }

    // Aracın hala müsait olup olmadığını kontrol et
    $stmt = $db->prepare("SELECT status FROM cars WHERE id = ?");
    $stmt->execute([$_SESSION['temp_rental']['car_id']]);
    $car = $stmt->fetch();

    if (!$car || $car['status'] !== 'available') {
        throw new Exception('Bu araç artık müsait değil.');
    }

    // Seçilen tarihlerde başka rezervasyon var mı kontrol et
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM rentals 
        WHERE car_id = ? 
        AND status != 'cancelled'
        AND (
            (start_date <= ? AND end_date >= ?) OR
            (start_date <= ? AND end_date >= ?) OR
            (start_date >= ? AND end_date <= ?)
        )
    ");
    $stmt->execute([
        $_SESSION['temp_rental']['car_id'],
        $_SESSION['temp_rental']['start_date'], $_SESSION['temp_rental']['start_date'],
        $_SESSION['temp_rental']['end_date'], $_SESSION['temp_rental']['end_date'],
        $_SESSION['temp_rental']['start_date'], $_SESSION['temp_rental']['end_date']
    ]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Seçilen tarihlerde bu araç başka bir müşteri tarafından rezerve edilmiş.');
    }

    // Transaction başlat
    $db->beginTransaction();

    try {
        // Rezervasyonu oluştur
        $stmt = $db->prepare("
            INSERT INTO rentals (
                car_id, user_id, start_date, end_date, 
                start_time, end_time, total_price, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $stmt->execute([
            $_SESSION['temp_rental']['car_id'],
            $_SESSION['user_id'],
            $_SESSION['temp_rental']['start_date'],
            $_SESSION['temp_rental']['end_date'],
            $_SESSION['temp_rental']['start_time'],
            $_SESSION['temp_rental']['end_time'],
            $_SESSION['temp_rental']['total_price']
        ]);

        $rental_id = $db->lastInsertId();

        // Ödeme kaydını oluştur
        $stmt = $db->prepare("
            INSERT INTO payments (
                rental_id, amount, payment_method, 
                card_number, card_holder, expiry_date, cvv,
                status, transaction_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', ?)
        ");

        $transaction_id = uniqid('TRX');
        $stmt->execute([
            $rental_id,
            $_SESSION['temp_rental']['total_price'],
            $payment_method,
            $payment_method === 'credit_card' ? substr($card_number, -4) : null,
            $payment_method === 'credit_card' ? $card_holder : null,
            $payment_method === 'credit_card' ? $expiry_date : null,
            $payment_method === 'credit_card' ? $cvv : null,
            $transaction_id
        ]);

        // Geçici rezervasyon bilgilerini temizle
        unset($_SESSION['temp_rental']);

        // Transaction'ı tamamla
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Ödeme başarıyla tamamlandı. Rezervasyonunuz onay bekliyor.',
            'transaction_id' => $transaction_id
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
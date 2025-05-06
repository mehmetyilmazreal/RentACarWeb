<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Oturum kontrolü
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Bu işlem için giriş yapmanız gerekiyor.');
    }

    // Form verilerini al
    $car_id = $_POST['car_id'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    // Zorunlu alanları kontrol et
    if (!$car_id || !$start_date || !$end_date || !$start_time || !$end_time) {
        throw new Exception('Lütfen tüm alanları doldurun.');
    }

    // Tarih formatlarını kontrol et
    $start_datetime = strtotime($start_date . ' ' . $start_time);
    $end_datetime = strtotime($end_date . ' ' . $end_time);
    
    if ($start_datetime >= $end_datetime) {
        throw new Exception('Dönüş tarihi ve saati, alış tarihi ve saatinden sonra olmalıdır.');
    }

    // Aracın müsait olup olmadığını kontrol et
    $stmt = $db->prepare("SELECT status, price FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();

    if (!$car) {
        throw new Exception('Araç bulunamadı.');
    }

    if ($car['status'] !== 'available') {
        throw new Exception('Bu araç şu anda müsait değil.');
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
        $car_id, 
        $start_date, $start_date,
        $end_date, $end_date,
        $start_date, $end_date
    ]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Seçilen tarihlerde bu araç başka bir müşteri tarafından rezerve edilmiş.');
    }

    // Toplam fiyatı hesapla
    $start = new DateTime($start_date . ' ' . $start_time);
    $end = new DateTime($end_date . ' ' . $end_time);
    $interval = $start->diff($end);
    $days = $interval->days + 1; // Gün sayısını hesapla (başlangıç günü dahil)
    $total_price = $days * $car['price'];

    // Geçici rezervasyon bilgilerini session'a kaydet
    $_SESSION['temp_rental'] = [
        'car_id' => $car_id,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'total_price' => $total_price
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Ödeme sayfasına yönlendiriliyorsunuz.',
        'redirect' => 'payment.php'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
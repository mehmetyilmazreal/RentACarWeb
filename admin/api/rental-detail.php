<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Kiralama ID'sini al
$rental_id = $_GET['id'] ?? null;

if (!$rental_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Kiralama ID\'si gerekli.'
    ]);
    exit;
}

try {
    // Kiralama detaylarını getir
    $rental = query("
        SELECT r.*, 
               u.name as user_name, 
               u.email as user_email,
               u.phone as user_phone,
               c.brand,
               c.model,
               c.plate,
               c.year as car_year
        FROM rentals r
        JOIN users u ON r.user_id = u.id
        JOIN cars c ON r.car_id = c.id
        WHERE r.id = ?
    ", [$rental_id])->fetch();

    if (!$rental) {
        echo json_encode([
            'success' => false,
            'message' => 'Kiralama bulunamadı.'
        ]);
        exit;
    }

    // Ekstra hizmetleri getir
    $extra_services = query("
        SELECT es.name, es.price
        FROM rental_extra_services res
        JOIN extra_services es ON res.extra_service_id = es.id
        WHERE res.rental_id = ?
    ", [$rental_id])->fetchAll();

    // HTML içeriğini oluştur
    $html = '
    <div class="row g-3">
        <div class="col-md-6">
            <h6>Müşteri Bilgileri</h6>
            <p>
                <strong>Ad Soyad:</strong> ' . htmlspecialchars($rental['user_name']) . '<br>
                <strong>E-posta:</strong> ' . htmlspecialchars($rental['user_email']) . '<br>
                <strong>Telefon:</strong> ' . htmlspecialchars($rental['user_phone']) . '
            </p>
        </div>
        <div class="col-md-6">
            <h6>Araç Bilgileri</h6>
            <p>
                <strong>Marka:</strong> ' . htmlspecialchars($rental['brand']) . '<br>
                <strong>Model:</strong> ' . htmlspecialchars($rental['model']) . '<br>
                <strong>Plaka:</strong> ' . htmlspecialchars($rental['plate']) . '<br>
                <strong>Yıl:</strong> ' . htmlspecialchars($rental['car_year']) . '
            </p>
        </div>
        <div class="col-md-6">
            <h6>Kiralama Detayları</h6>
            <p>
                <strong>Başlangıç:</strong> ' . date('d.m.Y H:i', strtotime($rental['start_date'] . ' ' . $rental['start_time'])) . '<br>
                <strong>Bitiş:</strong> ' . date('d.m.Y H:i', strtotime($rental['end_date'] . ' ' . $rental['end_time'])) . '<br>
                <strong>Toplam Gün:</strong> ' . $rental['total_days'] . '
            </p>
        </div>
        <div class="col-md-6">
            <h6>Ödeme Bilgileri</h6>
            <p>
                <strong>Toplam Tutar:</strong> ₺' . number_format($rental['total_price'], 2) . '<br>
                <strong>Ödeme Yöntemi:</strong> ' . ucfirst($rental['payment_method']) . '<br>
                <strong>Ödeme Durumu:</strong> ' . ucfirst($rental['payment_status']) . '
            </p>
        </div>';

    if (count($extra_services) > 0) {
        $html .= '
        <div class="col-12">
            <h6>Ekstra Hizmetler</h6>
            <ul class="list-group">';
        
        foreach ($extra_services as $service) {
            $html .= '
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ' . htmlspecialchars($service['name']) . '
                    <span class="badge bg-primary rounded-pill">₺' . number_format($service['price'], 2) . '</span>
                </li>';
        }
        
        $html .= '
            </ul>
        </div>';
    }

    $html .= '
    </div>';

    echo json_encode([
        'success' => true,
        'html' => $html
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
} 
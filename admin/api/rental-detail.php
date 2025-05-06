<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'corporate') {
    echo json_encode([
        'success' => false,
        'message' => 'Yetkisiz erişim'
    ]);
    exit;
}

// Kiralama ID'sini al
$rental_id = $_GET['id'] ?? null;

if (!$rental_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Kiralama ID\'si gerekli'
    ]);
    exit;
}

try {
    // Kiralama detaylarını getir
    $stmt = $db->prepare("
        SELECT r.*, 
               u.name as user_name, u.email as user_email, u.phone as user_phone,
               c.brand, c.model, c.plate, c.image as car_image,
               p.status as payment_status, p.payment_method, p.transaction_id
        FROM rentals r
        JOIN users u ON r.user_id = u.id
        JOIN cars c ON r.car_id = c.id
        LEFT JOIN payments p ON r.id = p.rental_id
        WHERE r.id = ?
    ");
    
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch();

    if (!$rental) {
        echo json_encode([
            'success' => false,
            'message' => 'Kiralama bulunamadı'
        ]);
        exit;
    }

    // Durum metinlerini tanımla
    $statusMap = [
        'pending' => [
            'class' => 'warning',
            'text' => 'Beklemede'
        ],
        'confirmed' => [
            'class' => 'success',
            'text' => 'Onaylandı'
        ],
        'completed' => [
            'class' => 'info',
            'text' => 'Tamamlandı'
        ],
        'cancelled' => [
            'class' => 'danger',
            'text' => 'İptal Edildi'
        ]
    ];

    $currentStatus = isset($rental['status']) && array_key_exists($rental['status'], $statusMap) 
        ? $rental['status'] 
        : 'pending';
    
    $statusClass = $statusMap[$currentStatus]['class'];
    $statusText = $statusMap[$currentStatus]['text'];

    // HTML içeriğini oluştur
    $html = "
    <div class='rental-details'>
        <div class='row'>
            <div class='col-md-6'>
                <h6 class='mb-3'>Kiralama Bilgileri</h6>
                <table class='table table-sm'>
                    <tr>
                        <th>Kiralama ID:</th>
                        <td>#{$rental['id']}</td>
                    </tr>
                    <tr>
                        <th>Durum:</th>
                        <td><span class='badge bg-{$statusClass}'>{$statusText}</span></td>
                    </tr>
                    <tr>
                        <th>Başlangıç:</th>
                        <td>" . date('d.m.Y', strtotime($rental['start_date'])) . "</td>
                    </tr>
                    <tr>
                        <th>Bitiş:</th>
                        <td>" . date('d.m.Y', strtotime($rental['end_date'])) . "</td>
                    </tr>
                    <tr>
                        <th>Toplam Tutar:</th>
                        <td>₺" . number_format($rental['total_price'], 2) . "</td>
                    </tr>
                    <tr>
                        <th>Oluşturulma:</th>
                        <td>" . date('d.m.Y H:i', strtotime($rental['created_at'])) . "</td>
                    </tr>
                </table>
            </div>
            <div class='col-md-6'>
                <h6 class='mb-3'>Araç Bilgileri</h6>
                <div class='car-info'>
                    <img src='../../assets/images/cars/{$rental['car_image']}' class='img-fluid mb-2' style='max-height: 150px;'>
                    <table class='table table-sm'>
                        <tr>
                            <th>Marka:</th>
                            <td>{$rental['brand']}</td>
                        </tr>
                        <tr>
                            <th>Model:</th>
                            <td>{$rental['model']}</td>
                        </tr>
                        <tr>
                            <th>Plaka:</th>
                            <td>{$rental['plate']}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class='row mt-4'>
            <div class='col-md-6'>
                <h6 class='mb-3'>Müşteri Bilgileri</h6>
                <table class='table table-sm'>
                    <tr>
                        <th>Ad Soyad:</th>
                        <td>{$rental['user_name']}</td>
                    </tr>
                    <tr>
                        <th>E-posta:</th>
                        <td>{$rental['user_email']}</td>
                    </tr>
                    <tr>
                        <th>Telefon:</th>
                        <td>{$rental['user_phone']}</td>
                    </tr>
                </table>
            </div>
            <div class='col-md-6'>
                <h6 class='mb-3'>Ödeme Bilgileri</h6>
                <table class='table table-sm'>
                    <tr>
                        <th>Durum:</th>
                        <td>" . ($rental['payment_status'] ?? 'Beklemede') . "</td>
                    </tr>
                    <tr>
                        <th>Ödeme Yöntemi:</th>
                        <td>" . ($rental['payment_method'] ?? '-') . "</td>
                    </tr>
                    <tr>
                        <th>İşlem ID:</th>
                        <td>" . ($rental['transaction_id'] ?? '-') . "</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>";

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
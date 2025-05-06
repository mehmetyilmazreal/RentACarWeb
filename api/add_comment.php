<?php
session_start();
require_once '../config/db.php';

// Kullanıcı girişi kontrolü
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yorum yapabilmek için giriş yapmalısınız.']);
    exit;
}

// POST verilerini kontrol et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Geçersiz istek metodu.']);
    exit;
}

$car_id = $_POST['car_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? null;

// Verilerin doğruluğunu kontrol et
if (!$car_id || !$rating || !$comment) {
    http_response_code(400);
    echo json_encode(['error' => 'Tüm alanları doldurunuz.']);
    exit;
}

// Rating değerinin geçerliliğini kontrol et
if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz puan değeri.']);
    exit;
}

try {
    // Yorumu veritabanına ekle
    $stmt = $db->prepare("
        INSERT INTO car_comments (car_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $car_id,
        $_SESSION['user_id'],
        $rating,
        $comment
    ]);

    // Başarılı yanıt döndür
    echo json_encode([
        'success' => true,
        'message' => 'Yorumunuz başarıyla eklendi.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Yorum eklenirken bir hata oluştu.',
        'details' => $e->getMessage()
    ]);
} 
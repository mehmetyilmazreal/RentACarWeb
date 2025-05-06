<?php
session_start();
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Kullanıcı ID\'si gerekli.'
    ]);
    exit;
}

$user_id = (int)$data['user_id'];

try {
    // Kullanıcıyı aktifleştir
    $db->query("
        UPDATE users 
        SET status = 'active', 
            updated_at = NOW()
        WHERE id = ?
    ", [$user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Kullanıcı başarıyla aktifleştirildi.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage()
    ]);
} 
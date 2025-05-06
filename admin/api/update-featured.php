<?php
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

try {
    // Yetki kontrolü
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'corporate') {
        throw new Exception('Bu işlem için yetkiniz yok.');
    }

    // JSON verisini al
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['car_id']) || !isset($data['is_featured'])) {
        throw new Exception('Geçersiz veri.');
    }

    // Araç durumunu güncelle
    $stmt = $db->prepare("UPDATE cars SET is_featured = ? WHERE id = ?");
    $stmt->execute([$data['is_featured'] ? 1 : 0, $data['car_id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Araç durumu güncellendi.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
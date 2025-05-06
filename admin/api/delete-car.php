<?php
session_start();
require_once '../../config/db.php';
require_once '../auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Car ID is required']);
    exit();
}

$car_id = $data['id'];

// Önce araç resmini sil
$sql = "SELECT image FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if ($car && $car['image'] && file_exists("../../" . $car['image'])) {
    unlink("../../" . $car['image']);
}

// Sonra araç kaydını sil
$sql = "DELETE FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Araç başarıyla silindi']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Araç silinirken bir hata oluştu']);
} 
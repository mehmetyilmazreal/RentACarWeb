<?php
session_start();
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Örnek konum güncellemesi (gerçek uygulamada GPS verisi kullanılacak)
    $locations = [
        ['lat' => 41.0082, 'lng' => 28.9784], // İstanbul
        ['lat' => 39.9334, 'lng' => 32.8597], // Ankara
        ['lat' => 38.4192, 'lng' => 27.1287], // İzmir
        ['lat' => 36.8841, 'lng' => 30.7056], // Antalya
        ['lat' => 40.1885, 'lng' => 29.0610], // Bursa
    ];
    
    // Tüm araçları getir
    $sql = "SELECT id FROM cars WHERE status != 'maintenance'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Her araç için rastgele bir konum ata
    foreach ($cars as $car) {
        $location = $locations[array_rand($locations)];
        
        // Konum tablosunda kayıt var mı kontrol et
        $sql = "SELECT id FROM car_locations WHERE car_id = :car_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':car_id', $car['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Konumu güncelle
            $sql = "UPDATE car_locations 
                    SET latitude = :latitude, longitude = :longitude, last_updated = NOW() 
                    WHERE car_id = :car_id";
        } else {
            // Yeni konum ekle
            $sql = "INSERT INTO car_locations (car_id, latitude, longitude, last_updated) 
                    VALUES (:car_id, :latitude, :longitude, NOW())";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':car_id', $car['id'], PDO::PARAM_INT);
        $stmt->bindParam(':latitude', $location['lat']);
        $stmt->bindParam(':longitude', $location['lng']);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Konumlar başarıyla güncellendi']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
} 
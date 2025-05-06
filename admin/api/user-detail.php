<?php
session_start();
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Kullanıcı ID\'si gerekli.'
    ]);
    exit;
}

$user_id = (int)$_GET['id'];

// Kullanıcı bilgilerini getir
$user = $db->query("
    SELECT u.*, 
           COUNT(r.id) as rental_count,
           SUM(r.total_price) as total_spent
    FROM users u
    LEFT JOIN rentals r ON u.id = r.user_id
    WHERE u.id = ?
    GROUP BY u.id
", [$user_id])->fetch();

if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Kullanıcı bulunamadı.'
    ]);
    exit;
}

// Son kiralamaları getir
$recent_rentals = $db->query("
    SELECT r.*, 
           c.brand, c.model, c.plate,
           CASE 
               WHEN r.status = 'active' THEN 'success'
               WHEN r.status = 'completed' THEN 'info'
               WHEN r.status = 'cancelled' THEN 'danger'
               ELSE 'secondary'
           END as status_class
    FROM rentals r
    JOIN cars c ON r.car_id = c.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
", [$user_id])->fetchAll();

// Son kiralama tarihini bul
$last_rental = $db->query("
    SELECT created_at
    FROM rentals
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 1
", [$user_id])->fetch();

$response = [
    'success' => true,
    'name' => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'],
    'type' => $user['type'],
    'created_at' => date('d.m.Y', strtotime($user['created_at'])),
    'rental_count' => $user['rental_count'],
    'total_spent' => number_format($user['total_spent'], 2),
    'last_rental_date' => $last_rental ? date('d.m.Y', strtotime($last_rental['created_at'])) : null,
    'recent_rentals' => array_map(function($rental) {
        return [
            'date' => date('d.m.Y', strtotime($rental['created_at'])),
            'car' => $rental['brand'] . ' ' . $rental['model'] . ' (' . $rental['plate'] . ')',
            'amount' => number_format($rental['total_price'], 2),
            'status' => $rental['status'] == 'active' ? 'Aktif' : 
                       ($rental['status'] == 'completed' ? 'Tamamlandı' : 'İptal Edildi'),
            'status_class' => $rental['status_class']
        ];
    }, $recent_rentals)
];

echo json_encode($response); 
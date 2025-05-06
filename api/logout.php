<?php
// Oturum başlat
session_start();

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Oturumu sonlandır
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Çıkış başarıyla yapıldı.'
]); 
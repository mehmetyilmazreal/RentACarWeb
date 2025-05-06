<?php
// Oturum başlat
session_start();

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Kullanıcı giriş yapmamışsa hata döndür
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Zaten çıkış yapmışsınız.'
    ]);
    exit;
}

try {
    // Remember me token'ını temizle
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Token'ı veritabanından sil
        query("DELETE FROM remember_tokens WHERE token = ?", [$token]);
        
        // Cookie'yi sil
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Oturum değişkenlerini temizle
    $_SESSION = array();
    
    // Oturum çerezini sil
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Oturumu sonlandır
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Başarıyla çıkış yapıldı.'
    ]);
} catch (Exception $e) {
    error_log('Çıkış yapma hatası: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.'
    ]);
} 
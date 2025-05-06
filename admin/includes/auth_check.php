<?php
// Oturum başlat (eğer başlatılmamışsa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../../config/db.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı bilgilerini veritabanından al
$stmt = query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
$user = $stmt->fetch();

// Kullanıcı bulunamadıysa veya aktif değilse oturumu sonlandır
if (!$user || $user['status'] !== 'active') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Kullanıcı tipi corporate değilse erişimi engelle
if ($user['type'] !== 'corporate') {
    header('Location: ../index.php');
    exit;
} 
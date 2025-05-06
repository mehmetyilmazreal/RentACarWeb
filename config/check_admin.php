<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/db.php';

try {
    // Admin kullanıcısını kontrol et
    $stmt = query("SELECT * FROM users WHERE email = 'admin@rentacar.com'");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "Admin kullanıcısı bulundu:<br>";
        echo "ID: " . $admin['id'] . "<br>";
        echo "Ad: " . $admin['name'] . "<br>";
        echo "E-posta: " . $admin['email'] . "<br>";
        echo "Tip: " . $admin['type'] . "<br>";
        echo "Durum: " . $admin['status'] . "<br>";
        echo "Şifre Hash: " . $admin['password'] . "<br>";
        
        // Şifre kontrolü
        $test_password = 'admin123';
        if (password_verify($test_password, $admin['password'])) {
            echo "<br>Şifre doğrulaması başarılı!";
        } else {
            echo "<br>Şifre doğrulaması başarısız!";
            
            // Yeni şifre oluştur
            $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
            echo "<br>Yeni hash: " . $new_hash;
            
            // Şifreyi güncelle
            $update = query("UPDATE users SET password = ? WHERE email = 'admin@rentacar.com'", [$new_hash]);
            if ($update->rowCount() > 0) {
                echo "<br>Şifre başarıyla güncellendi!";
            }
        }
    } else {
        echo "Admin kullanıcısı bulunamadı!";
    }
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
} 
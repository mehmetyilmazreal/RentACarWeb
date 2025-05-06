<?php
require_once '../config/db.php';

try {
    // SQL dosyasını oku
    $sql = file_get_contents('settings.sql');
    
    // SQL komutlarını çalıştır
    $db->exec($sql);
    
    echo "Settings tablosu başarıyla oluşturuldu ve varsayılan ayarlar eklendi.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?> 
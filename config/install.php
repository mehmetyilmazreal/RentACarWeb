<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'rentacar';
$username = 'root';
$password = '';

try {
    // PDO bağlantısı oluştur
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Veritabanı bağlantısı başarılı.<br>";
    
    // SQL dosyalarını oku ve çalıştır
    $sqlFiles = ['schema.sql', 'seed.sql'];
    
    foreach ($sqlFiles as $file) {
        $sql = file_get_contents(__DIR__ . '/' . $file);
        
        // Her bir SQL komutunu ayrı ayrı çalıştır
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "$file dosyası başarıyla çalıştırıldı.<br>";
    }
    
    echo "<br>Kurulum tamamlandı!<br>";
    echo "Admin kullanıcı bilgileri:<br>";
    echo "E-posta: admin@rentacar.com<br>";
    echo "Şifre: admin123<br>";
    
} catch(PDOException $e) {
    die("Hata: " . $e->getMessage());
} 
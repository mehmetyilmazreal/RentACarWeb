<?php
require_once '../config/db.php';

try {
    // Önce eski car_features tablosunu sil
    $db->exec("DROP TABLE IF EXISTS car_features");
    echo "Eski car_features tablosu silindi.<br>";

    // Araç özellikleri tablosunu yeniden oluştur
    $sql = "CREATE TABLE car_features (
        id INT AUTO_INCREMENT PRIMARY KEY,
        car_id INT NOT NULL,
        feature_name VARCHAR(100) NOT NULL,
        feature_value VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Araç özellikleri tablosu yeniden oluşturuldu.<br>";

    // Araç donanımı tablosunu oluştur
    $sql = "CREATE TABLE IF NOT EXISTS car_equipment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        car_id INT NOT NULL,
        equipment_name VARCHAR(100) NOT NULL,
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Araç donanımı tablosu oluşturuldu.<br>";

    // Cars tablosuna yeni sütunlar ekle
    $sql = "ALTER TABLE cars
            ADD COLUMN IF NOT EXISTS engine_size VARCHAR(50) AFTER fuel_type,
            ADD COLUMN IF NOT EXISTS horsepower INT AFTER engine_size,
            ADD COLUMN IF NOT EXISTS seats INT AFTER horsepower,
            ADD COLUMN IF NOT EXISTS doors INT AFTER seats,
            ADD COLUMN IF NOT EXISTS luggage_capacity VARCHAR(50) AFTER doors,
            ADD COLUMN IF NOT EXISTS air_conditioning BOOLEAN DEFAULT TRUE AFTER luggage_capacity,
            ADD COLUMN IF NOT EXISTS abs BOOLEAN DEFAULT TRUE AFTER air_conditioning,
            ADD COLUMN IF NOT EXISTS esp BOOLEAN DEFAULT TRUE AFTER abs,
            ADD COLUMN IF NOT EXISTS airbag_count INT DEFAULT 2 AFTER esp,
            ADD COLUMN IF NOT EXISTS cruise_control BOOLEAN DEFAULT FALSE AFTER airbag_count,
            ADD COLUMN IF NOT EXISTS parking_sensors BOOLEAN DEFAULT FALSE AFTER cruise_control,
            ADD COLUMN IF NOT EXISTS reverse_camera BOOLEAN DEFAULT FALSE AFTER parking_sensors,
            ADD COLUMN IF NOT EXISTS bluetooth BOOLEAN DEFAULT TRUE AFTER reverse_camera,
            ADD COLUMN IF NOT EXISTS navigation BOOLEAN DEFAULT FALSE AFTER bluetooth,
            ADD COLUMN IF NOT EXISTS sunroof BOOLEAN DEFAULT FALSE AFTER navigation,
            ADD COLUMN IF NOT EXISTS leather_seats BOOLEAN DEFAULT FALSE AFTER sunroof,
            ADD COLUMN IF NOT EXISTS is_featured BOOLEAN DEFAULT FALSE AFTER leather_seats";
    
    $db->exec($sql);
    echo "Cars tablosuna yeni sütunlar eklendi.<br>";

    // Eğer features tablosu varsa, car_features tablosuna verileri taşı
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'features'";
    $has_features_table = $db->query($sql)->fetchColumn();

    if ($has_features_table) {
        // Önce features tablosundaki verileri al
        $features = $db->query("SELECT * FROM features")->fetchAll(PDO::FETCH_ASSOC);
        
        // Her özellik için car_features tablosuna ekle
        $stmt = $db->prepare("INSERT INTO car_features (car_id, feature_name, feature_value) VALUES (?, ?, ?)");
        
        foreach ($features as $feature) {
            // Her araç için bu özelliği ekle
            $cars = $db->query("SELECT id FROM cars")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($cars as $car_id) {
                $stmt->execute([$car_id, $feature['name'], 'true']);
            }
        }
        
        // Eski features tablosunu sil
        $db->exec("DROP TABLE features");
        echo "Features tablosu car_features tablosuna taşındı ve silindi.<br>";
    }

    // Rezervasyonlar tablosunu oluştur
    $sql = "CREATE TABLE IF NOT EXISTS rentals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        car_id INT NOT NULL,
        user_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Rezervasyonlar tablosu oluşturuldu.<br>";

    // Ödemeler tablosunu oluştur
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rental_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('credit_card', 'bank_transfer') NOT NULL,
        card_number VARCHAR(16),
        card_holder VARCHAR(100),
        expiry_date VARCHAR(5),
        cvv VARCHAR(4),
        status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
        transaction_id VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Ödemeler tablosu oluşturuldu.<br>";

    echo "Veritabanı güncellemesi başarıyla tamamlandı.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
} 
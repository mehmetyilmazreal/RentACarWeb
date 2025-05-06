-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS rentacar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rentacar;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type ENUM('individual', 'corporate') NOT NULL DEFAULT 'individual',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Araçlar tablosu
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    plate VARCHAR(20) NOT NULL UNIQUE,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    status ENUM('available', 'rented', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Özellikler tablosu
CREATE TABLE IF NOT EXISTS features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Araç özellikleri tablosu
CREATE TABLE IF NOT EXISTS car_features (
    car_id INT NOT NULL,
    feature_id INT NOT NULL,
    PRIMARY KEY (car_id, feature_id),
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (feature_id) REFERENCES features(id) ON DELETE CASCADE
);

-- Kiralamalar tablosu
CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_date DATE NOT NULL,
    end_time TIME NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('credit_card', 'cash') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    status ENUM('pending', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id) REFERENCES cars(id)
);

-- Ekstra hizmetler tablosu
CREATE TABLE IF NOT EXISTS extra_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kiralama ekstra hizmetleri tablosu
CREATE TABLE IF NOT EXISTS rental_extra_services (
    rental_id INT NOT NULL,
    service_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (rental_id, service_id),
    FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES extra_services(id)
);

-- Araç konumları tablosu
CREATE TABLE IF NOT EXISTS car_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Örnek özellikler
INSERT INTO features (name) VALUES 
('Klima'),
('Otomatik Vites'),
('Bluetooth'),
('GPS'),
('Çocuk Koltuğu'),
('Sunroof'),
('Deri Koltuk'),
('Park Sensörü'),
('Geri Görüş Kamerası'),
('Yağmur Sensörü');

-- Örnek ekstra hizmetler
INSERT INTO extra_services (name, price, description) VALUES 
('Ek Sürücü', 50.00, 'İkinci sürücü ekleme hizmeti'),
('Bebek Koltuğu', 30.00, 'Bebek koltuğu kiralama hizmeti'),
('GPS Cihazı', 25.00, 'GPS navigasyon cihazı kiralama hizmeti'),
('Kasko Sigortası', 100.00, 'Tam kapsamlı kasko sigortası'),
('Yakıt Dolu Teslim', 150.00, 'Aracın yakıt dolu teslim edilmesi'); 
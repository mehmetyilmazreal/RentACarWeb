-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Varsayılan ayarları ekle
INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Rent A Car'),
('site_description', 'Araç Kiralama Sistemi'),
('contact_email', 'info@rentacar.com'),
('contact_phone', '+90 555 123 4567'),
('google_maps_api_key', ''),
('currency', 'TRY'),
('tax_rate', '18'),
('email_notifications', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value); 
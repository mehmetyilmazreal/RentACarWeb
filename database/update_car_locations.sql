-- Önce tablonun var olup olmadığını kontrol et
SET @tableExists = (SELECT COUNT(*) FROM information_schema.tables 
                   WHERE table_schema = DATABASE() 
                   AND table_name = 'car_locations');

-- Eğer tablo yoksa oluştur
SET @createTable = IF(@tableExists = 0,
    'CREATE TABLE car_locations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        car_id INT NOT NULL,
        latitude DECIMAL(10, 8) NOT NULL,
        longitude DECIMAL(11, 8) NOT NULL,
        last_updated DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    'SELECT "Table already exists"');

PREPARE stmt FROM @createTable;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Eksik sütunları kontrol et ve ekle
SET @addColumns = '';

-- latitude sütunu kontrolü
SET @hasLatitude = (SELECT COUNT(*) FROM information_schema.columns 
                   WHERE table_schema = DATABASE() 
                   AND table_name = 'car_locations' 
                   AND column_name = 'latitude');
SET @addColumns = IF(@hasLatitude = 0, 
    CONCAT(@addColumns, 'ADD COLUMN latitude DECIMAL(10, 8) NOT NULL AFTER car_id,'), 
    @addColumns);

-- longitude sütunu kontrolü
SET @hasLongitude = (SELECT COUNT(*) FROM information_schema.columns 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'car_locations' 
                    AND column_name = 'longitude');
SET @addColumns = IF(@hasLongitude = 0, 
    CONCAT(@addColumns, 'ADD COLUMN longitude DECIMAL(11, 8) NOT NULL AFTER latitude,'), 
    @addColumns);

-- last_updated sütunu kontrolü
SET @hasLastUpdated = (SELECT COUNT(*) FROM information_schema.columns 
                      WHERE table_schema = DATABASE() 
                      AND table_name = 'car_locations' 
                      AND column_name = 'last_updated');
SET @addColumns = IF(@hasLastUpdated = 0, 
    CONCAT(@addColumns, 'ADD COLUMN last_updated DATETIME NOT NULL AFTER longitude,'), 
    @addColumns);

-- created_at sütunu kontrolü
SET @hasCreatedAt = (SELECT COUNT(*) FROM information_schema.columns 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'car_locations' 
                    AND column_name = 'created_at');
SET @addColumns = IF(@hasCreatedAt = 0, 
    CONCAT(@addColumns, 'ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER last_updated,'), 
    @addColumns);

-- Eğer eklenecek sütun varsa ALTER TABLE komutunu çalıştır
SET @alterTable = IF(@addColumns != '', 
    CONCAT('ALTER TABLE car_locations ', SUBSTRING(@addColumns, 1, LENGTH(@addColumns) - 1)), 
    'SELECT "No new columns to add"');

PREPARE stmt FROM @alterTable;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Foreign key kontrolü
SET @hasForeignKey = (SELECT COUNT(*) FROM information_schema.key_column_usage 
                     WHERE table_schema = DATABASE() 
                     AND table_name = 'car_locations' 
                     AND referenced_table_name = 'cars');

SET @addForeignKey = IF(@hasForeignKey = 0,
    'ALTER TABLE car_locations ADD CONSTRAINT fk_car_locations_car_id FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE',
    'SELECT "Foreign key already exists"');

PREPARE stmt FROM @addForeignKey;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 
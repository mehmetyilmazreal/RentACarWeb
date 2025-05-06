<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Sayfa başlığını ayarla
$current_page = 'settings';

// Settings tablosunu kontrol et ve yoksa oluştur
try {
    $db->query("SELECT 1 FROM settings LIMIT 1");
} catch (PDOException $e) {
    // Tablo yoksa oluştur
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    
    // Varsayılan ayarları ekle
    $default_settings = [
        'site_title' => 'Rent A Car',
        'site_description' => 'Araç Kiralama Sistemi',
        'contact_email' => 'info@rentacar.com',
        'contact_phone' => '+90 555 123 4567',
        'google_maps_api_key' => '',
        'currency' => 'TRY',
        'tax_rate' => '18',
        'email_notifications' => '1'
    ];
    
    $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)";
    $stmt = $db->prepare($sql);
    
    foreach ($default_settings as $key => $value) {
        $stmt->execute([':key' => $key, ':value' => $value]);
    }
}

// Ayarları veritabanından çek - sadece gerekli sütunları seç
$sql = "SELECT setting_key, setting_value FROM settings";
$stmt = $db->prepare($sql);
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Her bir ayarı güncelle
        foreach ($_POST['settings'] as $key => $value) {
            $sql = "INSERT INTO settings (setting_key, setting_value) 
                    VALUES (:key, :value) 
                    ON DUPLICATE KEY UPDATE setting_value = :value";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':key' => $key,
                ':value' => $value
            ]);
        }
        
        $success_message = "Ayarlar başarıyla güncellendi.";
        
        // Ayarları yeniden yükle
        $stmt = $db->prepare("SELECT setting_key, setting_value FROM settings");
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        $error_message = "Ayarlar güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .admin-main {
            flex: 1;
            width: calc(100% - 250px);
            margin-left: 250px;
            padding: 0;
            background-color: #f8f9fa;
        }
        .admin-content {
            padding: 20px;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        .settings-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .settings-section h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-group .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 4px;
        }
        @media (max-width: 768px) {
            .admin-main {
                width: 100%;
                margin-left: 0;
            }
            .settings-section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Ayarlar</h2>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Site Ayarları -->
                    <div class="settings-section">
                        <h3>Site Ayarları</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_title">Site Başlığı</label>
                                    <input type="text" class="form-control" id="site_title" name="settings[site_title]" 
                                           value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_description">Site Açıklaması</label>
                                    <input type="text" class="form-control" id="site_description" name="settings[site_description]" 
                                           value="<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">İletişim E-posta</label>
                                    <input type="email" class="form-control" id="contact_email" name="settings[contact_email]" 
                                           value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_phone">İletişim Telefon</label>
                                    <input type="text" class="form-control" id="contact_phone" name="settings[contact_phone]" 
                                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- API Ayarları -->
                    <div class="settings-section">
                        <h3>API Ayarları</h3>
                        <div class="form-group">
                            <label for="google_maps_api_key">Google Maps API Anahtarı</label>
                            <input type="text" class="form-control" id="google_maps_api_key" name="settings[google_maps_api_key]" 
                                   value="<?php echo htmlspecialchars($settings['google_maps_api_key'] ?? ''); ?>">
                            <div class="form-text">Google Maps özelliklerini kullanabilmek için API anahtarı gereklidir.</div>
                        </div>
                    </div>
                    
                    <!-- Ödeme Ayarları -->
                    <div class="settings-section">
                        <h3>Ödeme Ayarları</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Para Birimi</label>
                                    <select class="form-control" id="currency" name="settings[currency]">
                                        <option value="TRY" <?php echo ($settings['currency'] ?? '') === 'TRY' ? 'selected' : ''; ?>>Türk Lirası (₺)</option>
                                        <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>Amerikan Doları ($)</option>
                                        <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_rate">Vergi Oranı (%)</label>
                                    <input type="number" class="form-control" id="tax_rate" name="settings[tax_rate]" 
                                           value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '18'); ?>" min="0" max="100" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bildirim Ayarları -->
                    <div class="settings-section">
                        <h3>Bildirim Ayarları</h3>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="email_notifications" name="settings[email_notifications]" 
                                       value="1" <?php echo ($settings['email_notifications'] ?? '') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_notifications">E-posta Bildirimleri</label>
                            </div>
                            <div class="form-text">Yeni kiralama talepleri ve önemli güncellemeler için e-posta bildirimleri gönder.</div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save'></i> Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
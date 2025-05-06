<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once '../config/db.php';

// JSON yanıt için header ayarla
header('Content-Type: application/json');

// Gelen ham veriyi al
$raw_input = file_get_contents('php://input');

// Debug için gelen veriyi logla
error_log('Gelen ham veri: ' . $raw_input);

// JSON verisini çöz
$data = json_decode($raw_input, true);

// JSON çözme hatasını kontrol et
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz JSON verisi.',
        'debug' => [
            'raw_input' => $raw_input,
            'json_error' => json_last_error_msg(),
            'json_error_code' => json_last_error()
        ]
    ]);
    exit;
}

// Gelen veriyi kontrol et
if ($data === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz veri formatı.',
        'debug' => [
            'raw_input' => $raw_input,
            'parsed_data' => $data
        ]
    ]);
    exit;
}

// Debug için çözülen veriyi logla
error_log('Çözülen veri: ' . print_r($data, true));

// Name alanı varlık kontrolü
if (!isset($data['name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Ad Soyad alanı bulunamadı.',
        'debug' => [
            'received_data' => $data,
            'missing_fields' => array_diff(['name', 'email', 'phone', 'password', 'user_type', 'tc_no'], array_keys($data))
        ]
    ]);
    exit;
}

// Name alanı boşluk kontrolü
$name = trim($data['name']);
if (empty($name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Ad Soyad alanı boş bırakılamaz. Lütfen adınızı ve soyadınızı giriniz.',
        'debug' => [
            'original_value' => $data['name'],
            'trimmed_value' => $name,
            'length' => strlen($name)
        ]
    ]);
    exit;
}

// Name alanı uzunluk kontrolü
if (strlen($name) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Ad Soyad en az 3 karakter olmalıdır.',
        'debug' => [
            'value' => $name,
            'length' => strlen($name)
        ]
    ]);
    exit;
}

// Name alanı format kontrolü
if (!preg_match('/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/', $name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Ad Soyad sadece harflerden oluşmalıdır.',
        'debug' => [
            'value' => $name,
            'invalid_chars' => preg_replace('/[a-zA-ZğüşıöçĞÜŞİÖÇ\s]/', '', $name)
        ]
    ]);
    exit;
}

// Diğer gerekli alanları kontrol et
$required_fields = ['email', 'phone', 'password', 'user_type', 'tc_no'];
$missing_fields = [];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Lütfen tüm zorunlu alanları doldurunuz.',
        'debug' => [
            'missing_fields' => $missing_fields,
            'received_data' => $data
        ]
    ]);
    exit;
}

// E-posta formatını kontrol et
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz e-posta adresi.',
        'debug' => [
            'email' => $data['email']
        ]
    ]);
    exit;
}

// Kullanıcı tipini kontrol et
if (!in_array($data['user_type'], ['individual', 'corporate'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz kullanıcı tipi.',
        'debug' => [
            'user_type' => $data['user_type']
        ]
    ]);
    exit;
}

// TC Kimlik numarası kontrolü
function validateTCKN($tckn) {
    // 11 haneli olmalı
    if (strlen($tckn) != 11) {
        return false;
    }
    
    // Sadece rakam olmalı
    if (!ctype_digit($tckn)) {
        return false;
    }
    
    // İlk hane 0 olamaz
    if ($tckn[0] == '0') {
        return false;
    }
    
    return true;
}

// TC Kimlik numarasını kontrol et
if (!validateTCKN($data['tc_no'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz TC kimlik numarası. Lütfen 11 haneli, geçerli bir TC kimlik numarası giriniz.',
        'debug' => [
            'tc_no' => $data['tc_no'],
            'length' => strlen($data['tc_no']),
            'is_numeric' => ctype_digit($data['tc_no']),
            'first_digit' => $data['tc_no'][0] ?? null
        ]
    ]);
    exit;
}

try {
    // TC Kimlik numarası kullanımda mı kontrol et
    $existing_tc = query("SELECT id FROM users WHERE tc_no = ?", [$data['tc_no']])->fetch();
    if ($existing_tc) {
        echo json_encode([
            'success' => false,
            'message' => 'Bu TC kimlik numarası zaten kullanımda.'
        ]);
        exit;
    }

    // E-posta adresi kullanımda mı kontrol et
    $existing_user = query("SELECT id FROM users WHERE email = ?", [$data['email']])->fetch();
    if ($existing_user) {
        echo json_encode([
            'success' => false,
            'message' => 'Bu e-posta adresi zaten kullanımda.'
        ]);
        exit;
    }

    // Şifreyi hashle
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Kullanıcıyı kaydet
    query("
        INSERT INTO users (name, email, phone, password, type, tc_no, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
    ", [
        $name,
        $data['email'],
        $data['phone'],
        $hashed_password,
        $data['user_type'],
        $data['tc_no']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Kayıt başarıyla tamamlandı.'
    ]);

} catch (Exception $e) {
    error_log('Kayıt hatası: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Bir hata oluştu: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} 
<?php
session_start();
require_once 'config/db.php';

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Geçici rezervasyon bilgilerini kontrol et
if (!isset($_SESSION['temp_rental'])) {
    header('Location: cars.php');
    exit;
}

$rental = $_SESSION['temp_rental'];

// Araç bilgilerini getir
$stmt = $db->prepare("SELECT brand, model, plate, image FROM cars WHERE id = ?");
$stmt->execute([$rental['car_id']]);
$car = $stmt->fetch();

if (!$car) {
    unset($_SESSION['temp_rental']);
    header('Location: cars.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme | OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="pt-5">
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h1 class="mb-4">Ödeme</h1>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Rezervasyon Detayları</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Araç:</strong> <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></p>
                                        <p><strong>Plaka:</strong> <?php echo htmlspecialchars($car['plate']); ?></p>
                                        <p><strong>Alış Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($rental['start_date'] . ' ' . $rental['start_time'])); ?></p>
                                        <p><strong>Dönüş Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($rental['end_date'] . ' ' . $rental['end_time'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Toplam Tutar:</strong> <?php echo number_format($rental['total_price'], 2); ?> ₺</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="paymentForm" class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Ödeme Bilgileri</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ödeme Yöntemi</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                        <label class="form-check-label" for="creditCard">
                                            <i class="fas fa-credit-card me-2"></i>Kredi Kartı
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer">
                                        <label class="form-check-label" for="bankTransfer">
                                            <i class="fas fa-university me-2"></i>Banka Havalesi
                                        </label>
                                    </div>
                                </div>

                                <div id="creditCardFields">
                                    <div class="mb-3">
                                        <label for="cardHolder" class="form-label">Kart Üzerindeki İsim</label>
                                        <input type="text" class="form-control" id="cardHolder" name="card_holder" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Kart Numarası</label>
                                        <input type="text" class="form-control" id="cardNumber" name="card_number" maxlength="16" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiryDate" class="form-label">Son Kullanma Tarihi</label>
                                            <input type="text" class="form-control" id="expiryDate" name="expiry_date" placeholder="AA/YY" maxlength="5" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" name="cvv" maxlength="4" required>
                                        </div>
                                    </div>
                                </div>

                                <div id="bankTransferFields" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6>Banka Hesap Bilgileri</h6>
                                        <p class="mb-1"><strong>Banka:</strong> Örnek Bank</p>
                                        <p class="mb-1"><strong>IBAN:</strong> TR00 0000 0000 0000 0000 0000 00</p>
                                        <p class="mb-1"><strong>Hesap Sahibi:</strong> OOF Araç Kiralama</p>
                                        <p class="mb-0"><strong>Tutar:</strong> <?php echo number_format($rental['total_price'], 2); ?> ₺</p>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lock me-2"></i>Ödemeyi Tamamla
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Özet</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kiralama Ücreti:</span>
                                    <span><?php echo number_format($rental['total_price'], 2); ?> ₺</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Toplam:</strong>
                                    <strong><?php echo number_format($rental['total_price'], 2); ?> ₺</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ödeme yöntemi değiştiğinde
            $('input[name="payment_method"]').change(function() {
                if ($(this).val() === 'credit_card') {
                    $('#creditCardFields').show();
                    $('#bankTransferFields').hide();
                } else {
                    $('#creditCardFields').hide();
                    $('#bankTransferFields').show();
                }
            });

            // Kart numarası formatı
            $('#cardNumber').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Son kullanma tarihi formatı
            $('#expiryDate').on('input', function() {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                this.value = value;
            });

            // CVV formatı
            $('#cvv').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Form gönderimi
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('api/process-payment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = 'my-rentals.php';
                    } else {
                        alert(data.message || 'Bir hata oluştu!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu!');
                });
            });
        });
    </script>
</body>
</html> 
<?php
session_start();
// Veritabanı bağlantısını dahil et
require_once 'config/db.php';

// Öne çıkan araçları getir
$featured_cars = $db->query("
    SELECT c.*, 
           GROUP_CONCAT(DISTINCT cf.feature_name) as additional_features
    FROM cars c
    LEFT JOIN car_features cf ON c.id = cf.car_id
    WHERE c.is_featured = 1 AND c.status = 'available'
    GROUP BY c.id
    LIMIT 6
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOF Araç Kiralama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        df-messenger {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1>Güvenilir ve Konforlu Araç Kiralama</h1>
                        <p class="lead">İhtiyacınıza uygun araçları uygun fiyatlarla kiralayın.</p>
                        <a href="cars.php" class="btn btn-custom btn-lg">Araçları İncele</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Cars Section -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Öne Çıkan Araçlar</h2>
                <div class="row">
                    <?php foreach ($featured_cars as $car): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="car-image">
                                    <img src="assets/images/cars/<?php echo htmlspecialchars($car['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
                                         class="img-fluid">
                                    <div class="car-overlay">
                                        <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn btn-light">
                                            Detayları Gör <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="car-content">
                                        <div class="car-header">
                                            <h3 class="car-title"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                                            <div class="car-price">
                                                <?php echo number_format($car['price'], 2); ?> ₺<span>/ gün</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="cars.php" class="btn btn-outline-custom">Tüm Araçları Gör</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <i class="fas fa-car fa-3x mb-3"></i>
                            <h3>Geniş Araç Filosu</h3>
                            <p>Her ihtiyaca uygun araç seçenekleri</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <i class="fas fa-shield-alt fa-3x mb-3"></i>
                            <h3>Güvenli Kiralama</h3>
                            <p>Sigortalı ve güvenli araç kiralama hizmeti</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <i class="fas fa-headset fa-3x mb-3"></i>
                            <h3>7/24 Destek</h3>
                            <p>Kesintisiz müşteri hizmetleri</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
        intent="WELCOME"
        chat-title="OOF ChatBot"
        agent-id="ad80c762-7514-4ec3-a3c9-c5657f610d33"
        language-code="tr"
    ></df-messenger>
</body>
</html> 
<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

// Google Maps API anahtarı
$google_maps_api_key = 'AIzaSyA8fA2b0yFXd03BhzOUwqU8j74dLsLYSAg'; // Buraya Google Maps API anahtarınızı ekleyin

// Araçları ve konumlarını getir
$sql = "SELECT c.*, cl.latitude, cl.longitude, cl.last_updated 
        FROM cars c 
        LEFT JOIN car_locations cl ON c.id = cl.car_id 
        WHERE c.status != 'maintenance'";
$stmt = $db->prepare($sql);
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sayfa başlığını ayarla
$current_page = 'locations';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Konumları - Admin Panel</title>
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
        #map {
            height: calc(100vh - 250px);
            min-height: 500px;
            width: 100%;
            border-radius: 8px;
        }
        .car-list {
            height: calc(100vh - 250px);
            min-height: 500px;
            overflow-y: auto;
        }
        .car-item {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .car-item:hover {
            background-color: var(--light-color);
        }
        .car-item.active {
            background-color: var(--accent-color);
            color: white;
        }
        .car-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-available {
            background-color: #28a745;
        }
        .status-rented {
            background-color: #ffc107;
        }
        .card {
            height: 100%;
            margin-bottom: 0;
        }
        .card-body {
            padding: 15px;
        }
        .row {
            margin: 0;
            height: calc(100vh - 200px);
        }
        .col-md-4, .col-md-8 {
            padding: 0 10px;
        }
        @media (max-width: 768px) {
            .admin-main {
                width: 100%;
                margin-left: 0;
            }
            .row {
                height: auto;
            }
            #map, .car-list {
                height: 400px;
            }
            .col-md-4, .col-md-8 {
                margin-bottom: 20px;
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
                    <h2>Araç Konumları</h2>
                    <button class="btn btn-primary" onclick="refreshLocations()">
                        <i class='bx bx-refresh'></i> Konumları Güncelle
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Araç Listesi</h5>
                                <div class="car-list">
                                    <?php foreach ($cars as $car): ?>
                                        <div class="car-item" 
                                             data-car-id="<?php echo $car['id']; ?>"
                                             data-lat="<?php echo $car['latitude'] ?? ''; ?>"
                                             data-lng="<?php echo $car['longitude'] ?? ''; ?>"
                                             onclick="showCarOnMap(this)">
                                            <div class="d-flex align-items-center">
                                                <span class="car-status status-<?php echo $car['status']; ?>"></span>
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h6>
                                                    <small class="text-muted">
                                                        Plaka: <?php echo htmlspecialchars($car['plate']); ?><br>
                                                        Son Güncelleme: <?php echo $car['last_updated'] ? date('d.m.Y H:i', strtotime($car['last_updated'])) : 'Bilgi yok'; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>"></script>
    <script>
        let map;
        let markers = {};
        
        function initMap() {
            // Türkiye'nin merkezi
            const turkey = { lat: 39.9334, lng: 32.8597 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 6,
                center: turkey,
                styles: [
                    {
                        "featureType": "all",
                        "elementType": "geometry",
                        "stylers": [{"color": "#f5f5f5"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{"color": "#c9c9c9"}]
                    }
                ]
            });
        }
        
        function showCarOnMap(element) {
            const carId = element.dataset.carId;
            const lat = parseFloat(element.dataset.lat);
            const lng = parseFloat(element.dataset.lng);
            
            // Aktif araç stilini güncelle
            document.querySelectorAll('.car-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
            
            // Eğer konum bilgisi varsa
            if (lat && lng) {
                // Eğer marker zaten varsa, onu güncelle
                if (markers[carId]) {
                    markers[carId].setPosition({ lat, lng });
                } else {
                    // Yeni marker oluştur
                    const marker = new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                        title: element.querySelector('h6').textContent
                    });
                    markers[carId] = marker;
                }
                
                // Haritayı bu konuma odakla
                map.setCenter({ lat, lng });
                map.setZoom(15);
            }
        }
        
        function refreshLocations() {
            fetch('api/update-car-locations.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Konumlar güncellenirken bir hata oluştu.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu.');
                });
        }
        
        // Sayfa yüklendiğinde haritayı başlat
        window.onload = initMap;
    </script>
</body>
</html> 
// Admin Panel JavaScript

// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});

// Map Initialization
function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 41.0082, lng: 28.9784 }, // İstanbul coordinates
        zoom: 12,
        styles: [
            {
                "featureType": "all",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{"color": "#e9e9e9"}, {"lightness": 17}]
            }
        ]
    });

    // Araç konumlarını haritada göster
    fetch('api/car-locations.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(car => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(car.latitude), lng: parseFloat(car.longitude) },
                    map: map,
                    title: car.model,
                    icon: {
                        url: '../assets/images/car-marker.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="car-info-window">
                            <h5>${car.model}</h5>
                            <p>Plaka: ${car.plate}</p>
                            <p>Durum: ${car.status}</p>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
            });
        });
}

// Kiralama İptal
function cancelRental(rentalId) {
    if (confirm('Bu kiralamayı iptal etmek istediğinizden emin misiniz?')) {
        fetch('api/cancel-rental.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ rental_id: rentalId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Kiralama başarıyla iptal edildi.');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message || 'Bir hata oluştu.');
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    }
}

// Araç Ekleme
function addCar(formData) {
    fetch('api/add-car.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Araç başarıyla eklendi.');
            setTimeout(() => {
                window.location.href = 'cars.php';
            }, 1500);
        } else {
            showAlert('danger', data.message || 'Bir hata oluştu.');
        }
    })
    .catch(error => {
        showAlert('danger', 'Bir hata oluştu.');
    });
}

// Araç Düzenleme
function editCar(carId, formData) {
    fetch(`api/edit-car.php?id=${carId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Araç başarıyla güncellendi.');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message || 'Bir hata oluştu.');
        }
    })
    .catch(error => {
        showAlert('danger', 'Bir hata oluştu.');
    });
}

// Araç Silme
function deleteCar(carId) {
    if (confirm('Bu aracı silmek istediğinizden emin misiniz?')) {
        fetch(`api/delete-car.php?id=${carId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Araç başarıyla silindi.');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message || 'Bir hata oluştu.');
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    }
}

// Kullanıcı İşlemleri
function deleteUser(userId) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
        fetch(`api/delete-user.php?id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Kullanıcı başarıyla silindi.');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message || 'Bir hata oluştu.');
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    }
}

// Alert Gösterme
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.admin-content');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Form Validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Image Preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

// Date Range Picker
if (document.querySelector('.date-range-picker')) {
    flatpickr('.date-range-picker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        minDate: 'today'
    });
}

// Select2 Initialization
if (document.querySelector('.select2')) {
    $('.select2').select2({
        theme: 'bootstrap-5'
    });
}

// DataTables Initialization
if (document.querySelector('.datatable')) {
    $('.datatable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json'
        },
        responsive: true
    });
}

// Chart.js Initialization
if (document.getElementById('revenueChart')) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
            datasets: [{
                label: 'Aylık Gelir',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#2563eb',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
} 
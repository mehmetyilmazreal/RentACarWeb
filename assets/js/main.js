// Fiyat aralığı slider'ı için
document.addEventListener('DOMContentLoaded', function() {
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    
    if (priceRange && priceValue) {
        priceRange.addEventListener('input', function() {
            priceValue.textContent = this.value + '₺';
        });
    }

    // Görünüm değiştirme butonları
    const viewButtons = document.querySelectorAll('.view-options .btn');
    const carGrid = document.querySelector('.row.g-4');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            if (this.querySelector('.fa-list')) {
                carGrid.classList.add('list-view');
            } else {
                carGrid.classList.remove('list-view');
            }
        });
    });

    // Filtre formunu sıfırlama
    const resetFilters = document.querySelector('.btn-outline-secondary');
    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            const form = this.closest('form');
            form.reset();
            priceValue.textContent = '500₺';
            priceRange.value = 500;
        });
    }
});

// Araç Detay Sayfası
if (document.querySelector('.car-gallery')) {
    // Swiper Galeri
    const gallerySwiper = new Swiper('.car-gallery', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    // Tarih Seçici
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const startTime = document.getElementById('start-time');
    const endTime = document.getElementById('end-time');

    if (startDate && endDate) {
        // Minimum tarih bugün
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        startDate.min = today.toISOString().split('T')[0];
        endDate.min = tomorrow.toISOString().split('T')[0];

        // Başlangıç tarihi değiştiğinde
        startDate.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const nextDay = new Date(selectedDate);
            nextDay.setDate(nextDay.getDate() + 1);
            endDate.min = nextDay.toISOString().split('T')[0];
            
            if (endDate.value && new Date(endDate.value) <= selectedDate) {
                endDate.value = nextDay.toISOString().split('T')[0];
            }
            calculateTotal();
        });

        // Bitiş tarihi değiştiğinde
        endDate.addEventListener('change', calculateTotal);
    }

    // Saat seçimi değiştiğinde
    if (startTime && endTime) {
        startTime.addEventListener('change', calculateTotal);
        endTime.addEventListener('change', calculateTotal);
    }

    // Ekstra hizmetler değiştiğinde
    const extraServices = document.querySelectorAll('.extra-service');
    extraServices.forEach(service => {
        service.addEventListener('change', calculateTotal);
    });

    // Toplam tutarı hesapla
    function calculateTotal() {
        const dailyPrice = parseFloat(document.getElementById('daily-price').textContent);
        const startDate = new Date(document.getElementById('start-date').value);
        const endDate = new Date(document.getElementById('end-date').value);
        
        if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            let total = dailyPrice * days;

            // Ekstra hizmetleri ekle
            extraServices.forEach(service => {
                if (service.checked) {
                    total += parseFloat(service.dataset.price) * days;
                }
            });

            // Toplam tutarı güncelle
            document.getElementById('total-price').textContent = total.toFixed(2);
            document.getElementById('total-days').textContent = days;
        }
    }
}

// 360 Derece Görüntüleme
if (document.getElementById('panorama')) {
    pannellum.viewer('panorama', {
        type: 'equirectangular',
        panorama: 'assets/images/360/mercedes-c200-360.jpg',
        autoLoad: true,
        autoRotate: -2,
        compass: true,
        northOffset: 0,
        showFullscreenCtrl: true,
        showControls: true,
        mouseZoom: true,
        minHfov: 50,
        maxHfov: 120,
        hfov: 100,
        hotSpots: [
            {
                pitch: -10,
                yaw: 0,
                type: "info",
                text: "Mercedes C200 Ön Görünüm",
                URL: "#"
            },
            {
                pitch: -10,
                yaw: 90,
                type: "info",
                text: "Mercedes C200 Yan Görünüm",
                URL: "#"
            },
            {
                pitch: -10,
                yaw: 180,
                type: "info",
                text: "Mercedes C200 Arka Görünüm",
                URL: "#"
            },
            {
                pitch: -10,
                yaw: 270,
                type: "info",
                text: "Mercedes C200 Diğer Yan Görünüm",
                URL: "#"
            }
        ]
    });
}

// Kayıt Formu
if (document.getElementById('registerForm')) {
    const registerForm = document.getElementById('registerForm');
    const individualFields = document.getElementById('individualFields');
    const corporateFields = document.getElementById('corporateFields');
    const registerTypeInputs = document.querySelectorAll('input[name="registerType"]');

    // Kayıt türü değiştiğinde
    registerTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'individual') {
                individualFields.style.display = 'block';
                corporateFields.style.display = 'none';
                // Kurumsal alanları opsiyonel yap
                corporateFields.querySelectorAll('input, textarea').forEach(field => {
                    field.required = false;
                });
                // Bireysel alanları zorunlu yap
                individualFields.querySelectorAll('input').forEach(field => {
                    field.required = true;
                });
            } else {
                individualFields.style.display = 'none';
                corporateFields.style.display = 'block';
                // Bireysel alanları opsiyonel yap
                individualFields.querySelectorAll('input').forEach(field => {
                    field.required = false;
                });
                // Kurumsal alanları zorunlu yap
                corporateFields.querySelectorAll('input, textarea').forEach(field => {
                    field.required = true;
                });
            }
        });
    });

    // Form gönderildiğinde
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        // Şifre kontrolü
        const password = this.querySelector('input[name="password"]') || this.querySelector('input[name="corporatePassword"]');
        const passwordConfirm = this.querySelector('input[name="passwordConfirm"]') || this.querySelector('input[name="corporatePasswordConfirm"]');

        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Şifreler eşleşmiyor');
            this.classList.add('was-validated');
            return;
        }

        // Form verilerini topla
        const formData = new FormData(this);
        const registerType = document.querySelector('input[name="registerType"]:checked').value;
        
        // Ad ve soyadı birleştir
        if (registerType === 'individual') {
            const firstName = formData.get('firstName');
            const lastName = formData.get('lastName');
            formData.set('name', `${firstName} ${lastName}`);
        } else {
            const authorizedName = formData.get('authorizedName');
            const authorizedSurname = formData.get('authorizedSurname');
            formData.set('name', `${authorizedName} ${authorizedSurname}`);
        }

        // Kullanıcı tipini ayarla
        formData.set('user_type', registerType);

        // TC No'yu ayarla
        if (registerType === 'individual') {
            formData.set('tc_no', formData.get('tcNo'));
        }

        // API'ye gönder
        submitRegistration(formData);
    });

    // TC Kimlik No doğrulama
    function validateTCKN(value) {
        if (value.length !== 11) return false;
        
        let digits = value.split('').map(Number);
        
        // İlk hane 0 olamaz
        if (digits[0] === 0) return false;
        
        // 1, 3, 5, 7, 9. hanelerin toplamının 7 katından, 2, 4, 6, 8. hanelerin toplamı çıkartıldığında,
        // elde edilen sonucun 10'a bölümünden kalan, 10. haneyi verir
        let odd = digits[0] + digits[2] + digits[4] + digits[6] + digits[8];
        let even = digits[1] + digits[3] + digits[5] + digits[7];
        let digit10 = (odd * 7 - even) % 10;
        
        if (digit10 !== digits[9]) return false;
        
        // İlk 10 hanenin toplamının 10'a bölümünden kalan, 11. haneyi verir
        let sum = digits.slice(0, 10).reduce((a, b) => a + b, 0);
        let digit11 = sum % 10;
        
        return digit11 === digits[10];
    }

    // Kayıt işlemini gönder
    async function submitRegistration(formData) {
        try {
            // FormData'yı JSON'a dönüştür
            const jsonData = {};
            formData.forEach((value, key) => {
                // Boş değerleri kontrol et
                if (value !== null && value !== undefined && value !== '') {
                    jsonData[key] = value;
                }
            });

            // Debug için konsola yazdır
            console.log('Gönderilen veri:', jsonData);

            const response = await fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonData)
            });

            // Debug için yanıtı kontrol et
            const responseText = await response.text();
            console.log('Sunucu yanıtı:', responseText);

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('JSON parse hatası:', e);
                throw new Error('Sunucu yanıtı geçersiz JSON formatında');
            }

            if (data.success) {
                // Başarılı kayıt
                showAlert('success', 'Kayıt işleminiz başarıyla tamamlandı. Giriş yapabilirsiniz.');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                // Hata durumu
                showAlert('danger', data.message || 'Kayıt işlemi sırasında bir hata oluştu.');
                console.error('Sunucu hatası:', data);
            }
        } catch (error) {
            console.error('İşlem hatası:', error);
            showAlert('danger', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
        }
    }

    // Alert gösterme
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        registerForm.insertAdjacentElement('beforebegin', alertDiv);
    }
}

// Giriş Formu
if (document.getElementById('loginForm')) {
    const loginForm = document.getElementById('loginForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.querySelector('input[name="password"]');

    // Şifre göster/gizle
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Form gönderildiğinde
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        const formData = new FormData(this);
        const rememberMe = document.getElementById('rememberMe').checked;
        formData.append('rememberMe', rememberMe);

        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Başarılı giriş
                showAlert('success', 'Giriş başarılı! Yönlendiriliyorsunuz...');
                
                // Beni hatırla seçeneği işaretliyse
                if (rememberMe) {
                    localStorage.setItem('rememberMe', 'true');
                }

                // Yönlendirme
                setTimeout(() => {
                    window.location.href = data.redirect || 'index.php';
                }, 1000);
            } else {
                // Hata durumu
                showAlert('danger', data.message || 'Giriş yapılamadı. Lütfen bilgilerinizi kontrol ediniz.');
            }
        } catch (error) {
            showAlert('danger', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
        }
    });

    // Sosyal medya girişi
    document.querySelector('.btn-outline-dark').addEventListener('click', function() {
        // Google girişi
        window.location.href = 'api/auth/google.php';
    });

    document.querySelector('.btn-outline-primary').addEventListener('click', function() {
        // Facebook girişi
        window.location.href = 'api/auth/facebook.php';
    });

    // Alert gösterme
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        loginForm.insertAdjacentElement('beforebegin', alertDiv);
    }

    // Beni hatırla kontrolü
    if (localStorage.getItem('rememberMe') === 'true') {
        document.getElementById('rememberMe').checked = true;
    }
}

// Şifremi Unuttum Formu
if (document.getElementById('forgotPasswordForm')) {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    let isSubmitting = false;

    forgotPasswordForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!this.checkValidity() || isSubmitting) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        isSubmitting = true;
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        // Buton durumunu güncelle
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Gönderiliyor...
        `;

        try {
            const formData = new FormData(this);
            const response = await fetch('api/forgot-password.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Başarılı gönderim
                showAlert('success', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi. Lütfen e-postanızı kontrol edin.');
                
                // Formu sıfırla
                this.reset();
                this.classList.remove('was-validated');
            } else {
                // Hata durumu
                showAlert('danger', data.message || 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
            }
        } catch (error) {
            showAlert('danger', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
        } finally {
            // Buton durumunu eski haline getir
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            isSubmitting = false;
        }
    });

    // Alert gösterme
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        forgotPasswordForm.insertAdjacentElement('beforebegin', alertDiv);

        // 5 saniye sonra alert'i kaldır
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Kiralama Formu
if (document.getElementById('bookingForm')) {
    const bookingForm = document.getElementById('bookingForm');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const startTime = document.getElementById('start-time');
    const endTime = document.getElementById('end-time');
    const extraServices = document.querySelectorAll('.extra-service');
    const dailyPriceElement = document.getElementById('daily-price');
    const totalDaysElement = document.getElementById('total-days');
    const totalPriceElement = document.getElementById('total-price');

    let isSubmitting = false;

    // Minimum tarih kontrolü
    const today = new Date();
    const minDate = today.toISOString().split('T')[0];
    startDate.min = minDate;
    endDate.min = minDate;

    // Tarih değişikliği kontrolü
    startDate.addEventListener('change', updateEndDateMin);
    endDate.addEventListener('change', calculateTotal);
    startTime.addEventListener('change', calculateTotal);
    endTime.addEventListener('change', calculateTotal);

    // Ekstra hizmet değişikliği kontrolü
    extraServices.forEach(service => {
        service.addEventListener('change', calculateTotal);
    });

    function updateEndDateMin() {
        endDate.min = startDate.value;
        if (endDate.value && endDate.value < startDate.value) {
            endDate.value = startDate.value;
        }
        calculateTotal();
    }

    function calculateTotal() {
        if (!startDate.value || !endDate.value) {
            totalDaysElement.textContent = '0';
            totalPriceElement.textContent = '0₺';
            return;
        }

        const start = new Date(startDate.value + 'T' + (startTime.value || '00:00'));
        const end = new Date(endDate.value + 'T' + (endTime.value || '00:00'));
        
        if (end < start) {
            totalDaysElement.textContent = '0';
            totalPriceElement.textContent = '0₺';
            return;
        }

        // Gün farkını hesapla
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Günlük ücreti al
        const dailyPrice = parseInt(dailyPriceElement.textContent);
        
        // Ekstra hizmetleri hesapla
        let extraTotal = 0;
        extraServices.forEach(service => {
            if (service.checked) {
                extraTotal += parseInt(service.dataset.price) * diffDays;
            }
        });

        // Toplam tutarı hesapla
        const total = (dailyPrice * diffDays) + extraTotal;

        // Değerleri güncelle
        totalDaysElement.textContent = diffDays;
        totalPriceElement.textContent = total + '₺';
    }

    // Form gönderimi
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!this.checkValidity() || isSubmitting) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        isSubmitting = true;
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        // Buton durumunu güncelle
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            İşleniyor...
        `;

        try {
            const formData = new FormData(this);
            formData.append('start_date', startDate.value);
            formData.append('end_date', endDate.value);
            formData.append('start_time', startTime.value);
            formData.append('end_time', endTime.value);
            formData.append('total_price', totalPriceElement.textContent);
            formData.append('total_days', totalDaysElement.textContent);

            const response = await fetch('api/booking.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'Kiralama işlemi başarıyla tamamlandı!');
                this.reset();
                this.classList.remove('was-validated');
                calculateTotal();
            } else {
                showAlert('danger', data.message || 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
            }
        } catch (error) {
            showAlert('danger', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
        } finally {
            // Buton durumunu eski haline getir
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            isSubmitting = false;
        }
    });

    // Alert gösterme
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        bookingForm.insertAdjacentElement('beforebegin', alertDiv);

        // 5 saniye sonra alert'i kaldır
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// İletişim Formu
if (document.getElementById('contactForm')) {
    const contactForm = document.getElementById('contactForm');
    let isSubmitting = false;

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!this.checkValidity() || isSubmitting) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        isSubmitting = true;
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        // Buton durumunu güncelle
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Gönderiliyor...
        `;

        try {
            const formData = new FormData(this);
            const response = await fetch('api/contact.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Başarılı gönderim
                showAlert('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
                
                // Formu sıfırla
                this.reset();
                this.classList.remove('was-validated');
            } else {
                // Hata durumu
                showAlert('danger', data.message || 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
            }
        } catch (error) {
            showAlert('danger', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
        } finally {
            // Buton durumunu eski haline getir
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            isSubmitting = false;
        }
    });

    // Alert gösterme
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        contactForm.insertAdjacentElement('beforebegin', alertDiv);

        // 5 saniye sonra alert'i kaldır
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
} 
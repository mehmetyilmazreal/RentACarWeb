-- Admin kullanıcısı oluştur (şifre: admin123)
INSERT INTO users (name, email, phone, password, type, status) VALUES 
('Admin', 'admin@rentacar.com', '5551234567', '$2y$10$YourNewHashHere', 'corporate', 'active'); 
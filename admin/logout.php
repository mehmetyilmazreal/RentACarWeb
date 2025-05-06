<?php
// Oturum başlat
session_start();

// Tüm oturum verilerini temizle
session_destroy();

// Login sayfasına yönlendir
header('Location: login.php');
exit; 
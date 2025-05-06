<?php
// Sayfa başlığını belirle
$page_title = 'Admin Panel';
switch (basename($_SERVER['PHP_SELF'])) {
    case 'index.php':
        $page_title = 'Dashboard';
        break;
    case 'rentals.php':
        $page_title = 'Kiralamalar';
        break;
    case 'cars.php':
        $page_title = 'Araçlar';
        break;
    case 'users.php':
        $page_title = 'Kullanıcılar';
        break;
    case 'locations.php':
        $page_title = 'Araç Konumları';
        break;
    case 'settings.php':
        $page_title = 'Ayarlar';
        break;
}
?>
<header class="admin-header">
    <div class="header-left">
        <button class="menu-toggle">
            <i class='bx bx-menu'></i>
        </button>
        <h2><?php echo $page_title; ?></h2>
    </div>
    <div class="header-right">
        <div class="user-info">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">
                <i class='bx bx-log-out'></i> Çıkış
            </a>
        </div>
    </div>
</header> 
<?php
// Aktif sayfayı belirle
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="OOF Logo" class="logo">
        <h3>Admin Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class='bx bxs-dashboard'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'rentals.php' ? 'active' : ''; ?>">
                <a href="rentals.php">
                    <i class='bx bxs-car'></i>
                    <span>Kiralamalar</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'cars.php' ? 'active' : ''; ?>">
                <a href="cars.php">
                    <i class='bx bxs-car-garage'></i>
                    <span>Araçlar</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <i class='bx bxs-user'></i>
                    <span>Kullanıcılar</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'locations.php' ? 'active' : ''; ?>">
                <a href="locations.php">
                    <i class='bx bxs-map'></i>
                    <span>Araç Konumları</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class='bx bxs-cog'></i>
                    <span>Ayarlar</span>
                </a>
            </li>
        </ul>
    </nav>
</aside> 
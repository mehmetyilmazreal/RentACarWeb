<?php
session_start();
require_once '../../config/db.php';
require_once '../includes/auth_check.php';

// Format kontrolü
if (!isset($_GET['format']) || !in_array($_GET['format'], ['excel', 'pdf'])) {
    die('Geçersiz format.');
}

// Filtreleri al
$where = [];
$params = [];

if (!empty($_GET['type'])) {
    $where[] = "u.type = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['status'])) {
    $where[] = "u.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['date_range'])) {
    $dates = explode(' to ', $_GET['date_range']);
    if (count($dates) == 2) {
        $where[] = "u.created_at BETWEEN ? AND ?";
        $params[] = $dates[0] . ' 00:00:00';
        $params[] = $dates[1] . ' 23:59:59';
    }
}

if (!empty($_GET['search'])) {
    $where[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $search = '%' . $_GET['search'] . '%';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

// SQL sorgusunu oluştur
$sql = "
    SELECT u.*, 
           COUNT(r.id) as rental_count,
           SUM(r.total_price) as total_spent
    FROM users u
    LEFT JOIN rentals r ON u.id = r.user_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " GROUP BY u.id ORDER BY u.created_at DESC";

// Kullanıcıları getir
$users = $db->query($sql, $params)->fetchAll();

if ($_GET['format'] == 'excel') {
    // Excel başlıkları
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="kullanicilar.xls"');
    header('Cache-Control: max-age=0');

    echo "ID\tAd Soyad\tE-posta\tTelefon\tTip\tDurum\tKayıt Tarihi\tKiralama Sayısı\tToplam Harcama\n";

    foreach ($users as $user) {
        echo implode("\t", [
            $user['id'],
            $user['name'],
            $user['email'],
            $user['phone'],
            $user['type'] == 'individual' ? 'Bireysel' : 'Kurumsal',
            $user['status'] == 'active' ? 'Aktif' : 'Pasif',
            date('d.m.Y', strtotime($user['created_at'])),
            $user['rental_count'],
            number_format($user['total_spent'], 2)
        ]) . "\n";
    }
} else {
    // PDF oluştur
    require_once '../../vendor/autoload.php';

    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f5f5f5; }
            h1 { text-align: center; }
        </style>
    </head>
    <body>
        <h1>Kullanıcı Listesi</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Tip</th>
                    <th>Durum</th>
                    <th>Kayıt Tarihi</th>
                    <th>Kiralama</th>
                    <th>Harcama</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($users as $user) {
        $html .= '
            <tr>
                <td>' . $user['id'] . '</td>
                <td>' . $user['name'] . '</td>
                <td>' . $user['email'] . '</td>
                <td>' . $user['phone'] . '</td>
                <td>' . ($user['type'] == 'individual' ? 'Bireysel' : 'Kurumsal') . '</td>
                <td>' . ($user['status'] == 'active' ? 'Aktif' : 'Pasif') . '</td>
                <td>' . date('d.m.Y', strtotime($user['created_at'])) . '</td>
                <td>' . $user['rental_count'] . '</td>
                <td>₺' . number_format($user['total_spent'], 2) . '</td>
            </tr>';
    }

    $html .= '
            </tbody>
        </table>
    </body>
    </html>';

    // PDF oluştur
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // PDF'i indir
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment;filename="kullanicilar.pdf"');
    echo $dompdf->output();
} 
<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

$status = $_GET['status'] ?? 'disiapkan';
$map = [
    'disiapkan' => 'disiapkan',
    'dimasak'   => 'dimasak',
    'dikirim'   => 'dikirim',
    'sampai'    => 'diterima',
    'selesai'   => 'selesai'
];
$dbStatus = $map[$status] ?? 'disiapkan';

$sql = "SELECT p.id, p.total_harga, p.status, p.dibuat_pada,
               pg.nama AS customer_name,
               pay.metode_pembayaran
        FROM pesanan p
        JOIN pengguna pg ON p.id_pengguna = pg.id
        LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan
        WHERE p.status = ? AND p.dikonfirmasi = 1";

// Jika status 'selesai', hanya tampilkan yang belum diarsipkan
if ($dbStatus === 'selesai') {
    $colCheck = $conn->query("SHOW COLUMNS FROM pesanan LIKE 'is_archived'");
    if ($colCheck->num_rows > 0) {
        $sql .= " AND p.is_archived = 0";
    }
}
$sql .= " ORDER BY p.dibuat_pada ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $dbStatus);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $detail = $conn->query("SELECT m.nama_menu, dp.jumlah, dp.harga FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id WHERE dp.id_pesanan = $id");
    $items = [];
    while ($item = $detail->fetch_assoc()) {
        $items[] = ['name' => $item['nama_menu'], 'qty' => $item['jumlah'], 'price' => $item['harga']];
    }
    $orders[] = [
        'rawId'    => $id,
        'id'       => 'ORD' . str_pad($id, 3, '0', STR_PAD_LEFT),
        'customer' => $row['customer_name'],
        'status'   => $row['status'],
        'items'    => $items,
        'total'    => (int)$row['total_harga'],
        'payment'  => strtoupper($row['metode_pembayaran'] ?? 'COD'),
        'createdAt'=> $row['dibuat_pada']
    ];
}
$stmt->close();

$totalOrders = $conn->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];
$totalCustomers = $conn->query("SELECT COUNT(DISTINCT id_pengguna) as total FROM pesanan")->fetch_assoc()['total'];
$totalMenuItems = $conn->query("SELECT COUNT(*) as total FROM menu")->fetch_assoc()['total'];

// ====================================================================
// LOGIKA MODE BACA: PEMISAHAN OMZET
// ====================================================================
// 1. Total Keseluruhan (Grand Total)
$totalRevenue = $conn->query("SELECT SUM(total_harga) as total FROM pesanan WHERE status = 'selesai'")->fetch_assoc()['total'] ?? 0;

// 2. Total QRIS
$qrisRevQuery = "SELECT SUM(p.total_harga) as total FROM pesanan p LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan WHERE p.status = 'selesai' AND UPPER(pay.metode_pembayaran) = 'QRIS'";
$qrisRevenue = $conn->query($qrisRevQuery)->fetch_assoc()['total'] ?? 0;

// 3. Total CASH & COD
$cashRevQuery = "SELECT SUM(p.total_harga) as total FROM pesanan p LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan WHERE p.status = 'selesai' AND (UPPER(pay.metode_pembayaran) IN ('CASH', 'COD') OR pay.metode_pembayaran IS NULL)";
$cashRevenue = $conn->query($cashRevQuery)->fetch_assoc()['total'] ?? 0;
// ====================================================================

$badges = [];
$badgeMap = [
    'disiapkan' => 'disiapkan',
    'dimasak'   => 'dimasak',
    'dikirim'   => 'dikirim',
    'sampai'    => 'diterima',
    'selesai'   => 'selesai'
];
foreach ($badgeMap as $front => $db) {
    $where = "status = '$db' AND dikonfirmasi = 1";
    if ($db === 'selesai') {
        $colCheck = $conn->query("SHOW COLUMNS FROM pesanan LIKE 'is_archived'");
        if ($colCheck->num_rows > 0) {
            $where .= " AND is_archived = 0";
        }
    }
    $cnt = $conn->query("SELECT COUNT(*) as cnt FROM pesanan WHERE $where")->fetch_assoc()['cnt'];
    $badges[$front] = $cnt;
}

echo json_encode([
    'orders' => $orders,
    'stats'  => [
        'totalOrders'    => (int)$totalOrders,
        'totalCustomers' => (int)$totalCustomers,
        'totalMenuItems' => (int)$totalMenuItems,
        'totalRevenue'   => (int)$totalRevenue,
        'qrisRevenue'    => (int)$qrisRevenue,
        'cashRevenue'    => (int)$cashRevenue
    ],
    'badges' => $badges
]);
?>
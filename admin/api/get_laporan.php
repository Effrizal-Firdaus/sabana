<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

$tipe = $_GET['tipe'] ?? 'penjualan';
$mulai = $_GET['mulai'] ?? date('Y-m-d', strtotime('-30 days'));
$selesai = $_GET['selesai'] ?? date('Y-m-d');
$export = isset($_GET['export']) && $_GET['export'] === 'csv';

if ($tipe === 'penjualan') {
    // PERBAIKAN: Ubah dari status != 'disiapkan' menjadi status = 'selesai'
    $query = "SELECT DATE(dibuat_pada) as tanggal, COUNT(*) as jumlah, SUM(total_harga) as total 
              FROM pesanan 
              WHERE status = 'selesai' AND dibuat_pada BETWEEN ? AND ?
              GROUP BY DATE(dibuat_pada) ORDER BY tanggal DESC";
    $stmt = $conn->prepare($query);
    $selesai_time = $selesai . ' 23:59:59';
    $stmt->bind_param('ss', $mulai, $selesai_time);
    $stmt->execute();
    $res = $stmt->get_result();
    $laporan = [];
    $totalKeseluruhan = 0;
    while ($row = $res->fetch_assoc()) {
        $laporan[] = [
            'tanggal' => $row['tanggal'],
            'jumlah' => (int)$row['jumlah'],
            'total' => (int)$row['total']
        ];
        $totalKeseluruhan += $row['total'];
    }
    if ($export) {
        exportCSV($laporan, ['Tanggal','Jumlah Pesanan','Total Pendapatan']);
        exit;
    }
    echo json_encode(['success' => true, 'laporan' => $laporan, 'total_keseluruhan' => $totalKeseluruhan]);
}
elseif ($tipe === 'produk') {
    // PERBAIKAN: Ubah dari p.status != 'disiapkan' menjadi p.status = 'selesai'
    $query = "SELECT m.nama_menu, SUM(dp.jumlah) as total_terjual, SUM(dp.subtotal) as total_pendapatan
              FROM detail_pesanan dp
              JOIN pesanan p ON dp.id_pesanan = p.id
              JOIN menu m ON dp.id_menu = m.id
              WHERE p.status = 'selesai' AND p.dibuat_pada BETWEEN ? AND ?
              GROUP BY dp.id_menu
              ORDER BY total_terjual DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $selesai_time = $selesai . ' 23:59:59';
    $stmt->bind_param('ss', $mulai, $selesai_time);
    $stmt->execute();
    $res = $stmt->get_result();
    $laporan = [];
    while ($row = $res->fetch_assoc()) {
        $laporan[] = [
            'nama_menu' => $row['nama_menu'],
            'total_terjual' => (int)$row['total_terjual'],
            'total_pendapatan' => (int)$row['total_pendapatan']
        ];
    }
    if ($export) exportCSV($laporan, ['Menu','Total Terjual','Total Pendapatan']);
    echo json_encode(['success' => true, 'laporan' => $laporan, 'mulai' => $mulai, 'selesai' => $selesai]);
}
elseif ($tipe === 'stok') {
    $query = "SELECT id, nama_menu, stok FROM menu ORDER BY nama_menu ASC";
    $res = $conn->query($query);
    $laporan = [];
    while ($row = $res->fetch_assoc()) {
        $laporan[] = [
            'nama_menu' => $row['nama_menu'],
            'stok' => (int)$row['stok']
        ];
    }
    if ($export) exportCSV($laporan, ['Menu','Stok Tersedia']);
    echo json_encode(['success' => true, 'laporan' => $laporan]);
}

function exportCSV($data, $headers) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>
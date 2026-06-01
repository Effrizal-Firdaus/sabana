<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

$query = "SELECT 
            p.id AS pesanan_id,
            p.id_pengguna,
            p.jenis_pesanan,
            p.total_harga,
            p.status,
            p.dibuat_pada,
            pg.nama AS customer_name,
            pg.email,
            pir.alamat,
            pir.deskripsi AS catatan,
            pay.metode_pembayaran,
            pay.status_pembayaran
          FROM pesanan p
          JOIN pengguna pg ON p.id_pengguna = pg.id
          LEFT JOIN pengiriman pir ON p.id = pir.id_pesanan
          LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan
          WHERE p.status = 'disiapkan' 
            AND p.dikonfirmasi = 0
          ORDER BY p.dibuat_pada ASC";

$result = mysqli_query($conn, $query);
$pesanan_list = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id_pesanan = $row['pesanan_id'];
    $detail_query = "SELECT dp.jumlah, dp.harga, m.nama_menu 
                     FROM detail_pesanan dp 
                     JOIN menu m ON dp.id_menu = m.id 
                     WHERE dp.id_pesanan = $id_pesanan";
    $detail_res = mysqli_query($conn, $detail_query);
    $items = [];
    while ($item = mysqli_fetch_assoc($detail_res)) {
        $items[] = [
            'name' => $item['nama_menu'],
            'qty' => $item['jumlah'],
            'price' => $item['harga']
        ];
    }

    $pesanan_list[] = [
        'id' => $row['pesanan_id'],
        'customer' => $row['customer_name'],
        'total' => (int)$row['total_harga'],
        'paymentMethod' => $row['metode_pembayaran'] ?? 'COD',
        'paymentStatus' => $row['status_pembayaran'] ?? 'belum',
        'address' => $row['alamat'] ?? '',
        'note' => $row['catatan'] ?? '',
        'createdAt' => $row['dibuat_pada'],
        'items' => $items,
        'jenis_pesanan' => $row['jenis_pesanan'] ?? 'delivery'
    ];
}

echo json_encode($pesanan_list);
?>
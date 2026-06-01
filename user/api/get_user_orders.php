<?php
session_start();
header('Content-Type: application/json');
// Gunakan session dari array user
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';
$user_id = $_SESSION['user']['id']; 

// Query utama: DITAMBAHKAN p.dikonfirmasi
$query = "SELECT p.id, p.total_harga, p.status, p.dibuat_pada, p.is_riwayat_hidden, p.dikonfirmasi,
                 pay.metode_pembayaran, pay.status_pembayaran,
                 pir.alamat, pir.deskripsi as catatan,
                 (SELECT rating FROM rating WHERE id_pesanan = p.id LIMIT 1) as rating
          FROM pesanan p
          LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan
          LEFT JOIN pengiriman pir ON p.id = pir.id_pesanan
          WHERE p.id_pengguna = ?
          ORDER BY p.dibuat_pada DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    // Jika pesanan status 'selesai' dan is_riwayat_hidden = 1, lewati
    if ($row['status'] === 'selesai' && $row['is_riwayat_hidden'] == 1) {
        continue;
    }
    
    $id_pesanan = $row['id'];
    $detailQuery = "SELECT m.nama_menu, dp.jumlah, dp.harga
                    FROM detail_pesanan dp
                    JOIN menu m ON dp.id_menu = m.id
                    WHERE dp.id_pesanan = ?";
    $stmt2 = $conn->prepare($detailQuery);
    $stmt2->bind_param('i', $id_pesanan);
    $stmt2->execute();
    $detailRes = $stmt2->get_result();
    $items = [];
    $jumlah_item = 0;
    $total_menu = 0;
    $firstItemName = '';
    while ($item = $detailRes->fetch_assoc()) {
        $items[] = [
            'name' => $item['nama_menu'],
            'qty'  => $item['jumlah'],
            'price'=> $item['harga']
        ];
        $jumlah_item += $item['jumlah'];
        $total_menu += $item['harga'] * $item['jumlah'];
        if (empty($firstItemName)) $firstItemName = $item['nama_menu'];
    }
    $stmt2->close();
    $ongkir = $row['total_harga'] - $total_menu;
    $orderName = (count($items) > 1) ? $firstItemName . ' dan lainnya' : $firstItemName;
    
    // Array Order DITAMBAHKAN 'dikonfirmasi'
    $orders[] = [
        'id'                => $id_pesanan,
        'nama'              => $orderName,
        'jumlah'            => $jumlah_item,
        'total'             => (int)$row['total_harga'],
        'status'            => $row['status'],
        'dikonfirmasi'      => $row['dikonfirmasi'], // INI YANG DITAMBAHKAN
        'tgl'               => date('d/m/Y', strtotime($row['dibuat_pada'])),
        'tanggal'           => $row['dibuat_pada'],
        'metodePembayaran'  => strtoupper($row['metode_pembayaran'] ?? 'COD'),
        'status_pembayaran' => $row['status_pembayaran'] ?? 'belum_bayar',
        'items'             => $items,
        'totalMenu'         => $total_menu,
        'ongkir'            => $ongkir,
        'alamat'            => $row['alamat'] ?? '',
        'catatan'           => $row['catatan'] ?? '',
        'rating'            => $row['rating'] ? (int)$row['rating'] : null
    ];
}
$stmt->close();
echo json_encode(['success' => true, 'orders' => $orders]);
?>
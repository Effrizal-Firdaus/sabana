<?php
session_start();
header('Content-Type: application/json');

// Keamanan: Hanya pelanggan yang login yang bisa akses
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include_once __DIR__ . '/../../server/koneksi.php';

// Ambil data JSON dari Javascript
$data = json_decode(file_get_contents("php://input"), true);
$order_id = isset($data['order_id']) ? intval($data['order_id']) : 0;
$user_id = $_SESSION['user']['id'];

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'ID Pesanan tidak valid.']);
    exit;
}

// 1. Cek kepemilikan dan validasi status pesanan
$check_query = "SELECT id, status, dikonfirmasi FROM pesanan WHERE id = ? AND id_pengguna = ?";
$stmt_check = $conn->prepare($check_query);
$stmt_check->bind_param("ii", $order_id, $user_id);
$stmt_check->execute();
$res = $stmt_check->get_result();
$order = $res->fetch_assoc();
$stmt_check->close();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan atau bukan milik Anda.']);
    exit;
}

// 2. Validasi Kunci: Tolak jika sudah dikonfirmasi admin atau statusnya bukan 'disiapkan'
if ($order['status'] !== 'disiapkan' || $order['dikonfirmasi'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Pesanan sudah diproses admin dan tidak dapat dibatalkan.']);
    exit;
}

// 3. Eksekusi Pembatalan dan Pengembalian Stok
$conn->begin_transaction();
try {
    // Kembalikan stok ayam/menu ke database
    $q_stok = "SELECT id_menu, jumlah FROM detail_pesanan WHERE id_pesanan = ?";
    $stmt_stok = $conn->prepare($q_stok);
    $stmt_stok->bind_param('i', $order_id);
    $stmt_stok->execute();
    $res_stok = $stmt_stok->get_result();

    $q_update_stok = "UPDATE menu SET stok = stok + ? WHERE id = ?";
    $stmt_update_stok = $conn->prepare($q_update_stok);
    while ($row = $res_stok->fetch_assoc()) {
        $stmt_update_stok->bind_param('ii', $row['jumlah'], $row['id_menu']);
        $stmt_update_stok->execute();
    }
    $stmt_stok->close();
    $stmt_update_stok->close();

    // Hapus seluruh jejak pesanan dari tabel berelasi
    $conn->query("DELETE FROM detail_pesanan WHERE id_pesanan = $order_id");
    $conn->query("DELETE FROM pembayaran WHERE id_pesanan = $order_id");
    $conn->query("DELETE FROM pengiriman WHERE id_pesanan = $order_id");
    $conn->query("DELETE FROM pesanan WHERE id = $order_id");

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dibatalkan.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
}
?>  
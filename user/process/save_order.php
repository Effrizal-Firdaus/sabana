<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login.']);
    exit;
}
$id_pengguna = (int)$_SESSION['user']['id'];

$path = __DIR__ . '/../../server/koneksi.php';
include_once $path;
if (!file_exists($path)) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database tidak ditemukan: ' . $path]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    exit;
}

$id_pengguna = (int)$input['id_pengguna'];
$total_bayar = (int)$input['total_bayar'];
$nama_penerima = trim($input['nama_penerima']);
$alamat = trim($input['alamat']);
$catatan = isset($input['catatan']) ? trim($input['catatan']) : '';
$metode_pembayaran = $input['metode_pembayaran'];
$status_pembayaran_input = $input['status_pembayaran'];
$items = $input['items'];
$kode_qr = isset($input['kode_qr']) ? $input['kode_qr'] : null;
$jenis_pesanan = isset($input['jenis_pesanan']) ? $input['jenis_pesanan'] : 'delivery';

if ($status_pembayaran_input === 'lunas') {
    $status_pembayaran_db = 'sudah_bayar';
    $waktu_bayar = date('Y-m-d H:i:s');
} else {
    $status_pembayaran_db = 'belum_bayar';
    $waktu_bayar = null;
}

$conn->begin_transaction();
try {
    // ========== LOGIKA NOMOR ANTRIAN (RESET TIAP HARI) ==========
    date_default_timezone_set('Asia/Jakarta');
    $tanggal_hari_ini = date('Y-m-d');

    $query_antrian = "SELECT MAX(nomor_antrian) as max_antrian FROM pesanan WHERE DATE(dibuat_pada) = ?";
    $stmt_antrian = $conn->prepare($query_antrian);
    $stmt_antrian->bind_param("s", $tanggal_hari_ini);
    $stmt_antrian->execute();
    $result_antrian = $stmt_antrian->get_result();
    $row_antrian = $result_antrian->fetch_assoc();

    // Jika hari ini sudah ada pesanan, tambah 1. Jika belum ada, mulai dari 1.
    $nomor_antrian_baru = ($row_antrian['max_antrian'] != null) ? (int)$row_antrian['max_antrian'] + 1 : 1;
    $stmt_antrian->close();
    // ============================================================

    // 1. Insert ke pesanan (Ditambahkan kolom nomor_antrian)
    $stmt = $conn->prepare("INSERT INTO pesanan (id_pengguna, jenis_pesanan, total_harga, nomor_antrian, status, dibuat_pada) VALUES (?, ?, ?, ?, 'disiapkan', NOW())");
    
    // Tipe data parameter: i (integer), s (string), i (integer), i (integer)
    $stmt->bind_param('isii', $id_pengguna, $jenis_pesanan, $total_bayar, $nomor_antrian_baru);
    $stmt->execute();
    $id_pesanan = $conn->insert_id;
    $stmt->close();

    // 2. Detail pesanan & update stok
    foreach ($items as $item) {
        $id_menu = (int)$item['id'];
        $jumlah = (int)$item['qty'];
        $harga = (int)$item['price'];
        $subtotal = $jumlah * $harga;

        $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiid', $id_pesanan, $id_menu, $jumlah, $harga, $subtotal);
        $stmt->execute();
        $stmt->close();
    }

    // 3. Pengiriman
    $stmt = $conn->prepare("INSERT INTO pengiriman (id_pesanan, alamat, deskripsi, status_pengiriman) VALUES (?, ?, ?, 'menunggu')");
    if ($jenis_pesanan === 'delivery') {
        $stmt->bind_param('iss', $id_pesanan, $alamat, $catatan);
    } else {
        // Take away: alamat NULL
        $nullAlamat = null;
        $stmt->bind_param('iss', $id_pesanan, $nullAlamat, $catatan);
    }
    $stmt->execute();
    $stmt->close();

    // 4. Pembayaran
    $metode = ($metode_pembayaran === 'QRIS') ? 'qris' : 'cash';
    $stmt = $conn->prepare("INSERT INTO pembayaran (id_pesanan, metode_pembayaran, jumlah_bayar, status_pembayaran, kode_qr, waktu_bayar) VALUES (?, ?, ?, ?, ?, ?)");
    $types = 'i' . 's' . 'i' . 's' . 's' . 's';
    $stmt->bind_param($types, $id_pesanan, $metode, $total_bayar, $status_pembayaran_db, $kode_qr, $waktu_bayar);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'id_pesanan' => $id_pesanan]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
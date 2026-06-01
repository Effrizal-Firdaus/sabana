<?php
session_start();
header('Content-Type: application/json');

// Keamanan: Pastikan hanya admin yang bisa mengakses API ini
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Sesuaikan path jika file ini ada di dalam admin/api/
include_once __DIR__ . '/../../server/koneksi.php';

// Query asli milik Anda (tidak diubah)
$query = "SELECT r.*, p.nama AS pengguna, m.nama_menu, ps.id AS pesanan_id
          FROM rating r
          JOIN pesanan ps ON r.id_pesanan = ps.id
          JOIN pengguna p ON ps.id_pengguna = p.id
          JOIN menu m ON r.id_menu = m.id
          ORDER BY r.dibuat_pada DESC";

$result = $conn->query($query);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Kembalikan data dalam bentuk JSON
echo json_encode($data);
?>
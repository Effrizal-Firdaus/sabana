<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

// Menambahkan status dan terakhir_login pada query
$result = $conn->query("SELECT id, nama, email, peran, status, terakhir_login, dibuat_pada FROM pengguna ORDER BY id ASC");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id' => (int)$row['id'],
        'nama' => $row['nama'],
        'email' => $row['email'],
        'peran' => $row['peran'],
        // Mengambil status dari DB
        'status' => $row['status'] ?? 'aktif',
        // Format tanggal terakhir_login jika datanya ada
        'terakhir_login' => $row['terakhir_login'] ? date('d/m/Y H:i', strtotime($row['terakhir_login'])) : null,
        'dibuat_pada' => date('d/m/Y H:i', strtotime($row['dibuat_pada']))
    ];
}
echo json_encode(['success' => true, 'users' => $users]);
?>
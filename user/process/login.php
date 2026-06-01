<?php
session_start();
include "../../server/koneksi.php";

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header("Location: ../login.html?error=empty_fields");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: ../login.html?error=not_found");
    exit;
}

$row = $res->fetch_assoc();

// =========================================================
// [FITUR BARU] CEK STATUS BLOKIR (Tolak akses sebelum cek password)
// =========================================================
if (isset($row['status']) && $row['status'] === 'diblokir') {
    // Arahkan kembali ke halaman login dengan pesan error khusus
    header("Location: ../login.html?error=blocked");
    exit;
}

$is_valid = false;

// Cek password (support hash dan plain text)
if (password_verify($password, $row['password'])) {
    $is_valid = true;
} elseif ($row['peran'] === 'admin' && $password === $row['password']) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE pengguna SET password = ? WHERE id = ?");
    $update->bind_param("si", $hashed, $row['id']);
    $update->execute();
    $is_valid = true;
}

if (!$is_valid) {
    header("Location: ../login.html?error=wrong_password");
    exit;
}

// =========================================================
// [FITUR BARU] UPDATE WAKTU TERAKHIR LOGIN (Jika password benar)
// =========================================================
$update_waktu = $conn->prepare("UPDATE pengguna SET terakhir_login = NOW() WHERE id = ?");
$update_waktu->bind_param("i", $row['id']);
$update_waktu->execute();

// Tentukan role
$role = $row['peran'];

if ($role === 'admin') {
    // Simpan session admin tanpa menghapus session user
    $_SESSION['admin'] = [
        'id'    => $row['id'],
        'nama'  => $row['nama'],
        'email' => $row['email'],
        'peran' => 'admin'
    ];
    header("Location: ../../admin/dashboard.php");
    exit;
} else {
    // Simpan session user tanpa menghapus session admin
    $_SESSION['user'] = [
        'id'    => $row['id'],
        'nama'  => $row['nama'],
        'email' => $row['email'],
        'peran' => 'user'
    ];
    header("Location: ../menu_utama.php");
    exit;
}
?>
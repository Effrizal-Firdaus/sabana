<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

$action = $_POST['action'] ?? '';
if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
    exit;
}

if ($action === 'tambah') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '12345678';
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    // Tambahkan default 'aktif' untuk kolom status
    $stmt = $conn->prepare("INSERT INTO pengguna (nama, email, password, peran, status) VALUES (?, ?, ?, ?, 'aktif')");
    $stmt->bind_param('ssss', $nama, $email, $hashed, $role);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email mungkin sudah terdaftar']);
    }
    $stmt->close();
}
elseif ($action === 'edit') {
    $id = (int)$_POST['id'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE pengguna SET nama=?, email=?, peran=?, password=? WHERE id=?");
        $stmt->bind_param('ssssi', $nama, $email, $role, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE pengguna SET nama=?, email=?, peran=? WHERE id=?");
        $stmt->bind_param('sssi', $nama, $email, $role, $id);
    }
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
elseif ($action === 'hapus') {
    $id = (int)$_POST['id'];
    // Jangan hapus admin utama (id=1) untuk keamanan
    if ($id === 1) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus admin utama']);
        exit;
    }
    $conn->query("DELETE FROM pengguna WHERE id = $id");
    echo json_encode(['success' => true]);
}
elseif ($action === 'reset') {
    $id = (int)$_POST['id'];
    $newPass = '12345678';
    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE pengguna SET password = ? WHERE id = ?");
    $stmt->bind_param('si', $hashed, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
// ==========================================
// FITUR BARU: BLOKIR PENGGUNA
// ==========================================
elseif ($action === 'blokir') {
    $id = (int)$_POST['id'];
    $status_baru = $_POST['status']; // 'aktif' atau 'diblokir' dari JS
    
    // Jangan blokir admin utama (id=1) untuk keamanan, persis seperti fitur hapus Anda
    if ($id === 1) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat memblokir admin utama']);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE pengguna SET status=? WHERE id=?");
    $stmt->bind_param('si', $status_baru, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
}
?>
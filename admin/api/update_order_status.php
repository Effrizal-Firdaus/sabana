<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
include_once __DIR__ . '/../../server/koneksi.php';

// --- TAMBAHAN LOGIKA BRANKAS: Panggil file helper ---
require_once 'catat_laporan.php'; 
// ----------------------------------------------------

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = $_POST['action'] ?? 'next';
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

// =========================================================================
// BLOK BARU: LOGIKA MENOLAK / MENGHAPUS PESANAN MASUK
// =========================================================================
if ($action === 'reject') {
    $conn->begin_transaction();
    try {
        // 1. Ambil data detail pesanan untuk melihat jumlah ayam yang di-booking
        $q_stok = "SELECT id_menu, jumlah FROM detail_pesanan WHERE id_pesanan = ?";
        $stmt_stok = $conn->prepare($q_stok);
        $stmt_stok->bind_param('i', $id);
        $stmt_stok->execute();
        $res_stok = $stmt_stok->get_result();

        // 2. Kembalikan stok ayam/menu tersebut ke tabel menu
        $q_update_stok = "UPDATE menu SET stok = stok + ? WHERE id = ?";
        $stmt_update_stok = $conn->prepare($q_update_stok);
        while ($row = $res_stok->fetch_assoc()) {
            $stmt_update_stok->bind_param('ii', $row['jumlah'], $row['id_menu']);
            $stmt_update_stok->execute();
        }
        $stmt_stok->close();
        $stmt_update_stok->close();

        // 3. Hapus semua jejak pesanan di tabel anak (Relasi)
        // Harus dihapus dari anak dulu agar tidak error Foreign Key
        $conn->query("DELETE FROM detail_pesanan WHERE id_pesanan = $id");
        $conn->query("DELETE FROM pembayaran WHERE id_pesanan = $id");
        $conn->query("DELETE FROM pengiriman WHERE id_pesanan = $id");
        $conn->query("DELETE FROM rating WHERE id_pesanan = $id");

        // 4. Terakhir, hapus pesanan utama
        $conn->query("DELETE FROM pesanan WHERE id = $id");

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Pesanan dibatalkan dan stok dikembalikan.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
    }
    exit;
}
// =========================================================================

if ($action === 'confirm') {
    // Cek apakah pesanan ada dan status 'disiapkan' serta belum dikonfirmasi
    $check = $conn->prepare("SELECT id, status, dikonfirmasi FROM pesanan WHERE id = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $res = $check->get_result();
    $order = $res->fetch_assoc();
    $check->close();

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    if ($order['status'] !== 'disiapkan') {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak dalam status disiapkan']);
        exit;
    }
    if ($order['dikonfirmasi'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Pesanan sudah dikonfirmasi sebelumnya']);
        exit;
    }

    $update = $conn->prepare("UPDATE pesanan SET dikonfirmasi = 1 WHERE id = ?");
    $update->bind_param('i', $id);
    if ($update->execute() && $update->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Pesanan diterima dan akan muncul di dashboard']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal konfirmasi: ' . $conn->error]);
    }
    $update->close();
    exit;
}

if ($action === 'archive') {
    // Hanya untuk pesanan dengan status 'selesai' dan belum diarsipkan
    $checkSt = $conn->prepare("SELECT status, is_archived FROM pesanan WHERE id = ?");
    $checkSt->bind_param('i', $id);
    $checkSt->execute();
    $resCheck = $checkSt->get_result();
    $orderData = $resCheck->fetch_assoc();
    $checkSt->close();

    if (!$orderData) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    if ($orderData['status'] !== 'selesai') {
        echo json_encode(['success' => false, 'message' => 'Hanya pesanan dengan status "Selesai" yang bisa diarsipkan']);
        exit;
    }
    if ($orderData['is_archived'] == 1) {
        echo json_encode(['success' => false, 'message' => 'Pesanan sudah diarsipkan sebelumnya']);
        exit;
    }

    $update = $conn->prepare("UPDATE pesanan SET is_archived = 1 WHERE id = ?");
    $update->bind_param('i', $id);
    if ($update->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pesanan berhasil diarsipkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengarsipkan: ' . $conn->error]);
    }
    $update->close();
    exit;
}

if ($action === 'unarchive') {
    $update = $conn->prepare("UPDATE pesanan SET is_archived = 0 WHERE id = ?");
    $update->bind_param('i', $id);
    if ($update->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false]);
    exit;
}

$statusOrder = ['disiapkan', 'dimasak', 'dikirim', 'diterima', 'selesai'];
$query = "SELECT status FROM pesanan WHERE id = ? AND dikonfirmasi = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    exit;
}
$current = $row['status'];
$idx = array_search($current, $statusOrder);
if ($idx !== false && $idx < count($statusOrder)-1) {
    $newStatus = $statusOrder[$idx+1];
    $conn->begin_transaction();
    try {
        $update = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
        $update->bind_param('si', $newStatus, $id);
        $update->execute();
        $update->close();
        
        if ($newStatus === 'selesai') {
            // --- TAMBAHAN LOGIKA BRANKAS: Ambil total harga dan metode bayar ---
            $payQuery = "SELECT p.total_harga, b.metode_pembayaran 
                         FROM pesanan p 
                         JOIN pembayaran b ON p.id = b.id_pesanan 
                         WHERE p.id = ?";
            $payStmt = $conn->prepare($payQuery);
            $payStmt->bind_param('i', $id);
            $payStmt->execute();
            $payRes = $payStmt->get_result();
            $payRow = $payRes->fetch_assoc();
            $payStmt->close();
            
            if ($payRow) {
                // 1. Eksekusi Pembayaran Cash (Kode Lama)
                if ($payRow['metode_pembayaran'] === 'cash') {
                    $updatePay = $conn->prepare("UPDATE pembayaran SET status_pembayaran = 'sudah_bayar', waktu_bayar = NOW() WHERE id_pesanan = ?");
                    $updatePay->bind_param('i', $id);
                    $updatePay->execute();
                    $updatePay->close();
                }
                
                // 2. EKSEKUSI BRANKAS PERMANEN (Kode Baru)
                catatPendapatanPermanen($conn, $id, $payRow['total_harga'], $payRow['metode_pembayaran']);
            }
            // --------------------------------------------------------------------

            $updateKirim = $conn->prepare("UPDATE pengiriman SET status_pengiriman = 'diterima', waktu_pengiriman = NOW() WHERE id_pesanan = ?");
            $updateKirim->bind_param('i', $id);
            $updateKirim->execute();
            $updateKirim->close();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Status sudah akhir']);
}
?>
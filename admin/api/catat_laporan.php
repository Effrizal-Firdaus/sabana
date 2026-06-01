<?php
/**
 * Fungsi: Helper untuk mencatat pendapatan ke dalam tabel 'laporan_permanen' (Ledger/Buku Besar)
 * Fungsi ini dipanggil ketika pesanan telah mencapai status 'selesai'.
 * * @param mysqli $conn Objek koneksi database aktif
 * @param int $id_pesanan ID dari pesanan yang selesai
 * @param float $total_pendapatan Total nominal tagihan (harga + ongkir jika ada)
 * @param string $metode_pembayaran Metode pembayaran yang digunakan (cash/qris)
 * @return boolean True jika berhasil masuk atau sudah ada, False jika gagal eksekusi
 */
function catatPendapatanPermanen($conn, $id_pesanan, $total_pendapatan, $metode_pembayaran) {
    
    // 1. Validasi Keamanan: Cek apakah id_pesanan ini sudah pernah dicatat sebelumnya
    // Hal ini untuk mencegah duplikasi data jika admin tidak sengaja memicu status 'selesai' 2 kali
    $cek_query = "SELECT id FROM laporan_permanen WHERE id_pesanan = ?";
    $stmt_cek = $conn->prepare($cek_query);
    $stmt_cek->bind_param("i", $id_pesanan);
    $stmt_cek->execute();
    $result = $stmt_cek->get_result();
    
    // 2. Jika data belum ada di dalam brankas (num_rows === 0), lakukan penyimpanan
    if($result->num_rows === 0) {
        // Query Insert dengan fungsi NOW() agar waktu pencatatan sesuai waktu server saat ini
        $insert_query = "INSERT INTO laporan_permanen (id_pesanan, total_pendapatan, metode_pembayaran, tanggal_selesai) VALUES (?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($insert_query);
        
        // Penjelasan bind_param: 
        // i = integer (id_pesanan)
        // d = double/decimal (total_pendapatan)
        // s = string (metode_pembayaran)
        $stmt_insert->bind_param("ids", $id_pesanan, $total_pendapatan, $metode_pembayaran);
        
        if($stmt_insert->execute()) {
            return true; // Data berhasil diamankan ke brankas
        } else {
            return false; // Terjadi kesalahan saat menyimpan
        }
    }
    
    // Jika data sudah ada di database, kita anggap proses ini selesai dan aman
    return true; 
}
?>
<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    header('Location: ../user/login.html');
    exit;
}
include_once __DIR__ . '/../server/koneksi.php';

// Ambil data pesanan yang sudah diarsipkan (is_archived = 1)
$query = "SELECT p.*, u.nama as nama_pelanggan, pay.metode_pembayaran 
          FROM pesanan p
          JOIN pengguna u ON p.id_pengguna = u.id
          LEFT JOIN pembayaran pay ON p.id = pay.id_pesanan
          WHERE p.is_archived = 1
          ORDER BY p.diupdate_pada DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Arsip Pesanan Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100">
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <div class="w-80 bg-gradient-to-br from-[#2c3e50] to-[#34495e] text-white fixed h-full overflow-y-auto shadow-lg z-30">
            <div class="p-6">
                <div class="flex items-center gap-3 bg-emerald-600/30 p-4 rounded-xl mb-8">
                    <i class="fa-solid fa-user-shield text-3xl"></i>
                    <h2 class="text-xl font-bold">Admin Panel</h2>
                </div>
                <nav class="flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white pr-2">
                            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 flex-1 font-semibold">
                                <i class="fa-solid fa-chart-line w-5"></i>
                                <span>Dashboard</span>
                            </a>
                            <button id="toggleArsipBtn" class="p-2 mr-1 transition-transform duration-300 transform rotate-180 focus:outline-none">
                                <i class="fa-solid fa-chevron-down text-sm"></i>
                            </button>
                        </div>

                        <div id="submenuArsip" class="flex flex-col gap-1 pl-9 pr-2 py-1 transition-all duration-300">
                            <a href="arsip.php" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-emerald-600 text-white shadow-md border-l-4 border-emerald-300 text-sm font-semibold transition-all duration-300">
                                <i class="fa-solid fa-box-archive w-4 text-center"></i>
                                <span>Arsip Pesanan</span>
                            </a>
                        </div>
                    </div>
                    <a href="kelola_menu.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-utensils w-5"></i> Kelola Menu</a>
                    <a href="pesanan.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-receipt w-5"></i> Pesanan</a>
                    <a href="pengguna.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-users w-5"></i> Pengguna</a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-file-alt w-5"></i> Laporan</a>
                    <a href="masukan.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-message w-5"></i> Masukan</a>
                    <a href="ulasan.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-star w-5"></i> Ulasan</a>
                    
                    <!-- TOMBOL LOGOUT STANDAR (MERAH) -->
                    <div class="mt-8 pt-4 border-t border-gray-700 w-full">
                        <a href="#" id="btnTriggerLogout" class="flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition-all duration-300 shadow-md hover:shadow-rose-500/30 active:scale-95 w-full">
                            <i class="fa-solid fa-sign-out-alt text-lg"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 ml-80 bg-slate-50 min-h-screen p-8">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-5 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-box-archive mr-2 text-emerald-500"></i> Data Arsip Pesanan</h1>
                        <p class="text-sm text-gray-500 mt-1">Menampilkan pesanan berstatus selesai yang telah diarsipkan.</p>
                    </div>
                    <span class="bg-slate-200 text-slate-700 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">Total: <?= $result->num_rows ?> Arsip</span>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="p-4">ID Pesanan</th>
                                    <th class="p-4">Pelanggan</th>
                                    <th class="p-4 text-center">Tanggal Selesai</th>
                                    <th class="p-4 text-center">Metode</th>
                                    <th class="p-4 text-right">Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="5" class="p-12 text-center text-gray-400 italic">Belum ada pesanan yang diarsipkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="hover:bg-slate-50 transition duration-150">
                                            <td class="p-4 font-bold text-gray-800">ORD-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                            <td class="p-4 font-medium"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                            <td class="p-4 text-center text-gray-500"><i class="fa-regular fa-clock mr-1"></i> <?= date('d/m/Y H:i', strtotime($row['diupdate_pada'])) ?></td>
                                            <td class="p-4 text-center">
                                                <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md text-xs font-bold uppercase border border-blue-100">
                                                    <?= $row['metode_pembayaran'] ?? 'CASH' ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-right font-bold text-emerald-600">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL LOGOUT -->
    <div id="logoutModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="logoutOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="logoutModalBox">
            <div class="w-16 h-16 bg-emerald-600/10 rounded-full flex items-center justify-center mb-5 shadow-inner">
                <i class="fa-solid fa-arrow-right-from-bracket text-3xl text-emerald-600 ml-1"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-500 mb-8 text-sm">Apakah Anda yakin ingin keluar dari sesi ini?</p>
            <div class="flex gap-4 w-full">
                <button id="btnCancelLogout" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-xl font-bold text-center transition">Tidak</button>
                <a href="process/logout.php" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-center flex items-center justify-center transition shadow-md">Iya, Logout</a>
            </div>
        </div>
    </div>

    <!-- SCRIPT TERPISAH -->
    <script src="js/arsip-admin.js?v=<?= time() ?>"></script>
</body>

</html>
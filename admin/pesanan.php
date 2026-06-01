<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    header('Location: ../user/login.html');
    exit;
}
// Tidak perlu query manual lagi karena akan di-fetch oleh AJAX
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/pesanan.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .timer-badge {
            font-family: monospace;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-80 bg-gradient-to-br from-[#2c3e50] to-[#34495e] text-white fixed h-full overflow-y-auto shadow-lg z-30">
            <div class="p-6">
                <div class="flex items-center gap-3 bg-emerald-600/30 p-4 rounded-xl mb-8">
                    <i class="fa-solid fa-user-shield text-3xl"></i>
                    <h2 class="text-xl font-bold whitespace-nowrap">Admin Panel</h2>
                </div>
                <nav class="flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white pr-2">
                            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 flex-1 font-semibold">
                                <i class="fa-solid fa-chart-line w-5"></i>
                                <span>Dashboard</span>
                            </a>
                            <button id="toggleArsipBtn" class="p-2 mr-1 transition-transform duration-300 transform rotate-0 focus:outline-none hover:text-white">
                                <i class="fa-solid fa-chevron-down text-sm"></i>
                            </button>
                        </div>
                        <div id="submenuArsip" class="hidden flex-col gap-1 pl-9 pr-2 py-1 transition-all duration-300">
                            <a href="arsip.php" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:bg-white/10 hover:text-white text-sm font-medium transition-all duration-300">
                                <i class="fa-solid fa-box-archive w-4 text-center"></i>
                                <span>Arsip Pesanan</span>
                            </a>
                        </div>
                    </div>
                    <a href="kelola_menu.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-utensils w-5"></i> <span>Kelola Menu</span>
                    </a>
                    <a href="pesanan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 bg-emerald-600 text-white shadow-lg border-l-4 border-emerald-300 font-semibold">
                        <i class="fa-solid fa-receipt w-5"></i>
                        <span>Pesanan</span>
                        <span id="pesananBadge" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 hidden">0</span>
                    </a>
                    <a href="pengguna.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-users w-5"></i> <span>Pengguna</span>
                    </a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-file-alt w-5"></i> <span>Laporan</span>
                    </a>
                    <a href="masukan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-message w-5"></i><span>Masukan</span>
                    </a>
                    <a href="ulasan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-star w-5"></i> Ulasan
                    </a>
                    <!-- Pembatas visual -->
                    <div class="mt-8 pt-4 border-t border-gray-700 w-full">
                        <a href="#" id="btnTriggerLogout" class="flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition-all duration-300 shadow-md hover:shadow-rose-500/30 active:scale-95 w-full">
                            <i class="fa-solid fa-sign-out-alt text-lg"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <div class="flex-1 ml-80 bg-slate-50 min-h-screen p-10">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-extrabold text-gray-800 flex items-center gap-3">
                    <i class="fa-solid fa-bag-shopping text-emerald-500"></i> Pesanan Delivery Masuk
                </h1>
                <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm">
                    <i class="fa-regular fa-clock"></i> pesanan dengan status <span class="font-bold text-emerald-500">disiapkan</span> yang belum dikonfirmasi oleh admin
                </div>
            </div>

            <div id="newOrdersContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-full text-center text-gray-400 py-16 flex flex-col items-center">
                    <i class="fa-solid fa-circle-notch fa-spin text-5xl mb-4 opacity-50"></i>
                    <p class="font-medium">Mencari pesanan baru...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Logout -->
    <div id="logoutModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="logoutOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="logoutModalBox">
            <div class="w-16 h-16 bg-emerald-600/10 rounded-full flex items-center justify-center mb-5 shadow-inner">
                <i class="fa-solid fa-arrow-right-from-bracket text-3xl text-emerald-600 ml-1"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-500 mb-8 text-sm">Apakah Anda yakin ingin keluar dari sesi ini?</p>
            <div class="flex gap-4 w-full">
                <button id="btnCancelLogout" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-xl font-bold text-center transition">Tidak</button>
                <a href="process/logout.php" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold flex items-center justify-center shadow-md transition">Iya, Logout</a>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="deleteOverlay" onclick="closeDeleteModal()"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="deleteModalBox">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-5">
                <i class="fa-solid fa-trash-can text-3xl text-emerald-600"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Hapus Pesanan?</h3>
            <p class="text-gray-500 mb-8 text-sm">Apakah Anda yakin ingin menghapus pesanan ini? Stok menu akan dikembalikan dan tidak akan diproses ke dapur.</p>
            <div class="flex gap-4 w-full">
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 py-3 rounded-xl font-bold transition text-gray-700">Tidak</button>
                <button onclick="eksekusiHapusPesanan()" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-emerald-500/30">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script>
        const btnTrigger = document.getElementById('btnTriggerLogout');
        const modalLogout = document.getElementById('logoutModal');
        const modalBoxLogout = document.getElementById('logoutModalBox');
        const cancelBtnLogout = document.getElementById('btnCancelLogout');
        const overlayLogout = document.getElementById('logoutOverlay');

        function showLogoutModal() {
            modalLogout.classList.remove('hidden');
            setTimeout(() => {
                modalBoxLogout.classList.remove('scale-95', 'opacity-0');
                modalBoxLogout.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function hideLogoutModal() {
            modalBoxLogout.classList.remove('scale-100', 'opacity-100');
            modalBoxLogout.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modalLogout.classList.add('hidden'), 300);
        }
        btnTrigger?.addEventListener('click', (e) => {
            e.preventDefault();
            showLogoutModal();
        });
        cancelBtnLogout?.addEventListener('click', hideLogoutModal);
        overlayLogout?.addEventListener('click', hideLogoutModal);
    </script>

    <script src="js/toast.js"></script>
    <script src="js/pesanan.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/sidebar-admin.js?v=<?= time() ?>"></script>
</body>

</html>
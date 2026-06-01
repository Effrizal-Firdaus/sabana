<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    header('Location: ../user/login.html');
    exit;
}
$admin_nama = htmlspecialchars($_SESSION['admin']['nama']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="w-80 bg-gradient-to-br from-[#2c3e50] to-[#34495e] text-white fixed h-full overflow-y-auto shadow-lg">
            <div class="p-6">
                <div class="flex items-center gap-3 bg-emerald-600/30 p-4 rounded-xl mb-8">
                    <i class="fa-solid fa-user-shield text-3xl"></i>
                    <h2 class="text-xl font-bold whitespace-nowrap">Admin Panel</h2>
                </div>
                <nav class="flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between rounded-lg transition-all duration-300 bg-emerald-600 text-white shadow-lg shadow-emerald-600/40 border-l-4 border-emerald-300 pr-2">
                            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 flex-1 font-semibold">
                                <i class="fa-solid fa-chart-line w-5"></i>
                                <span>Dashboard</span>
                            </a>
                            <button id="toggleArsipBtn" class="p-2 mr-1 text-white hover:text-emerald-200 transition-transform duration-300 transform">
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
                        <i class="fa-solid fa-utensils w-5"></i>
                        <span>Kelola Menu</span>
                    </a>
                    <a href="pesanan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-receipt w-5"></i>
                        <span>Pesanan</span>
                        <span id="pesananBadge" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 hidden">0</span>
                    </a>
                    <a href="pengguna.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-users w-5"></i>
                        <span>Pengguna</span>
                    </a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-file-alt w-5"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="masukan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-message w-5"></i><span>Masukan</span>
                    </a>
                    <a href="ulasan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-star w-5"></i> Ulasan
                    </a>
                    <div class="mt-8 pt-4 border-t border-gray-700">
                        <a href="#" id="btnTriggerLogout" class="flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition-all duration-300 shadow-md hover:shadow-rose-500/30 active:scale-95">
                            <i class="fa-solid fa-sign-out-alt text-lg"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <div class="flex-1 ml-80 bg-slate-50 min-h-screen">
            <header class="sticky top-0 z-40 px-10 pt-6 pb-4 bg-slate-50/90 backdrop-blur-md border-b border-gray-200/50">
                <div class="flex justify-between items-center bg-white p-4 pl-5 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600/20 to-emerald-600/5 flex items-center justify-center border border-emerald-600/10">
                            <i class="fa-solid fa-chart-line text-emerald-600 text-xl shadow-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-800 to-gray-600 tracking-tight">Dashboard Overview</h1>
                            <p class="text-xs font-medium text-gray-400 mt-0.5">Pantau aktivitas, pendapatan, dan pesanan restoran Sabana Anda.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 pr-2">
                        <button class="relative w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors border border-gray-200 shadow-sm active:scale-95">
                            <i class="fa-regular fa-bell"></i>
                        </button>
                        <div class="w-px h-8 bg-gray-200"></div>
                        <div class="user-info flex items-center gap-3 bg-white border border-transparent text-gray-700 rounded-full cursor-pointer transition-all duration-300 hover:bg-gray-50 active:scale-95 group">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Admin&background=059669&color=fff&rounded=true&bold=true" alt="Admin" class="w-10 h-10 rounded-full shadow-sm group-hover:scale-105 transition-transform">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex flex-col pr-2">
                                <span class="font-extrabold text-sm text-gray-800 leading-none group-hover:text-emerald-600 transition-colors">Administrator</span>
                                <span class="text-[10px] font-bold text-gray-400 mt-1">Super Admin</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 pr-2 group-hover:text-emerald-600 transition-colors"></i>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="modern-card p-6 flex items-center gap-5 relative overflow-hidden group cursor-pointer bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300">
                        <div class="icon-box w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-xl group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-300">
                            <i class="fa-solid fa-shopping-bag"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1 group-hover:text-blue-500 transition-colors duration-300">Total Pesanan</p>
                            <h3 id="totalOrders" class="text-2xl font-extrabold text-gray-800">0</h3>
                        </div>
                    </div>

                    <div class="modern-card p-6 flex items-center gap-5 relative overflow-hidden group cursor-pointer bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300">
                        <div class="icon-box w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-500 text-xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1 group-hover:text-purple-500 transition-colors duration-300">Total Pelanggan</p>
                            <h3 id="totalCustomers" class="text-2xl font-extrabold text-gray-800">0</h3>
                        </div>
                    </div>

                    <div class="modern-card p-6 flex items-center gap-5 relative overflow-hidden group cursor-pointer bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300">
                        <div class="icon-box w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 text-xl group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-300">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1 group-hover:text-orange-500 transition-colors duration-300">Total Menu</p>
                            <h3 id="totalMenuItems" class="text-2xl font-extrabold text-gray-800">0</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                    <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group cursor-pointer hover:border-emerald-400 hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300">
                        <div class="absolute -right-4 -bottom-4 opacity-[0.20] text-emerald-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                            <i class="fa-solid fa-chart-line text-9xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2 flex items-center gap-2 z-10">
                            <i class="fa-solid fa-money-bill-trend-up text-emerald-500 inline-block group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-300"></i>
                            Total Omzet
                        </p>
                        <h3 id="totalRevenue" class="text-3xl font-black text-gray-800 z-10">Rp 0</h3>
                        <p class="text-[10px] text-gray-400 mt-2 font-medium z-10">Seluruh pendapatan penjualan (QRIS & Tunai)</p>
                    </div>

                    <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center relative overflow-hidden group cursor-pointer hover:border-blue-400 hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300">
                        <div class="absolute -right-4 -bottom-4 opacity-[0.20] text-blue-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                            <i class="fa-solid fa-building-columns text-9xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2 flex items-center gap-2 z-10">
                            <i class="fa-solid fa-building-columns text-blue-500 inline-block group-hover:scale-125 group-hover:rotate-12 transition-transform duration-300"></i>
                            Saldo Bank
                        </p>
                        <h3 id="saldoBank" class="text-3xl font-black text-blue-600 z-10">Rp 0</h3>
                        <p class="text-[10px] text-gray-400 mt-2 z-10 font-medium">Dana dari QRIS + Hasil Setoran Tunai</p>
                    </div>

                    <div class="p-6 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-md border border-gray-700 hover:border-orange-500 flex flex-col justify-between relative overflow-hidden group cursor-pointer hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                        <div class="absolute -right-4 -bottom-4 opacity-[0.20] text-orange-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                            <i class="fa-solid fa-wallet text-9xl"></i>
                        </div>

                        <div class="z-10">
                            <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-orange-400 inline-block group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-300"></i>
                                Kas Belum Disetor
                            </p>
                            <h3 id="kasBelumDisetor" class="text-3xl font-black text-white mb-1">Rp 0</h3>
                            <p class="text-[10px] text-gray-400">Uang fisik di laci kasir (CASH/COD)</p>
                        </div>
                        <button type="button" id="btnTriggerSetor" class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 z-10 relative">
                            <i class="fa-solid fa-money-bill-transfer"></i> Setorkan ke Bank
                        </button>
                    </div>
                </div>

                <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
                    <div class="flex flex-wrap gap-2">
                        <button data-status="disiapkan" class="status-filter-btn active flex-1 px-4 py-2.5 rounded-xl font-semibold text-center text-sm border-2">
                            Disiapkan <span id="badge-disiapkan" class="ml-2 bg-white/90 text-gray-800 rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">0</span>
                        </button>
                        <button data-status="dimasak" class="status-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold text-center text-sm border-2">
                            Dimasak <span id="badge-dimasak" class="ml-2 bg-white/90 text-gray-800 rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">0</span>
                        </button>
                        <button data-status="dikirim" class="status-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold text-center text-sm border-2">
                            Dikirim <span id="badge-dikirim" class="ml-2 bg-white/90 text-gray-800 rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">0</span>
                        </button>
                        <button data-status="sampai" class="status-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold text-center text-sm border-2">
                            Sampai <span id="badge-sampai" class="ml-2 bg-white/90 text-gray-800 rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">0</span>
                        </button>
                        <button data-status="selesai" class="status-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold text-center text-sm border-2">
                            Selesai <span id="badge-selesai" class="ml-2 bg-white/90 text-gray-800 rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">0</span>
                        </button>
                    </div>
                </div>

                <div id="ordersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                    <div class="col-span-full text-center text-gray-400 py-16 flex flex-col items-center">
                        <i class="fa-solid fa-inbox text-5xl mb-4 opacity-50"></i>
                        <p class="font-medium">Memuat pesanan...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalSetorTunai" class="fixed inset-0 z-[1000] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" id="setorOverlay"></div>
        
        <div class="bg-white rounded-3xl shadow-2xl z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col overflow-hidden border border-white/20" id="modalSetorContent">
            
            <div class="bg-emerald-600 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white opacity-10 blur-xl"></div>
                <div class="absolute bottom-0 right-10 w-16 h-16 rounded-full bg-white opacity-10 blur-lg"></div>
                
                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-inner">
                            <i class="fa-solid fa-building-columns text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold tracking-wide">Setor ke Bank</h3>
                            <p class="text-emerald-50 text-xs font-medium mt-0.5">Pindahkan kas fisik ke rekening</p>
                        </div>
                    </div>
                    <button id="btnBatalSetorX" class="text-white/70 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-full w-8 h-8 flex items-center justify-center active:scale-90"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-emerald-50/70 rounded-2xl p-4 mb-6 border border-emerald-100 flex items-center justify-between group hover:bg-emerald-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-wallet text-sm"></i>
                        </div>
                        <span class="text-sm font-bold text-gray-600">Kas Tersedia</span>
                    </div>
                    <span class="font-black text-emerald-700 text-lg tracking-tight" id="displayMaksimalKas">Rp 0</span>
                </div>

                <div class="mb-8">
                    <label class="block text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2 pl-1">Nominal yang disetor</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-extrabold text-xl group-focus-within:text-emerald-500 transition-colors">Rp</span>
                        </div>
                        <input type="number" id="inputNominalSetor" 
                               class="w-full bg-gray-50 border-2 border-gray-200 rounded-2xl py-3 pl-12 pr-4 focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 font-black text-2xl text-gray-800 transition-all shadow-sm placeholder:text-gray-300 placeholder:font-medium" 
                               placeholder="0">
                    </div>
                    <p id="errorSetor" class="text-rose-500 text-xs mt-2 hidden font-bold flex items-center gap-1.5 pl-1">
                        <i class="fa-solid fa-circle-exclamation"></i> <span>Nominal tidak valid!</span>
                    </p>
                </div>

                <div class="flex gap-3 w-full">
                    <button id="btnBatalSetor" class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-600 py-3 rounded-xl font-bold text-center transition-all active:scale-95">Batal</button>
                    <button id="btnKonfirmasiSetor" class="w-2/3 bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-bold text-center shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Setorkan
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                <a href="process/logout.php" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-center flex items-center justify-center transition shadow-md">Iya, Logout</a>
            </div>
        </div>
    </div>

    <div id="arsipModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="arsipOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="arsipModalBox">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-5 shadow-inner">
                <i class="fa-solid fa-box-archive text-3xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Arsipkan Pesanan?</h3>
            <p class="text-gray-500 mb-8 text-sm">Pesanan ini akan dipindahkan ke halaman Data Arsip Pesanan.</p>
            <div class="flex gap-4 w-full">
                <button id="btnCancelArsip" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-xl font-bold text-center transition">Batal</button>
                <button id="btnConfirmArsip" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-center transition shadow-md">Ya, Arsipkan</button>
            </div>
        </div>
    </div>

    <script src="js/toast.js"></script>
    <script src="js/dashboard-admin.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/sidebar-admin.js?v=<?= time() ?>"></script>
</body>

</html>
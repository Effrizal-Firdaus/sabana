<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin']['peran'] !== 'admin') {
    header('Location: ../user/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ulasan Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .rating-star { color: #facc15; }
        /* Progress bar animasi */
        .progress-bar-fill { transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">
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
                    <a href="kelola_menu.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-utensils w-5"></i> Kelola Menu</a>
                    <a href="pesanan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-receipt w-5"></i>
                        <span>Pesanan</span>
                        <span id="pesananBadge" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 hidden">0</span>
                    </a>
                    <a href="pengguna.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-users w-5"></i> Pengguna</a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-file-alt w-5"></i> Laporan</a>
                    <a href="masukan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2"><i class="fa-solid fa-message w-5"></i> Masukan</a>
                    <a href="ulasan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 bg-emerald-600 text-white shadow-lg border-l-4 border-emerald-300"><i class="fa-solid fa-star w-5"></i> Ulasan</a>
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

        <!-- MAIN CONTENT -->
        <div class="flex-1 ml-80 p-8">
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight"><i class="fa-solid fa-star mr-2 text-yellow-500"></i> Ulasan Pelanggan</h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau tingkat kepuasan pelanggan secara real-time.</p>
                </div>
            </div>

            <!-- POINT 1: RINGKASAN ANALITIK -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Rata-rata Rating -->
                <div class="flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 pb-6 md:pb-0">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Rata-rata Rating</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-gray-800" id="avgRatingScore">0.0</span>
                        <span class="text-lg font-bold text-gray-400">/ 5</span>
                    </div>
                    <div class="flex rating-star text-2xl mt-2 mb-1" id="avgRatingStars">
                        <i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full mt-2">Dari <span id="totalUlasan">0</span> Ulasan</span>
                </div>

                <!-- Distribusi Bar Chart -->
                <div class="md:col-span-2 flex flex-col justify-center gap-3 px-2" id="ratingDistribution">
                    <!-- Progress Bar (Diisi otomatis oleh JS) -->
                    <div class="text-center text-gray-400 text-sm"><i class="fa-solid fa-circle-notch fa-spin"></i> Menghitung data analitik...</div>
                </div>
            </div>

            <!-- POINT 2 & 4: KONTROL PENCARIAN, FILTER, DAN URUTKAN -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center z-20 relative">
                <!-- Search Bar -->
                <div class="relative w-full md:w-1/3">
                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" id="searchUlasan" placeholder="Cari nama pelanggan atau ID Pesanan..." class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all font-medium placeholder:font-normal">
                </div>

                <!-- Dropdown Sort & Filter -->
                <div class="flex gap-3 w-full md:w-auto">
                    <div class="relative group">
                        <i class="fa-solid fa-utensils absolute left-3 top-3.5 text-gray-400 text-sm z-10"></i>
                        <select id="filterMenuUlasan" class="w-full md:w-48 appearance-none bg-gray-50 border border-gray-200 text-sm font-semibold text-gray-600 rounded-xl py-3 pl-9 pr-10 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer transition-all">
                            <option value="semua">Semua Menu</option>
                            <!-- Pilihan menu di-generate otomatis oleh JS berdasarkan data yang ada -->
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-400 text-xs pointer-events-none"></i>
                    </div>

                    <div class="relative group">
                        <i class="fa-solid fa-arrow-down-a-z absolute left-3 top-3.5 text-gray-400 text-sm z-10"></i>
                        <select id="sortUlasan" class="w-full md:w-48 appearance-none bg-gray-50 border border-gray-200 text-sm font-semibold text-gray-600 rounded-xl py-3 pl-9 pr-10 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer transition-all">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                            <option value="tertinggi">Rating Tertinggi</option>
                            <option value="terendah">Rating Terendah</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- CONTAINER ULASAN -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div id="ulasanContainer" class="divide-y divide-gray-50">
                    <!-- Data disuntikkan dari JS -->
                    <div class="p-16 text-center text-gray-400">
                        <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-3"></i>
                        <p class="font-medium">Memuat ulasan pelanggan...</p>
                    </div>
                </div>
            </div>

            <!-- POINT 4: PAGINATION (KONTROL HALAMAN) -->
            <div class="flex justify-between items-center bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100" id="paginationContainer">
                <p class="text-sm font-medium text-gray-500" id="pageInfo">Menampilkan 0 dari 0 Ulasan</p>
                <div class="flex gap-2">
                    <button id="btnPrevPage" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                    </button>
                    <button id="btnNextPage" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Next <i class="fa-solid fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL LOGOUT -->
    <div id="logoutModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="logoutOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="logoutModalBox">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-5"><i class="fa-solid fa-arrow-right-from-bracket text-3xl text-emerald-600"></i></div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-500 mb-8 text-sm">Apakah Anda yakin ingin keluar dari sesi ini?</p>
            <div class="flex gap-4 w-full">
                <button id="btnCancelLogout" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-xl font-bold transition">Tidak</button>
                <a href="process/logout.php" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-center transition shadow-md">Iya, Logout</a>
            </div>
        </div>
    </div>

    <script src="js/toast.js"></script>
    <!-- Menghubungkan File JS Baru -->
    <script src="js/ulasan.js?v=<?= time() ?>"></script>
    <script src="js/notifications.js"></script>
    <script src="js/sidebar-admin.js?v=<?= time() ?>"></script>
</body>
</html>
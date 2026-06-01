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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu</title>
    <link rel="icon" href="../../img/admin_sabana.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/kelola_menu.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
                    <a href="kelola_menu.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 bg-emerald-600 text-white shadow-lg shadow-emerald-600/40 border-l-4 border-emerald-300 font-semibold">
                        <i class="fa-solid fa-utensils w-5"></i><span>Kelola Menu</span>
                    </a>
                    <a href="pesanan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-receipt w-5"></i>
                        <span>Pesanan</span>
                        <span id="pesananBadge" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 hidden">0</span>
                    </a>
                    <a href="pengguna.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-users w-5"></i><span>Pengguna</span>
                    </a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-file-alt w-5"></i><span>Laporan</span>
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

        <!-- Main Content -->
        <div class="flex-1 ml-80 bg-slate-50 min-h-screen">
            <header class="sticky top-0 z-40 px-10 pt-6 pb-4 bg-slate-50/90 backdrop-blur-md border-b border-gray-200/50">
                <div class="flex justify-between items-center bg-white p-4 pl-5 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600/20 to-emerald-600/5 flex items-center justify-center border border-emerald-600/10">
                            <i class="fa-solid fa-utensils text-emerald-600 text-xl shadow-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-800 to-gray-600 tracking-tight">Manajemen Menu</h1>
                            <p class="text-xs font-medium text-gray-400 mt-0.5">Tambah, ubah harga, dan atur ketersediaan menu produk Sabana.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 pr-2">
                        <button class="relative w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors border border-gray-200 focus:outline-none shadow-sm active:scale-95">
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
                <!-- Action Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 w-full">
                    <div class="relative w-full sm:max-w-md group">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-emerald-600 transition-colors duration-300"></i>
                        <input type="text" id="searchMenu" placeholder="Cari nama menu yang anda inginkan..." class="w-full pl-11 pr-4 py-3 bg-white border-2 border-transparent hover:border-emerald-600/50 focus:border-emerald-600 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-600/10 shadow-sm hover:shadow-md transition-all duration-300 text-sm text-gray-800">
                    </div>
                    <button id="btnTambahMenu" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold transition-all duration-200 active:scale-95 shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus text-sm"></i> Tambah Menu Baru
                    </button>
                </div>

                <!-- Filter Kategori -->
                <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
                    <div class="flex flex-wrap gap-2">
                        <button data-category="semua" class="menu-filter-btn active flex-1 px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 text-center text-sm border-2">Semua Menu</button>
                        <button data-category="Reguler" class="menu-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 text-center text-sm border-2">Reguler</button>
                        <button data-category="Tambahan" class="menu-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 text-center text-sm border-2">Tambahan</button>
                        <button data-category="Paket" class="menu-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 text-center text-sm border-2">Paket</button>
                        <button data-category="Paket Combo" class="menu-filter-btn flex-1 px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 text-center text-sm border-2">Combo</button>
                    </div>
                </div>

                <!-- Grid Menu -->
                <div id="menuContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full"></div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL TAMBAH / EDIT MENU ================= -->
    <div id="menuModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="menuModalOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-lg transform scale-95 opacity-0 transition-all duration-300" id="menuModalBox">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-xl font-extrabold text-gray-800">Tambah Menu Baru</h3>
                <button id="btnCloseMenuModal" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form id="menuForm" enctype="multipart/form-data">
                <input type="hidden" id="menuId">
                <input type="hidden" id="gambarLama" value="">

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Menu</label>
                    <input type="text" id="menuNama" required placeholder="Contoh: Ayam Geprek Spesial" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 text-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                        <select id="menuKategori" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 text-sm cursor-pointer">
                            <option value="Reguler">Reguler</option>
                            <option value="Tambahan">Tambahan</option>
                            <option value="Paket">Paket</option>
                            <option value="Paket Combo">Paket Combo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Harga (Rp)</label>
                        <input type="number" id="menuHarga" required placeholder="Contoh: 15000" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Stok (Pcs)</label>
                        <input type="number" id="menuStok" required placeholder="Contoh: 50" min="0" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 text-sm font-bold text-gray-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi / Rincian Menu</label>
                    <textarea id="menuDeskripsi" rows="3" placeholder="Contoh: 1 Ayam Dada + Nasi + Es Teh Manis" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 text-sm resize-none"></textarea>
                </div>

                <!-- Input Gambar -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gambar Menu</label>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 rounded-lg bg-gray-100 overflow-hidden border border-gray-200 flex items-center justify-center">
                            <img id="previewGambar" src="../../img/default.png" class="w-full h-full object-cover" onerror="this.src='../../img/Logo_Sabana.png'">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="menuGambar" accept="image/*" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                            <p class="text-xs text-gray-400 mt-1">* Kosongkan jika tidak ingin mengubah gambar</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" id="btnBatalMenu" class="flex-1 modal-btn-cancel py-3 rounded-xl font-bold text-sm">Batal</button>
                    <button type="submit" class="flex-1 modal-btn-confirm py-3 rounded-xl font-bold text-sm">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="deleteModalOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 flex flex-col items-center text-center" id="deleteModalBox">
            <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mb-5 shadow-inner">
                <i class="fa-solid fa-trash-can text-2xl text-rose-600"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Hapus Menu?</h3>
            <p class="text-gray-500 mb-8 text-sm">Menu yang dihapus tidak dapat dikembalikan ke sistem kasir.</p>
            <div class="flex gap-4 w-full">
                <button id="btnCancelDelete" class="flex-1 modal-btn-cancel py-3 rounded-xl font-bold">Batal</button>
                <button id="btnConfirmDelete" class="flex-1 bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 text-white py-3 rounded-xl font-bold active:scale-95 shadow-md shadow-red-500/20">Hapus</button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL LOGOUT ================= -->
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

    <script src="js/kelola-menu.js"></script>
    <script src="js/toast.js"></script>
    <script src="js/notifications.js"></script>
    <script>
        // Logika Logout Modal
        document.getElementById('btnTriggerLogout')?.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = document.getElementById('logoutModal');
            const box = document.getElementById('logoutModalBox');
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        });
        document.getElementById('btnCancelLogout')?.addEventListener('click', () => {
            const modal = document.getElementById('logoutModal');
            const box = document.getElementById('logoutModalBox');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        });
    </script>
    <script src="js/sidebar-admin.js?v=<?= time() ?>"></script>
</body>

</html>
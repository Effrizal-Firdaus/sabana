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
    <title>Manajemen Pengguna</title>
    <link rel="icon" href="../../img/admin_sabana.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/pengguna.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex min-h-screen">
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
                        <i class="fa-solid fa-utensils w-5"></i> Kelola Menu
                    </a>
                    <a href="pesanan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-receipt w-5"></i>
                        <span>Pesanan</span>
                        <span id="pesananBadge" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 hidden">0</span>
                    </a>
                    <a href="pengguna.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 bg-emerald-600 text-white shadow-lg border-l-4 border-emerald-300 font-semibold">
                        <i class="fa-solid fa-users w-5"></i> Pengguna
                    </a>
                    <a href="laporan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-file-alt w-5"></i> Laporan
                    </a>
                    <a href="masukan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-message w-5"></i><span>Masukan</span>
                    </a>
                    <a href="ulasan.php" class="nav-item-admin flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 text-gray-300 hover:bg-white/10 hover:text-white hover:translate-x-2">
                        <i class="fa-solid fa-star w-5"></i> Ulasan
                    </a>
                    <div class="mt-8 pt-4 border-t border-gray-700 w-full">
                        <a href="#" id="btnTriggerLogout" class="flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition-all duration-300 shadow-md hover:shadow-rose-500/30 active:scale-95 w-full">
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
                            <i class="fa-solid fa-users text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-800 to-gray-600">Manajemen Pengguna</h1>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">Kelola akun admin dan pelanggan</p>
                        </div>
                    </div>
                    <button id="btnTambahUser" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-emerald-500/30 flex items-center gap-2 transition active:scale-95">
                        <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
                    </button>
                </div>
            </header>

            <div class="p-10">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Pengguna</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none" id="statTotalUsers">0</h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl"><i class="fa-solid fa-user-tag"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Pelanggan Aktif</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none" id="statTotalPelanggan">0</h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-xl"><i class="fa-solid fa-user-shield"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Admin</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none" id="statTotalAdmin">0</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                        <div class="relative w-full md:w-1/3">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchUser" placeholder="Cari nama atau email..." class="w-full pl-11 pr-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all text-sm font-medium">
                        </div>
                        <div class="w-full md:w-auto">
                            <select id="filterRole" class="w-full md:w-48 appearance-none border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer transition-all">
                                <option value="semua">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="pelanggan">Pelanggan</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full bg-white text-left">
                            <thead class="bg-slate-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-5 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Nama & Email</th>
                                    <th class="px-5 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-5 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Terakhir Login</th>
                                    <th class="px-5 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-gray-400 font-medium"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memuat data pengguna...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center mt-6" id="paginationContainer">
                        <p class="text-sm font-medium text-gray-500" id="pageInfo">Menampilkan 0 dari 0 Pengguna</p>
                        <div class="flex gap-2">
                            <button id="btnPrevPage" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"><i class="fa-solid fa-chevron-left"></i> Prev</button>
                            <button id="btnNextPage" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed">Next <i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="userModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="userModalOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-6 z-10 w-full max-w-md transform scale-95 opacity-0 transition-all duration-300" id="userModalBox">
            <div class="flex justify-between items-center mb-4">
                <h3 id="userModalTitle" class="text-xl font-bold text-gray-800">Tambah Pengguna</h3>
                <button id="btnCloseUserModal" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form id="userForm">
                <input type="hidden" id="userId">
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="userNama" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-600 outline-none">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" id="userEmail" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-600 outline-none">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <select id="userRole" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-600 outline-none">
                        <option value="pelanggan">Pelanggan</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="passwordField" class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" id="userPassword" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-600 outline-none">
                    <p class="text-xs text-gray-400 mt-1 font-medium">* Kosongkan jika tidak ingin mengubah (untuk edit)</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="btnCancelUser" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition">Batal</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold shadow-md shadow-emerald-500/30 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteUserModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="deleteUserOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 text-center" id="deleteUserBox">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fa-solid fa-trash-can text-3xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Hapus Pengguna?</h3>
            <p class="text-gray-500 mb-8 text-sm">Data pengguna ini akan dihapus secara permanen dari sistem.</p>
            <div class="flex gap-3">
                <button id="btnCancelDeleteUser" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition">Batal</button>
                <button id="btnConfirmDeleteUser" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold shadow-md transition">Hapus</button>
            </div>
        </div>
    </div>

    <div id="resetPasswordModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="resetPasswordOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 text-center" id="resetPasswordBox">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fa-solid fa-key text-3xl text-yellow-600"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2">Reset Password</h3>
            <p class="text-gray-500 mb-6 text-sm">Password akan diubah menjadi <span class="font-mono bg-gray-100 font-bold px-2 py-1 rounded text-gray-800">12345678</span></p>
            <div class="flex gap-3">
                <button id="btnCancelReset" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition">Batal</button>
                <button id="btnConfirmReset" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold transition shadow-md">Ya, Reset</button>
            </div>
        </div>
    </div>

    <div id="blokirUserModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="blokirUserOverlay"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 z-10 w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 text-center" id="blokirUserBox">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner" id="blokirIconContainer">
                <i class="fa-solid fa-ban text-3xl text-orange-600" id="blokirIcon"></i>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 mb-2" id="blokirTitle">Blokir Pengguna?</h3>
            <p class="text-gray-500 mb-8 text-sm" id="blokirDesc">Pengguna ini tidak akan bisa login ke dalam aplikasi lagi.</p>
            <div class="flex gap-3">
                <button id="btnCancelBlokir" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition">Batal</button>
                <button id="btnConfirmBlokir" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-xl font-bold shadow-md transition">Ya, Blokir</button>
            </div>
        </div>
    </div>

    <div id="logoutModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
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

    <script src="js/toast.js"></script>
    <script src="js/pengguna.js?v=<?= time() ?>"></script>
    <script src="js/notifications.js"></script>
    <script>
        // Logout modal handler
        const btnTrigger = document.getElementById('btnTriggerLogout');
        const logoutModal = document.getElementById('logoutModal');
        const logoutBox = document.getElementById('logoutModalBox');
        const logoutCancel = document.getElementById('btnCancelLogout');
        const logoutOverlay = document.getElementById('logoutOverlay');

        function showLogout() {
            logoutModal.classList.remove('hidden');
            setTimeout(() => {
                logoutBox.classList.remove('scale-95', 'opacity-0');
                logoutBox.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        function hideLogout() {
            logoutBox.classList.remove('scale-100', 'opacity-100');
            logoutBox.classList.add('scale-95', 'opacity-0');
            setTimeout(() => logoutModal.classList.add('hidden'), 300);
        }
        if (btnTrigger) btnTrigger.addEventListener('click', (e) => { e.preventDefault(); showLogout(); });
        if (logoutCancel) logoutCancel.addEventListener('click', hideLogout);
        if (logoutOverlay) logoutOverlay.addEventListener('click', hideLogout);
    </script>
    <script src="js/sidebar-admin.js?v=<?= time() ?>"></script>
</body>

</html>
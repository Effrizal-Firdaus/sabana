<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.html');
    exit;
}
$nama_user = htmlspecialchars($_SESSION['user']['nama']);
$email_user = htmlspecialchars($_SESSION['user']['email']);
$user_id = $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard</title>
    <link rel="icon" href="../../img/Logo_Sabana1.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sabanaRed: "#e11d48",
                        sabanaGold: "#ffcc00",
                        sabanaDark: "#1f2937"
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden font-poppins" data-user-id="<?= $user_id ?>" data-user-email="<?= $email_user ?>">
    <script>
        window.userData = {
            id: <?= json_encode($user_id) ?>,
            nama: <?= json_encode($nama_user) ?>,
            email: <?= json_encode($email_user) ?>
        };
    </script>
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="sidebar text-white">
            <div class="p-6">
                <div class="flex justify-center mb-8 pb-4 border-b border-gray-700">
                    <button id="backButton" class="back-button bg-[#fbbf24] hover:bg-[#f59e0b] text-slate-900 px-5 py-2.5 rounded-full font-semibold shadow-lg flex items-center gap-2 transition duration-200 ease-out">
                        <a href="javascript:history.back()" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                    </button>
                </div>
                <nav class="flex flex-col gap-2">
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300" data-menu="edit-profil">
                        <i class="fa-regular fa-user w-5"></i> Edit Profil
                    </a>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300" data-menu="pesanan-saya">
                        <i class="fa-solid fa-clipboard-list w-5"></i> Pesanan Saya
                    </a>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300" data-menu="riwayat">
                        <i class="fa-regular fa-clock w-5"></i> Riwayat Pesanan
                    </a>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300" data-menu="bantuan">
                        <i class="fa-regular fa-circle-question w-5"></i> Bantuan & Laporan
                    </a>
                    <a href="#" id="logoutBtn" class="flex items-center gap-3 px-4 py-3 mt-8 rounded-xl transition-all duration-300 bg-sabanaGold hover:bg-yellow-500 text-sabanaDark font-bold shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-right-from-bracket w-5"></i> Logout
                    </a>
                </nav>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content flex-1 ml-[280px]">
            <!-- Navbar identik dengan menu_utama.php -->
            <nav class="bg-white shadow-md sticky top-0 z-[9999]">
                <div class="container mx-auto px-6 py-4 flex justify-between items-center relative">
                    <div class="flex items-center">
                        <img src="../../img/Logo_Sabana.png" alt="Sabana Logo" class="h-20 w-auto" />
                        <span class="ml-4 text-3xl font-extrabold text-sabanaRed"><span class="text-sabanaGold">.</span></span>
                    </div>
                    <button class="hamburger-menu md:hidden relative z-[1050]" id="hamburgerMenu">
                        <span></span><span></span><span></span>
                    </button>
                    <div class="nav-container md:flex md:items-center" id="navContainer">
                        <a href="../menu_utama.php#home" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Home</a>
                        <a href="../menu_utama.php#keunggulan" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Keunggulan</a>
                        <div class="relative md:mx-4 flex items-center h-full pt-1">
                            <div id="menuDropdownBtn" class="flex items-center cursor-pointer">
                                <a href="../menu_utama.php#menu" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 hover:text-sabanaRed">Menu</a>
                                <div id="menuArrow" class="w-5 h-5 ml-1 pb-1 relative flex items-center justify-center transition-transform duration-300 cursor-pointer">
                                    <svg class="w-full h-full absolute text-yellow-900 opacity-20 filter blur-sm mt-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <svg class="w-full h-full relative" viewBox="0 0 24 24">
                                        <defs>
                                            <linearGradient id="crystalGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#f87171" />
                                                <stop offset="50%" stop-color="#fb923c" />
                                                <stop offset="100%" stop-color="#facc15" />
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#crystalGradient)" stroke="#e90707" stroke-width="0.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="menuDropdownContent" class="absolute left-0 top-full mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-2xl transition-all duration-300 z-[9999] flex flex-col overflow-hidden hidden">
                                <a href="../menu_kategori.php?kategori=reguler" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../../img/Ayam_dada.png" class="w-6 h-6 mr-3 object-contain" />Menu Reguler</a>
                                <a href="../menu_kategori.php?kategori=tambahan" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../../img/burger_ayam.png" class="w-6 h-6 mr-3 object-contain" />Menu Tambahan</a>
                                <a href="../menu_kategori.php?kategori=paket" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../../img/paket4.png" class="w-6 h-6 mr-3 object-contain" />Menu Paket</a>
                                <a href="../menu_kategori.php?kategori=combo" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"><img src="../../img/combo3.png" class="w-6 h-6 mr-3 object-contain" />Menu Combo</a>
                            </div>
                        </div>
                        <a href="../menu_utama.php#lokasi" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Lokasi</a>
                        <a href="#" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-4 px-6 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center active:bg-[#7f1d1d]">
                            <i class="fa-solid fa-user mr-2"></i> <?= $nama_user ?>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- KONTEN DINAMIS DASHBOARD -->
            <div class="p-6" id="dynamicContent">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900">Selamat datang, <?= $nama_user ?>!</h2>
                    <p class="text-gray-600 mt-1">Kelola profil, lihat pesanan, dan riwayat belanja Anda di sini.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-md text-center hover:shadow-lg transition">
                        <i class="fa-solid fa-cart-shopping text-3xl text-sabanaRed mb-3"></i>
                        <h3 id="statPesanan" class="text-2xl font-bold">0</h3>
                        <p class="text-gray-500">Pesanan Aktif</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center hover:shadow-lg transition">
                        <i class="fa-regular fa-clock text-3xl text-sabanaRed mb-3"></i>
                        <h3 id="statRiwayat" class="text-2xl font-bold">0</h3>
                        <p class="text-gray-500">Riwayat</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md text-center hover:shadow-lg transition">
                        <i class="fa-regular fa-star text-3xl text-sabanaRed mb-3"></i>
                        <h3 id="statRating" class="text-2xl font-bold">0</h3>
                        <p class="text-gray-500">Ulasan</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <form id="logoutForm" method="POST" action="logout.php" style="display:none;"></form>

    <script src="../js/dashboard-user.js"></script>
</body>

</html>
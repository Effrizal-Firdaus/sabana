<?php
session_start();
include_once __DIR__ . '/../server/koneksi.php';
$sudah_login = isset($_SESSION['user']['id']);
$nama_user   = $sudah_login ? htmlspecialchars($_SESSION['user']['nama']) : '';
$user_id     = $sudah_login ? htmlspecialchars($_SESSION['user']['id']) : '';
?>
<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Keranjang Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/menu_utama.css" />
    <link rel="stylesheet" href="css/keranjang.css" />
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

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden" data-logged-in="<?= $sudah_login ? 'true' : 'false' ?>" data-user-id="<?= $user_id ?>">
    <!-- NAVBAR (sama seperti sebelumnya) -->
    <nav class="bg-white shadow-md sticky top-0 z-[9999]">
        <!-- ... isi navbar sama seperti kode Anda ... -->
        <div class="container mx-auto px-6 py-4 flex justify-between items-center relative">
            <div class="flex items-center">
                <img src="../img/Logo_Sabana.png" alt="Sabana Logo" class="h-20 w-auto" />
                <span class="ml-4 text-3xl font-extrabold text-sabanaRed"><span class="text-sabanaGold">.</span></span>
            </div>
            <button class="hamburger-menu md:hidden relative z-[1050]" id="hamburgerMenu"><span></span><span></span><span></span></button>
            <div class="nav-container md:flex md:items-center" id="navContainer">
                <a href="menu_utama.php#home" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Home</a>
                <a href="menu_utama.php#keunggulan" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Keunggulan</a>
                <div class="relative group md:mx-4 flex items-center h-full pt-1">
                    <a href="menu_utama.php#menu" class="nav-link text-sabanaRed border-b-[3px] border-sabanaRed md:text-xl font-bold pb-1 transition duration-300">Menu</a>
                    <div class="w-5 h-5 ml-1 pb-1 relative flex items-center justify-center transform group-hover:translate-y-1 transition-transform duration-300 cursor-pointer">
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
                            <path fill="url(#crystalGradient)" stroke="#b45309" stroke-width="0.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="absolute left-0 top-full mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[9999] flex flex-col overflow-hidden">
                        <a href="menu_kategori.php?kategori=reguler" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/Ayam_dada.png" class="w-6 h-6 mr-3 object-contain" />Menu Reguler</a>
                        <a href="menu_kategori.php?kategori=tambahan" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/burger_ayam.png" class="w-6 h-6 mr-3 object-contain" />Menu Tambahan</a>
                        <a href="menu_kategori.php?kategori=paket" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/paket4.png" class="w-6 h-6 mr-3 object-contain" />Menu Paket</a>
                        <a href="menu_kategori.php?kategori=combo" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"><img src="../img/combo3.png" class="w-6 h-6 mr-3 object-contain" />Menu Combo</a>
                    </div>
                </div>
                <a href="menu_utama.php#lokasi" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Lokasi</a>
                <?php if ($sudah_login): ?>
                    <a href="process/dashboard.php" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-4 px-6 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center active:bg-[#7f1d1d]"><i class="fa-solid fa-user mr-2"></i> <?= $nama_user ?></a>
                    <!-- Tombol logout dihapus, karena sudah ada di dalam dashboard -->
                <?php else: ?>
                    <a href="login.html" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-6 px-8 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center"><i class="fa-solid fa-right-to-bracket mr-3"></i> Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- KONTEN KERANJANG -->
    <section class="py-16 bg-red-50 min-h-screen">
        <div class="container mx-auto px-6">
            <!-- Tombol kembali dan judul -->
            <div class="flex justify-between items-center mb-8">
                <button id="backButton" class="back-button bg-sabanaRed text-white px-5 py-2.5 rounded-full font-semibold shadow-md flex items-center gap-2 hover:bg-red-700 transition duration-200">
                    <a href="javascript:history.back()" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                </button>
                <h1 class="text-4xl font-black text-gray-900 tracking-wide uppercase text-center flex-1">Keranjang Belanja</h1>
                <div class="w-20"></div>
            </div>
            <!-- Container untuk daftar keranjang (WAJIB ADA) -->
            <div id="cartContainer" class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-center py-8 text-gray-500">Memuat keranjang...</div>
            </div>
        </div>
    </section>

    <!-- FOOTER / LOKASI -->
    <footer id="lokasi" class="bg-[#1a1a1a] text-gray-300 pt-16 pb-8 font-sans scroll-mt-24">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Bagian 1: Logo & Sosmed -->
            <div class="space-y-6">
                <img src="../img/Logo_Sabana.png" alt="Logo Sabana" class="h-16 w-auto" />
                <div>
                    <h3 class="text-2xl font-bold text-yellow-400 inline-block">Sabana</h3>
                    <span class="ml-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wider">Fried Chicken</span>
                </div>
                <div class="flex space-x-4 pt-2">
                    <a href="https://www.tiktok.com/@sabanaku" target="_blank" class="social-icon"><img src="../img/tiktok.png" alt="TikTok" class="w-10 h-10 object-contain" /></a>
                    <a href="https://www.instagram.com/sabanaku/" target="_blank" class="social-icon"><img src="../img/instagram.png" alt="Instagram" class="w-10 h-10 object-contain" /></a>
                    <a href="https://www.youtube.com/@sabanaku" target="_blank" class="social-icon"><img src="../img/youtube.png" alt="YouTube" class="w-10 h-10 object-contain" /></a>
                    <a href="https://sabana.co.id/" target="_blank" class="social-icon"><img src="../img/logo_sabana1.png" alt="Website" class="w-10 h-10 object-contain" /></a>
                </div>
            </div>

            <!-- Bagian 2: Alamat Lengkap (Titik Alamat) -->
            <div>
                <div class="border-l-4 border-yellow-400 pl-4 mb-6">
                    <h4 class="text-lg font-bold text-yellow-400 uppercase tracking-wider">📍 Alamat Kantor Pusat</h4>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-map-marker-alt text-yellow-400 mt-1"></i>
                    <p class="text-sm leading-relaxed">
                        <strong class="text-white">Sabana Group</strong><br>
                        Jl. Jatimakmur No.36, Kelurahan Jatimakmur,<br>
                        Kecamatan Pondok Gede, Kota Bekasi 17413<br>
                    </p>
                </div>
            </div>

            <!-- Bagian 3: Kontak -->
            <div>
                <div class="border-l-4 border-yellow-400 pl-4 mb-6">
                    <h4 class="text-lg font-bold text-yellow-400 uppercase tracking-wider">📞 Hubungi Kami</h4>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <i class="fab fa-whatsapp text-yellow-400 text-lg"></i>
                        <p class="text-sm">WhatsApp: <a href="https://wa.me/628882269963" class="hover:text-yellow-400 transition">0888-2269-963</a></p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-yellow-400"></i>
                        <p class="text-sm">Email: <a href="mailto:info@sabana.co.id" class="hover:text-yellow-400 transition">info@sabana.co.id</a></p>
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Peta & Tombol Buka Maps (Tanpa API Key) -->
            <div>
                <div class="border-l-4 border-yellow-400 pl-4 mb-4">
                    <h4 class="text-lg font-bold text-yellow-400 uppercase tracking-tight leading-tight">🗺️ PETA INTERAKTIF</h4>
                </div>
                <!-- Iframe Google Maps (embed) menggunakan alamat lengkap – tetap gratis meski tanpa API Key untuk embed statis -->
                <div class="rounded-lg overflow-hidden border border-gray-700 h-48 mb-3">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.986618580227!2d106.92805467571343!3d-6.281487861483861!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698d77a758cbd9%3A0x11104b365225d356!2sSabana%20Group!5e0!3m2!1sid!2sid!4v1709123456789!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe><iframe
                        title="Peta Lokasi Sabana Group"
                        src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY_HERE&q=Jl.+Jatimakmur+No.36+Kel.Jatimakmur+Pondok+Gede+Bekasi"
                        width="100%" height="100%" style="border:0" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
                <!-- Tautan alternatif: Buka Google Maps TANPA API KEY (URL publik) -->
                <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Jatimakmur+No.36+Kel.Jatimakmur+Pondok+Gede+Bekasi"
                    target="_blank"
                    class="text-xs text-yellow-400 hover:underline flex items-center justify-center gap-1 mt-1">
                    <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                </a>
            </div>
        </div>

        <div class="w-full border-t border-gray-800 mt-12 pt-8">
            <p class="text-center text-gray-500 text-sm">© 2024 Sabana Group (PT Sarana Berkah Niaga)</p>
        </div>
    </footer>

    <!-- MODAL PERINGATAN BELUM LOGIN -->
    <div id="loginWarningModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-sm w-full mx-4 p-6 shadow-2xl transform transition-all scale-95 opacity-0" id="modalContent">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4"><i class="fa-solid fa-triangle-exclamation text-sabanaRed text-3xl"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Login!</h3>
                <p class="text-gray-600 mb-6">Anda belum login. Silakan login terlebih dahulu untuk melihat keranjang belanja.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button id="modalCancelBtn" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all duration-200">Tidak</button>
                    <button id="modalLoginBtn" class="px-6 py-2.5 bg-sabanaRed text-white rounded-xl font-semibold hover:bg-red-700 hover:scale-105 transition-all duration-200 shadow-md">Login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/global.js"></script>
    <script src="js/keranjang.js"></script>
</body>

</html>
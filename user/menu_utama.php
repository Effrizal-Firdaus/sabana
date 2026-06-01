<?php
session_start();
$sudah_login = isset($_SESSION['user']);
$nama_user   = $sudah_login ? htmlspecialchars($_SESSION['user']['nama']) : '';
?>
<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sabana Fried Chicken</title>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- External CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/menu_utama.css">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sabanaRed: "#e11d48",
                        sabanaRedHover: "#be123c",
                        sabanaGold: "#ffcc00",
                        sabanaDark: "#1f2937",
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden" data-logged-in="<?= $sudah_login ? 'true' : 'false' ?>">
    <!-- NAVIGATION -->
    <nav class="bg-white shadow-md sticky top-0 z-[9999]">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center relative">
            <div class="flex items-center">
                <img src="../img/Logo_Sabana.png" alt="Sabana Logo" class="h-20 w-auto" />
                <span class="ml-4 text-3xl font-extrabold text-sabanaRed"><span class="text-sabanaGold">.</span></span>
            </div>

            <button class="hamburger-menu md:hidden relative z-[1050]" id="hamburgerMenu">
                <span></span><span></span><span></span>
            </button>

            <div class="nav-container md:flex md:items-center" id="navContainer">
                <a href="#home" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Home</a>
                <a href="#keunggulan" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Keunggulan</a>

                <div class="relative md:mx-4 flex items-center h-full pt-1">
                    <div id="menuDropdownBtn" class="flex items-center cursor-pointer">
                        <a href="#menu" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 hover:text-sabanaRed">Menu</a>
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
                        <a href="menu_kategori.php?kategori=reguler" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/Ayam_dada.png" class="w-6 h-6 mr-3 object-contain" />Menu Reguler</a>
                        <a href="menu_kategori.php?kategori=tambahan" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/burger_ayam.png" class="w-6 h-6 mr-3 object-contain" />Menu Tambahan</a>
                        <a href="menu_kategori.php?kategori=paket" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 border-b border-gray-50 transition-colors"><img src="../img/paket4.png" class="w-6 h-6 mr-3 object-contain" />Menu Paket</a>
                        <a href="menu_kategori.php?kategori=combo" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"><img src="../img/combo3.png" class="w-6 h-6 mr-3 object-contain" />Menu Combo</a>
                    </div>
                </div>

                <a href="#lokasi" class="nav-link text-gray-700 md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Lokasi</a>

                <?php if ($sudah_login): ?>
                    <a href="process/dashboard.php" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-4 px-6 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center active:bg-[#7f1d1d]">
                        <i class="fa-solid fa-user mr-2"></i> <?= $nama_user ?>
                    </a>
                    <!-- Tidak ada tombol logout di sini -->
                <?php else: ?>
                    <a href="login.html" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-6 px-8 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center">
                        <i class="fa-solid fa-right-to-bracket mr-3"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- PROMO SLIDER -->
    <section class="bg-white">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide relative"><img src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?q=80&w=1200&auto=format&fit=crop" alt="Promo 1" />
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-white p-6">
                        <h2 class="text-4xl font-black mb-2 uppercase tracking-tighter text-center">Paket Gajian Sabana!</h2>
                        <p class="text-xl mb-6 text-center">Beli Paket dan Combo. Nikmati Sekarang!</p>
                    </div>
                </div>
                <div class="swiper-slide relative"><img src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=1600&auto=format&fit=crop" alt="Promo 2" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-white p-6">
                        <h2 class="text-4xl font-black mb-2 uppercase tracking-tighter text-center">Menu Baru Sabana!</h2>
                        <p class="text-xl mb-6 text-center">Nikmati kelezatan bumbu rahasia di setiap gigitan.</p>
                    </div>
                </div>
                <div class="swiper-slide relative"><img src="https://images.unsplash.com/photo-1527477396000-e27163b481c2?q=80&w=1600&auto=format&fit=crop" alt="Promo 3" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-white p-6">
                        <h2 class="text-4xl font-black mb-2 uppercase tracking-tighter text-center">Hemat Bareng Keluarga</h2>
                        <p class="text-xl mb-6 text-center">Dengan varian combo extra. Pesan Sekarang!</p><a href="menu_kategori.php" class="btn-pesan-sekarang bg-sabanaRed text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg inline-block">Pesan Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next text-sabanaGold"></div>
            <div class="swiper-button-prev text-sabanaGold"></div>
        </div>
    </section>

    <!-- HERO SECTION -->
    <section id="home" class="relative bg-white py-20 md:py-32 overflow-hidden border-b border-gray-100">
        <div class="absolute top-0 left-0 w-64 h-64 bg-sabanaGold/10 blur-[100px] rounded-full -ml-20 -mt-20"></div>
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <div class="space-y-8 text-center md:text-left">
                <h1 class="text-4xl md:text-6xl font-black text-gray-900 leading-tight">Crispy di Luar,<br />Juicy di Dalam.<br /><span class="text-sabanaRed">Pilihan Keluarga Indonesia.</span></h1>
                <p class="text-xl text-gray-600 max-w-lg mx-auto md:mx-0 leading-relaxed font-medium">Nikmati kelezatan ayam goreng autentik Sabana dengan bumbu meresap sempurna.<span class="text-sabanaRed font-bold">Halal, Higienis,</span> dan pastinya bikin nagih!</p>
                <div class="flex justify-center md:justify-start pt-4"><a href="#menu" class="px-10 py-4 bg-sabanaRed text-white rounded-xl font-bold text-lg hover:bg-red-700 hover:scale-105 hover:shadow-2xl transition-all duration-300 shadow-xl flex items-center group">Lihat Menu Kami<i class="fa-solid fa-arrow-right ml-3 group-hover:translate-x-2 transition-transform"></i></a></div>
            </div>
            <div class="flex justify-end items-center group w-full relative">
                <div class="absolute inset-0 bg-sabanaRed/5 blur-[80px] rounded-full scale-75 group-hover:bg-sabanaRed/10 transition-all"></div>
                <img src="../img/Ayam_goreng.profil.png" alt="Ayam Goreng" class="w-full h-auto max-w-md lg:max-w-lg object-contain object-right drop-shadow-[0_35px_35px_rgba(225,29,72,0.3)] transform transition-all duration-700 ease-in-out group-hover:scale-110 group-hover:-rotate-2 -mr-10 md:-mr-24 lg:-mr-32 relative z-10" onerror="this.src='https://placehold.co/500x500/fef2f2/e11d48?text=Ayam+Profil'" />
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN SECTION -->
    <section id="keunggulan" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-black text-center tracking-wide uppercase mb-12">Mengapa Memilih Sabana?</h2>
            <div class="grid md:grid-cols-3 gap-8 text-center">
                
                <!-- Kartu 1: 100% Halal -->
                <div class="bg-gray-50 p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-red-400 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group cursor-pointer relative overflow-hidden">
                    <!-- Watermark Latar Belakang -->
                    <div class="absolute -right-4 -bottom-4 opacity-5 text-red-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                        <i class="fas fa-check-circle text-9xl"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-sabanaRed text-3xl mb-6 group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 group-hover:-rotate-12 transition-all duration-300 shadow-sm">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-red-600 transition-colors duration-300">100% Halal</h3>
                        <p class="text-gray-600">Proses penyembelihan dan pengolahan sesuai syariat Islam dan standar SOP ketat.</p>
                    </div>
                </div>
                
                <!-- Kartu 2: Harga Ekonomis -->
                <div class="bg-gray-50 p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-yellow-400 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group cursor-pointer relative overflow-hidden">
                    <!-- Watermark Latar Belakang -->
                    <div class="absolute -right-4 -bottom-4 opacity-5 text-yellow-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                        <i class="fas fa-tag text-9xl"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 text-sabanaGold text-3xl mb-6 group-hover:bg-yellow-400 group-hover:text-white group-hover:scale-110 group-hover:rotate-12 transition-all duration-300 shadow-sm">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-yellow-600 transition-colors duration-300">Harga Ekonomis</h3>
                        <p class="text-gray-600">Rasa bintang lima, harga kaki lima. Pas di kantong untuk seluruh keluarga.</p>
                    </div>
                </div>
                
                <!-- Kartu 3: 3.000+ Gerai -->
                <div class="bg-gray-50 p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-red-400 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group cursor-pointer relative overflow-hidden">
                    <!-- Watermark Latar Belakang -->
                    <div class="absolute -right-4 -bottom-4 opacity-5 text-red-500 group-hover:scale-110 group-hover:-translate-y-3 transition-transform duration-500">
                        <i class="fas fa-store text-9xl"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-sabanaRed text-3xl mb-6 group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 group-hover:-rotate-12 transition-all duration-300 shadow-sm">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-red-600 transition-colors duration-300">3.000+ Gerai</h3>
                        <p class="text-gray-600">Mudah ditemukan di mana saja, tersebar luas di Pulau Jawa dan Sumatra.</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- KUMPULAN MENU -->
    <section id="menu" class="py-16 bg-red-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-gray-900 tracking-wide uppercase">Pilihan Menu Kami</h2>
                <p class="text-gray-600 mt-2">Pesan menu favorit Anda dan keluarga sekarang juga!</p>
            </div>
            <div id="menu-wrapper">
                <div id="group-0" class="mb-16 menu-group">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
                        
                        <div class="group flex flex-col items-center cursor-pointer w-full">
                            <div class="w-full aspect-square relative flex items-center justify-center bg-transparent overflow-visible mb-4 md:mb-6">
                                <img src="../img/Ayam_dada.png" alt="Ayam Goreng Dada" 
                                     class="w-full h-full object-contain drop-shadow-md transition-all duration-500 ease-out group-hover:!scale-[1.2] group-hover:!-translate-y-4 group-hover:drop-shadow-2xl group-hover:rotate-2" 
                                     style="max-width: 100%; max-height: 100%;" />
                            </div>
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out group-hover:!-translate-y-2">
                                <h3 class="text-lg md:text-xl font-black text-gray-900 leading-tight tracking-tight">Ayam Goreng Dada</h3>
                            </div>
                        </div>

                        <div class="group flex flex-col items-center cursor-pointer w-full">
                            <div class="w-full aspect-square relative flex items-center justify-center bg-transparent overflow-visible mb-4 md:mb-6">
                                <img src="../img/burger_ayam.png" alt="Burger Ayam" 
                                     class="w-full h-full object-contain drop-shadow-md transition-all duration-500 ease-out group-hover:!scale-[1.2] group-hover:!-translate-y-4 group-hover:drop-shadow-2xl group-hover:-rotate-2" 
                                     style="max-width: 100%; max-height: 100%;" />
                            </div>
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out group-hover:!-translate-y-2">
                                <h3 class="text-lg md:text-xl font-black text-gray-900 leading-tight tracking-tight">Burger Ayam</h3>
                            </div>
                        </div>

                        <div class="group flex flex-col items-center cursor-pointer w-full">
                            <div class="w-full aspect-square relative flex items-center justify-center bg-transparent overflow-visible mb-4 md:mb-6">
                                <img src="../img/paket3.png" alt="Paket Ayam Geprek" 
                                     class="w-full h-full object-contain drop-shadow-md transition-all duration-500 ease-out group-hover:!scale-[1.2] group-hover:!-translate-y-4 group-hover:drop-shadow-2xl group-hover:rotate-2" 
                                     style="max-width: 100%; max-height: 100%;" />
                            </div>
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out group-hover:!-translate-y-2">
                                <h3 class="text-lg md:text-xl font-black text-gray-900 leading-tight tracking-tight">Ayam Sambal Geprek + Nasi + Es teh</h3>
                            </div>
                        </div>

                        <div class="group flex flex-col items-center cursor-pointer w-full">
                            <div class="w-full aspect-square relative flex items-center justify-center bg-transparent overflow-visible mb-4 md:mb-6">
                                <img src="../img/combo3.png" alt="7 Pcs Paha Bawah" 
                                     class="w-full h-full object-contain drop-shadow-md transition-all duration-500 ease-out group-hover:!scale-[1.2] group-hover:!-translate-y-4 group-hover:drop-shadow-2xl group-hover:-rotate-2" 
                                     style="max-width: 100%; max-height: 100%;" />
                            </div>
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out group-hover:!-translate-y-2">
                                <h3 class="text-lg md:text-xl font-black text-gray-900 leading-tight tracking-tight">7 Pcs Paha Bawah</h3>
                            </div>
                        </div>

                    </div>
                </div>
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

    <!-- Modal Peringatan Belum Login -->
    <div id="loginWarningModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-sm w-full mx-4 p-6 shadow-2xl transform transition-all scale-95 opacity-0" id="modalContent">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4"><i class="fa-solid fa-triangle-exclamation text-sabanaRed text-3xl"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Login!</h3>
                <p class="text-gray-600 mb-6">Anda belum login. Silakan login terlebih dahulu untuk melakukan pemesanan.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button id="modalCancelBtn" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all duration-200">Tidak</button>
                    <button id="modalLoginBtn" class="px-6 py-2.5 bg-sabanaRed text-white rounded-xl font-semibold hover:bg-red-700 hover:scale-105 transition-all duration-200 shadow-md">Login</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/global.js"></script>
    <script src="js/menu_utama.js"></script>
</body>

</html>
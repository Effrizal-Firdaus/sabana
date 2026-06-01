<?php
session_start();

// Sertakan koneksi database dari folder server
include_once __DIR__ . '/../server/koneksi.php';

$sudah_login = isset($_SESSION['user']['id']);
$nama_user   = $sudah_login ? htmlspecialchars($_SESSION['user']['nama']) : '';

$kategori_pilihan = isset($_GET['kategori']) ? strtolower($_GET['kategori']) : 'reguler';
?>
<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Kategori Menu</title>
    <link rel="icon" href="../../img/Logo_Sabana1.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/menu_kategori.css" />
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
    <style>
        .menu-link {
            text-decoration: none;
            display: block;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden" data-logged-in="<?= $sudah_login ? 'true' : 'false' ?>">
    <!-- NAVBAR (dengan dropdown toggle ASLI milik user) -->
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
                <a href="menu_utama.php#home" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Home</a>
                <a href="menu_utama.php#keunggulan" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Keunggulan</a>
                <div class="relative md:mx-4 flex items-center h-full pt-1">
                    <div id="menuDropdownBtn" class="flex items-center cursor-pointer">
                        <a href="menu_utama.php#menu" class="nav-link text-sabanaRed border-b-[3px] border-sabanaRed md:text-xl font-bold pb-1 transition duration-300">Menu</a>
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
                                <path fill="url(#crystalGradient)" stroke="#b45309" stroke-width="0.5" d="M19 9l-7 7-7-7"></path>
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
                <a href="menu_utama.php#lokasi" class="nav-link text-gray-700 hover:text-sabanaRed md:text-xl font-bold pb-1 transition duration-300 md:mx-4">Lokasi</a>
                <?php if ($sudah_login): ?>
                    <a href="process/dashboard.php" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-4 px-6 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center active:bg-[#7f1d1d]">
                        <i class="fa-solid fa-user mr-2"></i> <?= $nama_user ?>
                    </a>
                <?php else: ?>
                    <a href="login.html" class="mx-6 md:mx-0 mt-6 md:mt-0 md:ml-6 px-8 py-3 bg-sabanaRed text-white rounded-full text-xl font-bold hover:bg-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 shadow-lg flex items-center justify-center active:bg-[#7f1d1d]">
                        <i class="fa-solid fa-right-to-bracket mr-3"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="relative bg-white overflow-hidden py-12 md:py-20 border-b border-gray-100">
        <div class="container mx-auto px-6 relative z-10">
            <a href="menu_utama.php#home" class="inline-flex items-center gap-2 bg-sabanaRed text-white font-semibold px-5 py-2.5 rounded-full shadow-md hover:bg-red-700 hover:shadow-lg hover:scale-105 transition-all duration-300 mb-8 group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-0.5 transition"></i> <span>HOME</span>
            </a>
            <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16 lg:gap-20">
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 leading-tight">
                        Nikmati menu pilihan terbaik<br />
                        <span class="text-sabanaRed">dari Sabana Fried Chicken</span>
                    </h1>
                    <p class="text-lg text-gray-600 mt-4 max-w-lg mx-auto md:mx-0">Rasakan kelezatan ayam goreng dengan bumbu spesial yang meresap hingga ke tulang.</p>
                </div>
                <div class="flex-1 flex justify-end md:justify-end">
                    <img src="../img/gambar_navbar.png" alt="Menu Pilihan" class="w-full max-w-sm md:max-w-md lg:max-w-lg object-contain drop-shadow-2xl hover:scale-105 transition duration-500 rounded-lg" onerror="this.src='../img/Logo_Sabana.png'">
                </div>
            </div>
        </div>
    </div>

    <!-- KONTEN MENU -->
    <section id="menu" class="py-16 bg-red-50 min-h-screen">
        <?php if ($kategori_pilihan === 'reguler'): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-1 w-full">
                <div class="relative flex items-center justify-center mb-12 w-full">
                    <h2 class="text-3xl font-black text-gray-900 tracking-wide uppercase">MENU REGULER</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
                    <?php
                    $items = [
                        ['id' => 1, 'img' => 'Ayam_dada.png', 'name' => 'Ayam Goreng Dada', 'price' => 11000, 'desc' => 'Daging ayam tanpa tulang yang tebal dan juicy, dibalut tepung krispi dengan bumbu rahasia Sabana. Setiap gigitan memberikan ledakan gurih yang bikin nagih.'],
                        ['id' => 2, 'img' => 'Ayam_Pahaatas.png', 'name' => 'Ayam Goreng Paha Atas', 'price' => 11000, 'desc' => 'Paha ayam bagian atas dengan tekstur super lembut dan bumbu meresap hingga ke tulang. Rasanya gurih alami, pas di lidah.'],
                        ['id' => 3, 'img' => 'paha_bawah.png', 'name' => 'Ayam Goreng Paha Bawah', 'price' => 9000, 'desc' => 'Paha bawah yang renyah di luar, empuk di dalam. Dagingnya kecil tapi sarat rasa. Sempurna untuk camilan atau lauk praktis.'],
                        ['id' => 4, 'img' => 'sayap.png', 'name' => 'Ayam Goreng Sayap', 'price' => 9000, 'desc' => 'Sayap ayam dengan kulit yang sangat renyah dan bumbu meresap. Cocok untuk Anda yang suka menikmati ayam sambil ngobrol santai.']
                    ];
                    foreach ($items as $item):
                        $stok_sementara = isset($item['stok']) ? (int)$item['stok'] : 8;
                        $isHabisKategori = ($stok_sementara <= 0);
                    ?>
                        <a href="pesan.php?menu=<?= urlencode(json_encode($item)) ?>" class="group flex flex-col cursor-pointer w-full no-underline h-full">
                            
                            <div class="w-full aspect-square relative flex items-center justify-center mb-4 md:mb-6">
                                <?php if (isset($item['badge'])): ?>
                                    <div class="absolute top-2 right-2 md:top-4 md:right-4 bg-sabanaGold text-sabanaDark text-[10px] md:text-xs font-extrabold px-3 py-1 rounded-full z-30 drop-shadow-sm"><?= $item['badge'] ?></div>
                                <?php endif; ?>

                                <!-- EFEK ZOOM 30% KEMBALI (scale-[1.2] & translate-y-4) -->
                                <img src="../img/<?= $item['img'] ?>" alt="<?= $item['name'] ?>" 
                                     class="w-full h-full object-contain transition-all duration-500 ease-out <?= $isHabisKategori ? 'grayscale opacity-60' : 'group-hover:scale-[1.2] group-hover:-translate-y-4 group-hover:drop-shadow-2xl' ?>" />

                                <?php if ($isHabisKategori): ?>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none transition-all duration-300">
                                        <span class="text-red-600 font-black text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(255,255,255,1)] tracking-widest uppercase">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out <?= $isHabisKategori ? '' : 'group-hover:-translate-y-2' ?>">
                                <h3 class="text-lg md:text-xl font-black <?= $isHabisKategori ? 'text-red-500' : 'text-gray-900' ?> leading-tight tracking-tight"><?= $item['name'] ?></h3>
                            </div>

                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($kategori_pilihan === 'tambahan'): ?>
            <div class="max-w-7xl mx-auto px-4 lg:px-1 w-full">
                <div class="relative flex items-center justify-center mb-16 w-full">
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-wide uppercase">MENU TAMBAHAN</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 md:gap-10">
                    <?php
                    $items = [
                        ['id' => 5, 'img' => 'burger_ayam.png', 'name' => 'Burger Ayam', 'price' => 12000, 'desc' => 'Roti empuk dengan isian ayam crispy, saus spesial Sabana, selada segar, dan timun.'],
                        ['id' => 6, 'img' => 'rice_box.png', 'name' => 'Rice Box', 'price' => 12000, 'desc' => 'Nasi kotak praktis dengan lauk ayam crispy dan sambal.'],
                        ['id' => 7, 'img' => 'kentang.png', 'name' => 'Kentang Goreng', 'price' => 8000, 'desc' => 'Kentang import yang digoreng hingga keemasan.'],
                        ['id' => 8, 'img' => 'nasi.png', 'name' => 'Nasi Putih', 'price' => 4000, 'desc' => 'Nasi putih pulen, hangat, dan wangi.'],
                        ['id' => 9, 'img' => 'kulit.png', 'name' => 'Kulit Krispy', 'price' => 5000, 'desc' => 'Kulit ayam yang digoreng super renyah tanpa minyak berlebih.'],
                        ['id' => 10, 'img' => 'strips.png', 'name' => 'Chicken Strips', 'price' => 4000, 'desc' => 'Strips daging ayam fillet tanpa tulang.'],
                        ['id' => 11, 'img' => 'bakso.png', 'name' => 'Bakso Goreng', 'price' => 4000, 'desc' => 'Bakso sapi yang digoreng hingga berkulit krispi.'],
                        ['id' => 12, 'img' => 'roll.png', 'name' => 'Chicken Roll', 'price' => 4000, 'desc' => 'Roti gulung lembut berisi ayam crispy, mayones, dan sayuran segar.'],
                        ['id' => 13, 'img' => 'esteh.png', 'name' => 'Es Teh', 'price' => 3000, 'desc' => 'Teh melati asli dengan gula aren.']
                    ];
                    foreach ($items as $item):
                        $stok_sementara = isset($item['stok']) ? (int)$item['stok'] : 8;
                        $isHabisKategori = ($stok_sementara <= 0);
                    ?>
                        <a href="pesan.php?menu=<?= urlencode(json_encode($item)) ?>" class="group flex flex-col cursor-pointer w-full no-underline h-full">
                            <div class="w-full aspect-square relative flex items-center justify-center mb-4 md:mb-6">
                                <?php if (isset($item['badge'])): ?>
                                    <div class="absolute top-2 right-2 md:top-4 md:right-4 bg-sabanaGold text-sabanaDark text-[10px] md:text-xs font-extrabold px-3 py-1 rounded-full z-30 drop-shadow-sm"><?= $item['badge'] ?></div>
                                <?php endif; ?>

                                <img src="../img/<?= $item['img'] ?>" alt="<?= $item['name'] ?>" 
                                     class="w-full h-full object-contain transition-all duration-500 ease-out <?= $isHabisKategori ? 'grayscale opacity-60' : 'group-hover:scale-[1.2] group-hover:-translate-y-4 group-hover:drop-shadow-2xl' ?>" />
                                
                                <?php if ($isHabisKategori): ?>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none transition-all duration-300">
                                        <span class="text-red-600 font-black text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(255,255,255,1)] tracking-widest uppercase">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out <?= $isHabisKategori ? '' : 'group-hover:-translate-y-2' ?>">
                                <h3 class="text-lg md:text-xl font-black <?= $isHabisKategori ? 'text-red-500' : 'text-gray-900' ?> leading-tight tracking-tight"><?= $item['name'] ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($kategori_pilihan === 'paket'): ?>
            <div class="max-w-7xl mx-auto px-4 lg:px-1 w-full">
                <div class="relative flex items-center justify-center mb-16 w-full">
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-wide uppercase">MENU PAKET</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
                    <?php
                    $items = [
                        ['id' => 14, 'img' => 'paket1.png', 'name' => 'Ayam Dada + Nasi + Es Teh', 'price' => 20000, 'badge' => 'PAKET', 'desc' => 'Paket hemat untuk makan siang.'],
                        ['id' => 15, 'img' => 'paket2.png', 'name' => 'Ayam Sayap + Nasi + Es Teh', 'price' => 18000, 'badge' => 'PAKET', 'desc' => 'Nikmati 2 potong sayap ayam renyah, nasi pulen.'],
                        ['id' => 16, 'img' => 'paket3.png', 'name' => 'Ayam Sambal Geprek + Nasi + Es teh', 'price' => 25000, 'badge' => 'PAKET', 'desc' => 'Ayam geprek dengan sambal bawang pedas.'],
                        ['id' => 17, 'img' => 'paket4.png', 'name' => 'Ayam Sambal Ijo + Nasi + Es Teh', 'price' => 25000, 'badge' => 'PAKET', 'desc' => 'Ayam goreng dengan sambal ijo khas Padang.']
                    ];
                    foreach ($items as $item):
                        $stok_sementara = isset($item['stok']) ? (int)$item['stok'] : 8;
                        $isHabisKategori = ($stok_sementara <= 0);
                    ?>
                        <a href="pesan.php?menu=<?= urlencode(json_encode($item)) ?>" class="group flex flex-col cursor-pointer w-full no-underline h-full">
                            <div class="w-full aspect-square relative flex items-center justify-center mb-4 md:mb-6">
                                <?php if (isset($item['badge'])): ?>
                                    <div class="absolute top-2 right-2 md:top-4 md:right-4 bg-sabanaGold text-sabanaDark text-[10px] md:text-xs font-extrabold px-3 py-1 rounded-full z-30 drop-shadow-sm"><?= $item['badge'] ?></div>
                                <?php endif; ?>

                                <img src="../img/<?= $item['img'] ?>" alt="<?= $item['name'] ?>" 
                                     class="w-full h-full object-contain transition-all duration-500 ease-out <?= $isHabisKategori ? 'grayscale opacity-60' : 'group-hover:scale-[1.2] group-hover:-translate-y-4 group-hover:drop-shadow-2xl' ?>" />
                                
                                <?php if ($isHabisKategori): ?>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none transition-all duration-300">
                                        <span class="text-red-600 font-black text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(255,255,255,1)] tracking-widest uppercase">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out <?= $isHabisKategori ? '' : 'group-hover:-translate-y-2' ?>">
                                <h3 class="text-lg md:text-xl font-black <?= $isHabisKategori ? 'text-red-500' : 'text-gray-900' ?> leading-tight tracking-tight"><?= $item['name'] ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($kategori_pilihan === 'combo'): ?>
            <div class="max-w-7xl mx-auto px-4 lg:px-1 w-full">
                <div class="relative flex items-center justify-center mb-16 w-full">
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-wide uppercase">MENU COMBO</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 md:gap-10">
                    <?php
                    $items = [
                        ['id' => 18, 'img' => 'combo1.png', 'name' => '3 Pcs Paha Bawah', 'price' => 25000, 'badge' => 'HEMAT', 'desc' => '3 potong paha bawah ayam goreng krispi.'],
                        ['id' => 19, 'img' => 'combo2.png', 'name' => '5 Pcs Paha Bawah', 'price' => 41000, 'badge' => 'HEMAT', 'desc' => '5 potong paha bawah dengan rasa meresap.'],
                        ['id' => 20, 'img' => 'combo3.png', 'name' => '7 Pcs Paha Bawah', 'price' => 56000, 'badge' => 'HEMAT', 'desc' => '7 potong paha bawah super hemat.']
                    ];
                    foreach ($items as $item):
                        $stok_sementara = isset($item['stok']) ? (int)$item['stok'] : 8;
                        $isHabisKategori = ($stok_sementara <= 0);
                    ?>
                        <a href="pesan.php?menu=<?= urlencode(json_encode($item)) ?>" class="group flex flex-col cursor-pointer w-full no-underline h-full">
                            <div class="w-full aspect-square relative flex items-center justify-center mb-4 md:mb-6">
                                <?php if (isset($item['badge'])): ?>
                                    <div class="absolute top-2 right-2 md:top-4 md:right-4 bg-sabanaGold text-sabanaDark text-[10px] md:text-xs font-extrabold px-3 py-1 rounded-full z-30 drop-shadow-sm"><?= $item['badge'] ?></div>
                                <?php endif; ?>

                                <img src="../img/<?= $item['img'] ?>" alt="<?= $item['name'] ?>" 
                                     class="w-full h-full object-contain transition-all duration-500 ease-out <?= $isHabisKategori ? 'grayscale opacity-60' : 'group-hover:scale-[1.2] group-hover:-translate-y-4 group-hover:drop-shadow-2xl' ?>" />
                                
                                <?php if ($isHabisKategori): ?>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none transition-all duration-300">
                                        <span class="text-red-600 font-black text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(255,255,255,1)] tracking-widest uppercase">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out <?= $isHabisKategori ? '' : 'group-hover:-translate-y-2' ?>">
                                <h3 class="text-lg md:text-xl font-black <?= $isHabisKategori ? 'text-red-500' : 'text-gray-900' ?> leading-tight tracking-tight"><?= $item['name'] ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
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

            <!-- Bagian 4: Peta & Tombol Buka Maps -->
            <div>
                <div class="border-l-4 border-yellow-400 pl-4 mb-4">
                    <h4 class="text-lg font-bold text-yellow-400 uppercase tracking-tight leading-tight">🗺️ PETA INTERAKTIF</h4>
                </div>
                <!-- Iframe Google Maps -->
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

    <script src="js/global.js"></script>
    <script src="js/menu_kategori.js"></script>
</body>

</html>
<?php
session_start();
include_once __DIR__ . '/../server/koneksi.php';
$sudah_login = isset($_SESSION['user']['id']);
$nama_user   = $sudah_login ? htmlspecialchars($_SESSION['user']['nama']) : '';
$user_id = $sudah_login ? htmlspecialchars($_SESSION['user']['id']) : '';

$menu_data_json = isset($_GET['menu']) ? $_GET['menu'] : '';
if (!$menu_data_json) {
    header('Location: menu_utama.php');
    exit;
}
$menu = json_decode(urldecode($menu_data_json), true);
if (!$menu || !isset($menu['name'])) {
    header('Location: menu_utama.php');
    exit;
}

function normalizeImagePath($img)
{
    return preg_replace('/\.(jpe?g)$/i', '.png', $img);
}

function resolveMenuImage($gambar, $nama = '')
{
    $mapping = [
        'Ayam Goreng Dada' => 'Ayam_dada.png',
        'Ayam Goreng Paha Atas' => 'Ayam_Pahaatas.png',
        'Ayam Goreng Paha Bawah' => 'paha_bawah.png',
        'Ayam Goreng Sayap' => 'sayap.png',
        'Burger Ayam' => 'burger_ayam.png',
        'Rice Box' => 'rice_box.png',
        'Kentang Goreng' => 'kentang.png',
        'Nasi Putih' => 'nasi.png',
        'Kulit Krispy' => 'kulit.png',
        'Chicken Strips' => 'strips.png',
        'Bakso Goreng' => 'bakso.png',
        'Chicken Roll' => 'roll.png',
        'Es Teh' => 'esteh.png',
        'Ayam Dada + Nasi + Es Teh' => 'paket1.png',
        'Ayam Sayap + Nasi + Es Teh' => 'paket2.png',
        'Ayam Sambal Geprek + Nasi + Es teh' => 'paket3.png',
        'Ayam Sambal Ijo + Nasi + Es Teh' => 'paket4.png',
        '3 Pcs Paha Bawah' => 'combo1.png',
        '5 Pcs Paha Bawah' => 'combo2.png',
        '7 Pcs Paha Bawah' => 'combo3.png',
    ];

    if ($nama && isset($mapping[$nama])) {
        return $mapping[$nama];
    }
    if ($gambar) {
        return normalizeImagePath(basename($gambar));
    }
    return 'default.png';
}

if (isset($menu['img'])) {
    $menu['img'] = resolveMenuImage($menu['img'], $menu['name'] ?? '');
}

$menuId = isset($menu['id']) ? intval($menu['id']) : null;
$menuName = trim($menu['name']);
$menuKategori = isset($menu['kategori']) ? trim($menu['kategori']) : '';
$foundMenu = false;

if ($menuId) {
    $stmt = $conn->prepare('SELECT id, nama_menu, stok, harga, gambar, kategori FROM menu WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $menuId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $menu['id'] = (int) $row['id'];
            $menu['name'] = $row['nama_menu'];
            $menu['price'] = (int) $row['harga'];
            $menu['img'] = resolveMenuImage($row['gambar'], $row['nama_menu']);
            $menu['kategori'] = $row['kategori'] ?: $menuKategori;
            $menu['stok'] = (int) $row['stok'];
            $foundMenu = true;
        }
        $stmt->close();
    }
}

if (!$foundMenu && $menuName !== '') {
    $stmt = $conn->prepare('SELECT id, nama_menu, stok, harga, gambar, kategori FROM menu WHERE nama_menu = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $menuName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $menu['id'] = (int) $row['id'];
            $menu['name'] = $row['nama_menu'];
            $menu['price'] = (int) $row['harga'];
            $menu['img'] = resolveMenuImage($row['gambar'], $row['nama_menu']);
            $menu['kategori'] = $row['kategori'] ?: $menuKategori;
            $menu['stok'] = (int) $row['stok'];
            $foundMenu = true;
        }
        $stmt->close();
    }
}

if (!$foundMenu && !isset($menu['stok'])) {
    $menu['stok'] = 8;
}

// Fungsi untuk menentukan kategori berdasarkan nama menu
function getKategori($nama)
{
    $reguler = ['Ayam Goreng Dada', 'Ayam Goreng Paha Atas', 'Ayam Goreng Paha Bawah', 'Ayam Goreng Sayap'];
    $tambahan = ['Burger Ayam', 'Rice Box', 'Kentang Goreng', 'Nasi Putih', 'Kulit Krispy', 'Chicken Strips', 'Bakso Goreng', 'Chicken Roll', 'Es Teh'];
    $paket = ['Ayam Dada + Nasi + Es Teh', 'Ayam Sayap + Nasi + Es Teh', 'Ayam Sambal Geprek + Nasi + Es teh', 'Ayam Sambal Ijo + Nasi + Es Teh'];
    $combo = ['3 Pcs Paha Bawah', '5 Pcs Paha Bawah', '7 Pcs Paha Bawah'];
    if (in_array($nama, $reguler)) return 'Reguler';
    if (in_array($nama, $tambahan)) return 'Tambahan';
    if (in_array($nama, $paket)) return 'Paket';
    if (in_array($nama, $combo)) return 'Combo';
    return 'Lainnya';
}

if (!isset($menu['kategori'])) $menu['kategori'] = getKategori($menu['name']);


// Deskripsi baru untuk semua menu
$deskripsi_baru = [
    'Ayam Goreng Dada' => 'Daging ayam tanpa tulang yang tebal dan juicy, dibalut tepung krispi dengan bumbu rahasia Sabana. Setiap gigitan memberikan ledakan gurih yang bikin nagih. Cocok untuk Anda yang suka daging padat dan mengenyangkan.',
    'Ayam Goreng Paha Atas' => 'Paha ayam bagian atas dengan tekstur super lembut dan bumbu meresap hingga ke tulang. Rasanya gurih alami, pas di lidah. Pilihan favorit bagi pecinta daging yang juicy dan mudah dipisahkan dari tulang.',
    'Ayam Goreng Paha Bawah' => 'Paha bawah yang renyah di luar, empuk di dalam. Dagingnya kecil tapi sarat rasa. Sempurna untuk camilan atau lauk praktis. Sensasi kriuk khas Sabana di setiap suapan.',
    'Ayam Goreng Sayap' => 'Sayap ayam dengan kulit yang sangat renyah dan bumbu meresap. Cocok untuk Anda yang suka menikmati ayam sambil ngobrol santai. Rasanya gurih, sedikit pedas (jika pesan sambal), dan bikin ketagihan.',
    'Burger Ayam' => 'Roti empuk dengan isian ayam crispy, saus spesial Sabana, selada segar, dan timun. Paduan gurih, creamy, dan renyah dalam satu gigitan. Lebih enak dari burger fast food terkenal!',
    'Rice Box' => 'Nasi kotak praktis dengan lauk ayam crispy dan sambal. Cocok untuk makan siang di kantor atau perjalanan. Hemat, mengenyangkan, dan tetap lezat.',
    'Kentang Goreng' => 'Kentang import yang digoreng hingga keemasan. Renyah di luar, lembut di dalam, dengan taburan bumbu spesial. Cocok sebagai pendamping ayam atau camilan sendiri.',
    'Nasi Putih' => 'Nasi putih pulen, hangat, dan wangi. Pendamping sempurna untuk semua menu ayam Sabana. Dijamin membuat lauk semakin istimewa.',
    'Kulit Krispy' => 'Kulit ayam yang digoreng super renyah tanpa minyak berlebih. Gurih, kriuk, dan ringan. Camilan favorit semua umur. Bisa juga ditabur di nasi untuk tekstur ekstra.',
    'Chicken Strips' => 'Strips daging ayam fillet tanpa tulang, dibalut tepung krispi. Bentuknya panjang dan mudah digigit. Cocok untuk anak-anak karena tidak repot dengan tulang. Disajikan dengan saus tomat atau mayones.',
    'Bakso Goreng' => 'Bakso sapi yang digoreng hingga berkulit krispi, kenyal di dalam. Aromanya harum, rasanya gurih. Bisa dinikmati langsung atau dicocol saus pedas.',
    'Chicken Roll' => 'Roti gulung lembut berisi ayam crispy, mayones, dan sayuran segar. Praktis untuk bekal atau makan di perjalanan. Rasa creamy dan gurih berpadu sempurna.',
    'Es Teh' => 'Teh melati asli dengan gula aren, disajikan dingin dengan es batu. Segar dan tidak terlalu manis. Pas untuk melepas dahaga setelah menikmati ayam goreng.',
    'Ayam Dada + Nasi + Es Teh' => 'Paket hemat untuk makan siang: ayam dada goreng crispy, nasi putih hangat, dan es teh manis. Kenyang, bergizi, dan ramah di kantong. Pilihan terbaik pekerja kantoran.',
    'Ayam Sayap + Nasi + Es Teh' => 'Nikmati 2 potong sayap ayam renyah, nasi pulen, dan es teh segar. Cocok untuk Anda yang suka bagian sayap dengan rasa meresap sempurna.',
    'Ayam Sambal Geprek + Nasi + Es teh' => 'Ayam geprek dengan sambal bawang pedas menggugah selera, ditambah nasi dan es teh. Pedasnya nampol, kriuknya mantap. Wajib coba untuk pecinta pedas!',
    'Ayam Sambal Ijo + Nasi + Es Teh' => 'Ayam goreng dengan sambal ijo khas Padang yang pedas dan segar. Sambal ijo buatan rumah dengan cabai hijau pilihan. Rasanya autentik dan menggoyang lidah.',
    '3 Pcs Paha Bawah' => '3 potong paha bawah ayam goreng krispi. Cocok untuk santap berdua dengan lauk tambahan nasi atau kentang. Hemat 15% dibanding beli satuan.',
    '5 Pcs Paha Bawah' => '5 potong paha bawah dengan rasa meresap hingga ke tulang. Pilihan keluarga kecil (3-4 orang). Lebih irit dan praktis untuk kumpul bareng.',
    '7 Pcs Paha Bawah' => '7 potong paha bawah super hemat. Ideal untuk pesta keluarga atau arisan. Dapatkan potongan harga spesial dan gratis satu es teh.'
];

if (isset($deskripsi_baru[$menu['name']])) {
    $menu['desc'] = $deskripsi_baru[$menu['name']];
}

// KALKULASI RATING GABUNGAN (BASELINE DINAMIS + REAL DATABASE)
// ======================================================================
// Data manipulasi khusus untuk memicu psikologi Social Proof pelanggan
$baseline_data = [
    'Ayam Goreng Dada' => ['rating' => 4.8, 'ulasan' => 2150],
    'Ayam Goreng Paha Atas' => ['rating' => 4.9, 'ulasan' => 1840],
    'Ayam Goreng Paha Bawah' => ['rating' => 4.7, 'ulasan' => 1520],
    'Ayam Goreng Sayap' => ['rating' => 4.6, 'ulasan' => 890],
    'Burger Ayam' => ['rating' => 4.6, 'ulasan' => 980],
    'Rice Box' => ['rating' => 4.5, 'ulasan' => 750],
    'Kentang Goreng' => ['rating' => 4.7, 'ulasan' => 1240],
    'Nasi Putih' => ['rating' => 4.9, 'ulasan' => 3100], 
    'Kulit Krispy' => ['rating' => 4.9, 'ulasan' => 1750], 
    'Chicken Strips' => ['rating' => 4.7, 'ulasan' => 620],
    'Bakso Goreng' => ['rating' => 4.6, 'ulasan' => 540],
    'Chicken Roll' => ['rating' => 4.5, 'ulasan' => 480],
    'Es Teh' => ['rating' => 4.8, 'ulasan' => 2800], 
    'Ayam Dada + Nasi + Es Teh' => ['rating' => 4.8, 'ulasan' => 1985],
    'Ayam Sayap + Nasi + Es Teh' => ['rating' => 4.6, 'ulasan' => 820],
    'Ayam Sambal Geprek + Nasi + Es teh' => ['rating' => 4.9, 'ulasan' => 2430], 
    'Ayam Sambal Ijo + Nasi + Es Teh' => ['rating' => 4.7, 'ulasan' => 1050],
    '3 Pcs Paha Bawah' => ['rating' => 4.8, 'ulasan' => 1650],
    '5 Pcs Paha Bawah' => ['rating' => 4.9, 'ulasan' => 1120],
    '7 Pcs Paha Bawah' => ['rating' => 4.8, 'ulasan' => 890]
];

// Tentukan rating dan ulasan berdasarkan nama menu, jika tidak ada di list gunakan default 450
$base_rating = isset($baseline_data[$menu['name']]) ? $baseline_data[$menu['name']]['rating'] : 4.6;
$base_ulasan = isset($baseline_data[$menu['name']]) ? $baseline_data[$menu['name']]['ulasan'] : 450;

$menu['rating'] = $base_rating;
$menu['ulasan'] = $base_ulasan;
$menu_id_for_rating = $menu['id'] ?? 0;

if ($menu_id_for_rating > 0) {
    // Ambil JUMLAH ulasan asli dan TOTAL KESELURUHAN BINTANG dari database
    $stmt_rating = $conn->prepare("SELECT COUNT(*) as real_ulasan, SUM(rating) as real_total_bintang FROM rating WHERE id_menu = ?");
    if ($stmt_rating) {
        $stmt_rating->bind_param('i', $menu_id_for_rating);
        $stmt_rating->execute();
        $res_rating = $stmt_rating->get_result();
        
        if ($row_rating = $res_rating->fetch_assoc()) {
            $real_ulasan = (int)$row_rating['real_ulasan'];
            $real_total_bintang = (int)$row_rating['real_total_bintang'];
            
            // Jika ada ulasan asli di database, gabungkan dengan baseline
            if ($real_ulasan > 0) {
                $total_semua_ulasan = $base_ulasan + $real_ulasan;
                $total_semua_poin = ($base_rating * $base_ulasan) + $real_total_bintang;
                
                $menu['ulasan'] = $total_semua_ulasan;
                $menu['rating'] = round($total_semua_poin / $total_semua_ulasan, 1);
            }
        }
        $stmt_rating->close();
    }
}

if (!isset($menu['stok'])) $menu['stok'] = 8;

// ======================================================================
// PENENTU STATUS HABIS
// ======================================================================
$isHabis = ((int)$menu['stok'] <= 0);
?>
<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Pesan Menu<?= htmlspecialchars($menu['name']) ?> - Sabana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="css/menu_utama.css" />
    <link rel="stylesheet" href="css/pesan.css" />
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

<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden" data-logged-in="<?= $sudah_login ? 'true' : 'false' ?>" data-user-id="<?= $user_id ?>">
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

    <section class="py-16 bg-red-50">
        <div class="pesan-container">
            <a href="javascript:history.back()" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali ke menu</a>
            <div class="detail-grid">
                
                <div class="product-image relative">
                    <img src="../img/<?= htmlspecialchars($menu['img']) ?>" alt="<?= htmlspecialchars($menu['name']) ?>" id="productMainImg" class="rounded-2xl shadow-sm transition-all duration-300" <?= $isHabis ? 'style="filter: grayscale(100%); opacity: 0.6;"' : '' ?>>
                    
                    <?php if($isHabis): ?>
                    <div class="absolute inset-0 z-10 flex items-center justify-center rounded-2xl pointer-events-none">
                        <span class="text-red-600 font-black text-4xl md:text-5xl tracking-widest uppercase" style="text-shadow: 0 0 12px rgba(255,255,255,1);">HABIS</span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="product-detail">
                    <h1 class="product-title"><?= htmlspecialchars($menu['name']) ?></h1>
                    <div class="product-price">Rp <?= number_format($menu['price'], 0, ',', '.') ?></div>
                    <div class="rating-section">
                        <div class="stars">
                            <?php $r = $menu['rating'];
                            for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($r)) echo '<i class="fas fa-star text-yellow-400"></i>';
                                elseif ($i - 0.5 <= $r) echo '<i class="fas fa-star-half-alt text-yellow-400"></i>';
                                else echo '<i class="far fa-star text-yellow-400"></i>'; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-value"><?= number_format($r, 1) ?></span>
                        <span class="rating-count">(<?= number_format($menu['ulasan']) ?> penilaian)</span>
                    </div>
                    <p class="product-description"><?= htmlspecialchars($menu['desc']) ?></p>
                    
                    <div class="quantity-section <?= $isHabis ? 'opacity-40 pointer-events-none' : '' ?>" style="display: block; width: 100%;">
                        <span class="quantity-label" style="display: block; margin-bottom: 0.5rem;">Jumlah:</span>
                        <div class="quantity-control">
                            <button type="button" id="decrementQty" class="qty-btn" <?= $isHabis ? 'disabled' : '' ?>>-</button>
                            <input type="number" id="orderQty" value="1" min="1" max="<?= $menu['stok'] ?>" class="qty-input" <?= $isHabis ? 'disabled' : '' ?>>
                            <button type="button" id="incrementQty" class="qty-btn" <?= $isHabis ? 'disabled' : '' ?>>+</button>
                        </div>
                    </div>
                    <div class="stock-info" style="display: block; width: 100%; margin: 1rem 0;">
                        <span class="stock-label">Stok Menu:</span>
                        <span id="stockValue" class="stock-value <?= $isHabis ? 'text-gray-500' : '' ?>"><?= $menu['stok'] ?></span>
                    </div>
                    <div id="pesanWarning" class="alert hidden" role="alert">
                        <span class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <div class="alert-text"></div>
                    </div>
                    
                    <?php if($isHabis): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-start gap-3 mt-4 mb-4">
                            <i class="fa-solid fa-circle-info mt-1"></i>
                            <p class="text-sm font-medium leading-relaxed">Mohon maaf, menu ini sedang tidak tersedia. Silakan cek kembali nanti atau pilih menu favorit lainnya.</p>
                        </div>
                        <button disabled class="order-btn !bg-red-600 !text-white !opacity-60 !cursor-not-allowed flex items-center justify-center gap-2 shadow-none" style="display: block; width: 100%;">
                            <i class="fa-solid fa-ban text-xl"></i> Maaf, Stok Habis
                        </button>
                    <?php else: ?>
                        <button id="orderNowBtn" class="order-btn" style="display: block; width: 100%;"><i class="fa-solid fa-cart-shopping"></i> Pesan Sekarang</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-wide uppercase text-center mb-12">Menu Rekomendasi Lainnya</h2>
            <div class="menu-rekomendasi">
                <!-- Grid dengan jarak yang lebih lega (gap-10) agar saat zoom tidak tabrakan -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
                    <?php
                    $rekom = [
                        ['id' => 1, 'img' => 'Ayam_dada.png', 'name' => 'Ayam Goreng Dada', 'price' => 11000, 'desc' => $deskripsi_baru['Ayam Goreng Dada'], 'rating' => 4.5, 'ulasan' => 56, 'stok' => 8, 'kategori' => 'Reguler'],
                        ['id' => 5, 'img' => 'burger_ayam.png', 'name' => 'Burger Ayam', 'price' => 12000, 'desc' => $deskripsi_baru['Burger Ayam'], 'rating' => 4.5, 'ulasan' => 56, 'stok' => 8, 'kategori' => 'Tambahan'],
                        ['id' => 14, 'img' => 'paket1.png', 'name' => 'Paket Dada + Nasi + Teh', 'price' => 20000, 'desc' => $deskripsi_baru['Ayam Dada + Nasi + Es Teh'], 'rating' => 4.5, 'ulasan' => 56, 'stok' => 8, 'kategori' => 'Paket', 'badge' => 'PAKET'],
                        ['id' => 18, 'img' => 'combo1.png', 'name' => 'Combo 3 Pcs Paha Bawah', 'price' => 25000, 'desc' => $deskripsi_baru['3 Pcs Paha Bawah'], 'rating' => 4.5, 'ulasan' => 56, 'stok' => 8, 'kategori' => 'Combo', 'badge' => 'HEMAT']
                    ];
                    foreach ($rekom as $r):
                        $itemData = $r;
                        $isHabisRekom = ((int)$r['stok'] <= 0);
                    ?>
                        <a href="pesan.php?menu=<?= urlencode(json_encode($itemData)) ?>" class="group flex flex-col cursor-pointer w-full no-underline h-full">
                            
                            <!-- WADAH GAMBAR TANPA KOTAK (Boxless) -->
                            <div class="w-full aspect-square relative flex items-center justify-center mb-4 md:mb-6 bg-transparent overflow-visible">
                                <?php if (isset($r['badge'])): ?>
                                    <div class="absolute top-0 right-0 md:top-2 md:right-2 bg-sabanaGold text-sabanaDark text-[10px] md:text-xs font-extrabold px-3 py-1 rounded-full z-30 drop-shadow-sm"><?= $r['badge'] ?></div>
                                <?php endif; ?>

                                <!-- EFEK ZOOM 30% DIPAKSAKAN MENGGUNAKAN !IMPORTANT -->
                                <img src="../img/<?= $r['img'] ?>" alt="<?= $r['name'] ?>" 
                                     class="w-full h-full object-contain drop-shadow-md transition-all duration-500 ease-out <?= $isHabisRekom ? 'grayscale opacity-60' : 'group-hover:!scale-[1.2] group-hover:!-translate-y-4 group-hover:drop-shadow-2xl' ?>" style="max-width: 100%; max-height: 100%;" />
                                
                                <?php if ($isHabisRekom): ?>
                                    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none transition-all duration-300">
                                        <span class="text-red-600 font-black text-xl md:text-2xl drop-shadow-[0_0_8px_rgba(255,255,255,1)] tracking-widest uppercase">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- TEKS NAMA MENU -->
                            <div class="w-full mt-auto text-center transition-transform duration-500 ease-out <?= $isHabisRekom ? '' : 'group-hover:!-translate-y-2' ?>">
                                <h3 class="text-lg md:text-xl font-black <?= $isHabisRekom ? 'text-red-500' : 'text-gray-900' ?> leading-tight tracking-tight"><?= $r['name'] ?></h3>
                            </div>
                            
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-center mt-12">
                <button id="lihatSemuaMenuBtn" class="bg-sabanaRed text-white px-10 py-4 rounded-full font-bold text-xl hover:bg-red-700 hover:scale-105 transition-all duration-300 shadow-lg flex items-center gap-3 active:bg-[#7f1d1d]">
                    <i class="fa-solid fa-list text-2xl"></i> Lihat Semua Menu
                </button>
            </div>
        </div>
    </section>

    <div id="kategoriModal" class="fixed inset-0 bg-black/70 z-[10001] flex items-center justify-center hidden transition-all duration-300 p-4 backdrop-blur-sm">
        <div class="bg-gradient-to-br from-white to-red-50 rounded-2xl max-w-lg w-full shadow-2xl transform transition-all scale-95 opacity-0 border border-red-100" id="modalKategoriContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-sabanaRed">Pilih Menu Berdasarkan Kategori</h3>
                    <button id="closeKategoriModal" class="text-gray-400 hover:text-sabanaRed transition text-2xl"><i class="fa-solid fa-circle-xmark"></i></button>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <a href="menu_kategori.php?kategori=reguler" class="group bg-white/80 backdrop-blur-sm p-4 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:scale-105 border border-gray-100 hover:border-sabanaRed/50 hover:shadow-sabanaRed/20">
                        <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-red-50 p-3 group-hover:bg-red-100 transition flex items-center justify-center"><img src="../img/Ayam_dada.png" class="w-full h-full object-contain"></div><span class="font-bold text-gray-800 group-hover:text-sabanaRed transition text-lg">Menu Reguler</span>
                        <p class="text-xs text-gray-500 mt-1">Ayam goreng dengan bagian yang anda sukai</p>
                    </a>
                    <a href="menu_kategori.php?kategori=tambahan" class="group bg-white/80 backdrop-blur-sm p-4 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:scale-105 border border-gray-100 hover:border-sabanaRed/50 hover:shadow-sabanaRed/20">
                        <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-red-50 p-3 group-hover:bg-red-100 transition flex items-center justify-center"><img src="../img/burger_ayam.png" class="w-full h-full object-contain"></div><span class="font-bold text-gray-800 group-hover:text-sabanaRed transition text-lg">Menu Tambahan</span>
                        <p class="text-xs text-gray-500 mt-1">Menu yang membuat anda tambah selera</p>
                    </a>
                    <a href="menu_kategori.php?kategori=paket" class="group bg-white/80 backdrop-blur-sm p-4 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:scale-105 border border-gray-100 hover:border-sabanaRed/50 hover:shadow-sabanaRed/20">
                        <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-red-50 p-3 group-hover:bg-red-100 transition flex items-center justify-center"><img src="../img/paket3.png" class="w-full h-full object-contain"></div><span class="font-bold text-gray-800 group-hover:text-sabanaRed transition text-lg">Menu Paket</span>
                        <p class="text-xs text-gray-500 mt-1">Hemat dengan paket lengkap yang menjanjikan</p>
                    </a>
                    <a href="menu_kategori.php?kategori=combo" class="group bg-white/80 backdrop-blur-sm p-4 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:scale-105 border border-gray-100 hover:border-sabanaRed/50 hover:shadow-sabanaRed/20">
                        <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-red-50 p-3 group-hover:bg-red-100 transition flex items-center justify-center"><img src="../img/combo3.png" class="w-full h-full object-contain"></div><span class="font-bold text-gray-800 group-hover:text-sabanaRed transition text-lg">Menu Combo</span>
                        <p class="text-xs text-gray-500 mt-1">Pilihan untuk keluarga & kumpul bersama</p>
                    </a>
                </div>
                <div class="mt-6 text-center text-gray-400 text-xs">Silahkan pilih kategori menu yang anda inginkan untuk melihat pilihan menu yang lebih lengkap.</div>
            </div>
        </div>
    </div>

    <div id="loginWarningModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center hidden transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-sm w-full mx-4 p-6 shadow-2xl transform transition-all scale-95 opacity-0" id="modalContent">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4"><i class="fa-solid fa-triangle-exclamation text-sabanaRed text-3xl"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Login!</h3>
                <p class="text-gray-600 mb-6">Anda belum login. Silakan login terlebih dahulu untuk melakukan pemesanan.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button id="modalCancelBtn" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all duration-200">Tidak</button>
                    <button id="modalLoginBtn" class="px-6 py-2.5 bg-sabanaRed text-white rounded-xl font-semibold hover:bg-red-700 active:bg-[#7f1d1d] hover:scale-105 transition-all duration-200 shadow-md">Login</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="menuIdHidden" value="<?= isset($menu['id']) ? (int) $menu['id'] : 0 ?>">
    <input type="hidden" id="menuNameHidden" value="<?= htmlspecialchars($menu['name']) ?>">
    <input type="hidden" id="menuPriceHidden" value="<?= $menu['price'] ?>">
    <input type="hidden" id="menuImgHidden" value="<?= $menu['img'] ?>">
    <input type="hidden" id="menuKategoriHidden" value="<?= $menu['kategori'] ?>">

    <a href="keranjang.php" id="floatingCart" class="fixed bottom-6 right-6 z-50 cursor-pointer transition-all duration-300 hover:scale-110">
        <img src="../img/keranjang.png" alt="Keranjang" class="w-12 h-12 object-contain" onerror="this.src='https://placehold.co/48x48/e11d48/white?text=Cart'">
        <span id="cartBadge" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
    </a>

    <footer id="lokasi" class="bg-[#1a1a1a] text-gray-300 pt-16 pb-8 font-sans scroll-mt-24">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
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

            <div>
                <div class="border-l-4 border-yellow-400 pl-4 mb-4">
                    <h4 class="text-lg font-bold text-yellow-400 uppercase tracking-tight leading-tight">🗺️ PETA INTERAKTIF</h4>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/global.js"></script>
    <script src="js/pesan.js"></script>
</body>

</html>
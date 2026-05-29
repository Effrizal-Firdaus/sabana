<?php
session_start();
include_once __DIR__ . '/../server/koneksi.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.html');
    exit;
}

$nama_user = htmlspecialchars($_SESSION['user']['nama']);
$email_user = htmlspecialchars($_SESSION['user']['email']);
$user_id = htmlspecialchars($_SESSION['user']['id']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sabana Fried Chicken</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/checkout.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sabanaRed: "#e11d48",
                        sabanaGold: "#ffcc00",
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-red-50 font-poppins" data-user-id="<?= $user_id ?>">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header dengan tombol kembali -->
        <div class="flex items-center gap-4 mb-8">
            <a href="keranjang.php" class="back-button bg-sabanaRed text-white px-4 py-2.5 rounded-full font-semibold shadow-md flex items-center gap-2 hover:bg-red-700 active:bg-[#7f1d1d] transition-all duration-200 hover:scale-105">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">
                <span class="bg-gradient-to-r from-sabanaRed to-sabanaGold bg-clip-text text-transparent">Checkout</span>
            </h1>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri & Tengah -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Daftar Pesanan (akan diisi oleh JS) -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-sabanaRed to-rose-500 px-6 py-4">
                        <h2 class="text-white font-bold text-xl flex items-center gap-2">
                            <i class="fa-regular fa-receipt"></i> Ringkasan Pesanan
                        </h2>
                    </div>
                    <div id="checkoutItemsList" class="divide-y divide-gray-100 p-4">
                        <div class="text-center text-gray-500 py-8">Memuat data...</div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center font-bold text-lg border-t">
                        <span>Total Menu (<span id="totalItemCount">0</span> item)</span>
                        <span id="subtotalMenu" class="text-sabanaRed text-xl">Rp 0</span>
                    </div>
                </div>

                <!-- Alamat & Pengiriman -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-sabanaRed to-rose-500 px-6 py-4">
                        <h2 class="text-white font-bold text-xl flex items-center gap-2">
                            <i class="fa-solid fa-location-dot"></i> Alamat & Pengiriman
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <!-- Pilihan Jenis Pesanan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pesanan</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-sabanaRed">
                                    <input type="radio" name="jenis_pesanan" value="delivery" checked> <i class="fa-solid fa-truck"></i> Delivery
                                </label>
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-sabanaRed">
                                    <input type="radio" name="jenis_pesanan" value="takeaway"> <i class="fa-solid fa-store"></i> Take Away
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Penerima</label>
                            <input type="text" id="namaPenerima" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sabanaRed focus:border-transparent transition" value="<?= $nama_user ?>">
                        </div>
                        <div id="alamatContainer">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea id="alamat" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sabanaRed focus:border-transparent transition" placeholder="Jl. Jatimakmur No.36, Kel. Jatimakmur, Kec. Pondok Gede, Kota Bekasi"></textarea>
                            <p class="text-xs text-gray-400 mt-1">* Tulis alamat lengkap Anda (jalan, nomor, kecamatan, kota)</p>
                        </div>
                        <!-- TAMBAHAN FIELD CATATAN -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea id="catatan" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sabanaRed focus:border-transparent transition" placeholder="Contoh: Ayamnya minta pedas level 3, jangan pakai bawang, atau tambah sambal extra..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">* Catatan khusus untuk dapur/kurir</p>
                        </div>
                        <div id="jarakContainer">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Jarak (km)</label>
                            <input type="number" id="jarak" value="0" step="0.1" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sabanaRed">
                            <p class="text-xs text-gray-400 mt-1">* Jika jarak ≤ 3 km, ongkir GRATIS</p>
                        </div>
                        <div id="layananContainer">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Layanan Pengiriman</label>
                            <div class="flex flex-col gap-3">
                                <label class="pengiriman-option w-full flex items-center gap-4 p-3 border rounded-xl cursor-pointer transition hover:border-sabanaRed has-[:checked]:border-sabanaRed has-[:checked]:bg-red-50">
                                    <input type="radio" name="pengiriman" value="reguler" checked class="w-4 h-4">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">Reguler</div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs text-gray-500">Ongkir Rp 8.000</span>
                                            <span class="text-xs text-gray-400">Estimasi masak 10-20 menit</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="pengiriman-option w-full flex items-center gap-4 p-3 border rounded-xl cursor-pointer transition hover:border-sabanaRed has-[:checked]:border-sabanaRed has-[:checked]:bg-red-50">
                                    <input type="radio" name="pengiriman" value="express" class="w-4 h-4">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">Express</div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs text-gray-500">Ongkir Rp 12.000</span>
                                            <span class="text-xs text-gray-400">Estimasi masak 5-10 menit</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Pembayaran -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 sticky top-6">
                    <div class="bg-gradient-to-r from-sabanaRed to-rose-500 px-6 py-4">
                        <h2 class="text-white font-bold text-xl flex items-center gap-2">
                            <i class="fa-regular fa-credit-card"></i> Rincian Pembayaran
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between border-b border-dashed pb-2">
                            <span class="text-gray-600">ID Pesanan</span>
                            <span id="orderId" class="font-mono font-bold text-sabanaRed">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal Menu</span>
                            <span id="subtotalMenuDetail" class="font-semibold">Rp 0</span>
                        </div>
                        <div class="flex justify-between" id="ongkirRow">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span id="ongkirDetail" class="font-semibold">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total Pembayaran</span>
                            <span id="totalBayar" class="text-sabanaRed">Rp 0</span>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="px-6 pb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer w-full justify-center has-[:checked]:bg-red-50 has-[:checked]:border-sabanaRed">
                                <input type="radio" name="payment" value="QRIS" checked> <i class="fa-solid fa-qrcode text-xl"></i> QRIS
                            </label>
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer w-full justify-center has-[:checked]:bg-red-50 has-[:checked]:border-sabanaRed">
                                <input type="radio" name="payment" value="COD"> <i class="fa-solid fa-truck text-xl"></i> COD
                            </label>
                        </div>
                    </div>

                    <div id="paymentPreview" class="px-6 pb-4">
                        <div id="qrisPreview" class="payment-preview bg-gray-50 border border-red-200 rounded-2xl p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">QRIS Tagihan</p>
                                    <h3 id="qrisAmount" class="text-2xl font-bold text-sabanaRed">Rp 0</h3>
                                </div>
                                <div class="w-24 h-24 rounded-2xl bg-white border border-gray-200 overflow-hidden flex items-center justify-center">
                                    <img src="../img/qris.png" alt="QRIS" class="w-full h-full object-contain" onerror="this.src='https://placehold.co/96x96/e11d48/white?text=QRIS'">
                                </div>
                            </div>
                            <div class="grid gap-2 text-sm text-gray-700">
                                <div class="flex justify-between"><span class="font-medium">Nama Penerima</span><span id="qrisNama">-</span></div>
                                <div class="flex justify-between"><span class="font-medium">ID Pesanan</span><span id="qrisOrderId">-</span></div>
                                <div class="flex justify-between"><span class="font-medium">Subtotal Menu</span><span id="qrisSubtotal">Rp 0</span></div>
                                <div class="flex justify-between"><span class="font-medium">Ongkos Kirim</span><span id="qrisOngkir">Rp 0</span></div>
                                <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2"><span>Total Pembayaran</span><span id="qrisTotal">Rp 0</span></div>
                            </div>
                        </div>
                        <div id="codInfo" class="payment-preview hidden bg-gray-50 border border-gray-200 rounded-2xl p-4 text-gray-700">
                            <p class="font-semibold mb-2">Bayar di Tempat (COD)</p>
                            <p class="text-sm text-gray-500">Pilih COD jika Anda ingin membayar langsung ke kurir ketika pesanan tiba.</p>
                        </div>
                    </div>

                    <!-- Tombol Konfirmasi -->
                    <div class="p-6 pt-2">
                        <button id="confirmOrderBtn" class="w-full bg-sabanaRed text-white font-bold py-3 rounded-xl hover:bg-red-700 hover:scale-105 transition-all duration-300 shadow-lg flex items-center justify-center gap-2 active:scale-95 active:bg-[#7f1d1d]">
                            <i class="fa-regular fa-circle-check"></i> Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast sukses -->
    <div id="successToast" class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg hidden items-center gap-3 z-50">
        <i class="fa-regular fa-circle-check text-xl"></i>
        <span>Pesanan berhasil dibuat!</span>
    </div>

    <script src="js/checkout.js"></script>
</body>

</html>
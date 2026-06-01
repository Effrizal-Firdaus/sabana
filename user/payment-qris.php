<?php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.html');
    exit;
}
$user_id = htmlspecialchars($_SESSION['user']['id']);
$nama_user = htmlspecialchars($_SESSION['user']['nama']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="css/payment-qris.css">
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
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <a href="checkout.php" class="back-button text-white px-4 py-2.5 rounded-full font-semibold inline-flex items-center gap-2 transition-all duration-200 mb-4">
                <i class="fa-solid fa-arrow-left"></i> <span>Kembali</span>
            </a>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight mt-4">
                <span class="text-sabanaRed">Pembayaran QRIS</span>
            </h1>
            <p class="text-gray-600 mt-2">Scan QR Code di bawah untuk menyelesaikan pembayaran pesanan Anda</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Kolom Kiri: QR Code QRIS -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 payment-card self-start">
                <div class="bg-gradient-to-r from-sabanaRed to-rose-500 px-6 py-4">
                    <h2 class="text-white font-bold text-xl flex items-center gap-2">
                        <i class="fa-solid fa-qrcode"></i> QRIS Pembayaran
                    </h2>
                </div>
                <div class="p-8 flex flex-col items-center gap-6">
                    <div id="qr-code" class="p-4 bg-gray-50 rounded-2xl border-2 border-gray-200">
                        <div class="w-64 h-64 bg-white rounded-lg flex items-center justify-center">
                            <img id="qrisImage" src="../img/qris.png" alt="QRIS" class="w-full h-full object-contain" onerror="this.style.display='none'">
                            <div id="qrContainer"></div>
                        </div>
                    </div>
                    <div class="w-full text-center text-sm text-gray-600 space-y-2">
                        <p class="font-semibold">📱 Scan dengan aplikasi pembayaran Anda</p>
                        <p>Atau</p>
                        <button type="button" id="confirmPaymentBtn" class="w-full bg-green-500 text-white font-bold py-3 rounded-xl hover:bg-green-600 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-regular fa-circle-check"></i> Konfirmasi Pembayaran
                        </button>
                        <p class="text-xs text-gray-500 mt-3">Klik tombol di atas setelah Anda selesai melakukan pembayaran</p>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Pesanan -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 payment-card h-fit sticky top-6">
                <div class="bg-gradient-to-r from-sabanaRed to-rose-500 px-6 py-4">
                    <h2 class="text-white font-bold text-xl flex items-center gap-2">
                        <i class="fa-regular fa-receipt"></i> Detail Pesanan
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <p class="text-xs uppercase tracking-widest text-gray-500 mb-1">ID Pesanan</p>
                        <p id="detailOrderId" class="text-2xl font-bold text-sabanaRed font-mono">-</p>
                    </div>
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div>
                            <label class="text-xs uppercase tracking-widest text-gray-500">Nama Penerima</label>
                            <p id="detailNamaPenerima" class="text-lg font-semibold text-gray-900">-</p>
                        </div>
                        <div class="border-t border-dashed my-3"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal Menu</span>
                            <span id="detailSubtotal" class="font-semibold">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span id="detailOngkir" class="font-semibold">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold pt-3 border-t border-gray-200">
                            <span>Total Pembayaran</span>
                            <span id="detailTotal" class="text-sabanaRed text-2xl">Rp 0</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Ringkasan Pesanan:</p>
                        <div id="detailItems" class="space-y-2 text-sm"></div>
                    </div>
                    <!-- Input nominal -->
                    <div id="nominalPaymentContainer" class="mt-4 pt-4 border-t border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal yang dibayarkan</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" id="nominalBayarInput" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-sabanaRed focus:border-sabanaRed" placeholder="Masukkan nominal" value="">
                        </div>
                        <p class="text-xs text-gray-400 mt-1" id="nominalHint"></p>
                    </div>
                    <!-- Status Pembayaran -->
                    <div id="statusPembayaran" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-center">
                        <p class="text-sm font-semibold text-yellow-800">
                            <i class="fa-solid fa-hourglass-end mr-2"></i> Menunggu Pembayaran
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed bottom-5 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg hidden items-center gap-3 z-50">
        <i class="fa-regular fa-circle-check text-xl"></i>
        <span id="toastMessage">Pembayaran dikonfirmasi!</span>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="fixed bottom-5 right-5 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg hidden items-center gap-3 z-50">
        <i class="fa-regular fa-circle-exclamation text-xl"></i>
        <span id="errorToastMessage">Nominal tidak sesuai!</span>
    </div>

    <script src="js/payment-qris.js"></script>
</body>

</html>
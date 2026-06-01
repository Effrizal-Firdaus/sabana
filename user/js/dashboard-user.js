document.addEventListener("DOMContentLoaded", function() {
    // ========== TOAST NOTIFICATION ==========
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fa-${type === 'success' ? 'solid fa-check-circle' : 'solid fa-exclamation-triangle'}"></i>
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close">&times;</button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
        setTimeout(() => {
            if (toast && toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }

    // ========== RATING STORAGE (localStorage) ==========
    const bodyElement = document.body;
    const userId = bodyElement.dataset.userId || (window.userData && window.userData.email ? window.userData.email.toLowerCase().replace(/[^a-z0-9]/g, '_') : 'guest');
    const ratingStorageKey = `sabanaRatingStatus_${userId}`;
    function getRatingStatus(orderId) {
        try {
            const ratings = JSON.parse(localStorage.getItem(ratingStorageKey) || '{}');
            return ratings[orderId] || null;
        } catch(e) { return null; }
    }
    function setRatingStatus(orderId, rating) {
        const ratings = JSON.parse(localStorage.getItem(ratingStorageKey) || '{}');
        ratings[orderId] = rating;
        localStorage.setItem(ratingStorageKey, JSON.stringify(ratings));
    }

    const contentDiv = document.getElementById('dynamicContent');
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutForm = document.getElementById('logoutForm');

    // ========== PROFIL (localStorage) ==========
    const profileStorageKey = `sabana_userProfile_${userId}`;
    function loadUserProfile() {
        let profile = window.userData ? { ...window.userData } : {};
        try {
            const saved = localStorage.getItem(profileStorageKey);
            if (saved) {
                let parsed = JSON.parse(saved);
                if (profile.email && parsed.email !== profile.email) {
                    parsed.email = profile.email;
                    localStorage.setItem(profileStorageKey, JSON.stringify(parsed));
                }
                profile = Object.assign({}, parsed, profile);
            }
        } catch (err) {}
        if (window.userData && window.userData.email) {
            profile.email = window.userData.email;
        }
        return profile;
    }
    function saveUserProfile(data) {
        try {
            localStorage.setItem(profileStorageKey, JSON.stringify(data));
        } catch (err) {}
    }
    let userData = loadUserProfile();

    // ========== API: Ambil pesanan dari database ==========
    async function fetchUserOrders() {
        try {
            const response = await fetch('../api/get_user_orders.php');
            const data = await response.json();
            if (data.success) return data.orders;
            else return [];
        } catch (err) {
            console.error('Gagal ambil pesanan:', err);
            return [];
        }
    }

    // ========== UPDATE STATISTIK ==========
    async function updateStats() {
        const orders = await fetchUserOrders();
        const aktif = orders.filter(o => o.status !== 'selesai').length;
        const riwayat = orders.filter(o => o.status === 'selesai').length;
        const statPesanan = document.getElementById('statPesanan');
        const statRiwayat = document.getElementById('statRiwayat');
        if (statPesanan) statPesanan.innerText = aktif;
        if (statRiwayat) statRiwayat.innerText = riwayat;
    }

    // ========== RENDER PESANAN SAYA ==========
    let pollingInterval;
    let pollingActive = true;
    let currentMenu = 'pesanan-saya';

    async function renderPesananSaya() {
        // Jika polling sedang dihentikan sementara (user sedang ngetik), jangan render ulang
        if (!pollingActive && currentMenu === 'pesanan-saya') {
            return;
        }
        
        const orders = await fetchUserOrders();
        const pesananAktif = orders.filter(o => o.status !== 'selesai');
        
        // Hanya tampilkan pesanan yang selesai TAPI BELUM DIBERI RATING
        const pesananSelesai = orders.filter(o => o.status === 'selesai' && !o.rating);

        if (pesananAktif.length === 0 && pesananSelesai.length === 0) {
            contentDiv.innerHTML = `
                <div class="max-w-3xl mx-auto py-20 px-6">
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-xl overflow-hidden">
                        <div class="p-10 text-center">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-sabanaRed/10 text-sabanaRed text-3xl shadow-inner">
                                <i class="fa-solid fa-bell-slash"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-900 mb-3">Belum ada pesanan aktif</h3>
                            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Saat ini belum ada order yang sedang diproses. Yuk, cek menu dan tambahkan pesanan favoritmu untuk memulai.</p>
                            <a href="../menu_utama.php#menu" class="inline-flex items-center justify-center gap-2 rounded-full bg-sabanaRed text-white px-6 py-3 text-sm font-semibold hover:bg-red-600 hover:-translate-y-0.5 active:scale-95 active:translate-y-0.5 transform transition-all duration-200 ease-out shadow-sm hover:shadow-lg active:shadow-sm">Lihat Menu Sekarang <i class="fa-solid fa-utensils"></i></a>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        let html = `
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 border-l-4 border-sabanaRed pl-4">Pesanan Saya</h2>
                    <p class="text-gray-500 mt-2 ml-4">Status pesanan Anda saat ini</p>
                </div>
                <div class="space-y-6">
        `;

        // ================= PESANAN AKTIF =================
        for (const order of pesananAktif) {
            let statusText = '', statusIcon = '', progressColor = '';
            let step = 0;
            switch(order.status) {
                case 'disiapkan': statusText = 'Disiapkan'; statusIcon = 'fa-kitchen-set'; progressColor = '#f97316'; step = 0; break;
                case 'dimasak': statusText = 'Dimasak'; statusIcon = 'fa-fire'; progressColor = '#eab308'; step = 1; break;
                case 'dikirim': statusText = 'Dikirim'; statusIcon = 'fa-truck'; progressColor = '#3b82f6'; step = 2; break;
                case 'diterima': statusText = 'Sampai'; statusIcon = 'fa-location-dot'; progressColor = '#22c55e'; step = 3; break;
                default: statusText = order.status || '';
            }
            const progressPercent = (step / 4) * 100;
            const hargaAsli = order.totalMenu || order.total;

            // --- LOGIKA TOMBOL BATAL PESANAN ---
            let cancelBtnHtml = '';
            if (order.status === 'disiapkan') {
                if (order.dikonfirmasi == 0) {
                    // Jika admin belum konfirmasi, muncul tombol merah bisa dipencet
                    cancelBtnHtml = `
                    <div class="mt-4 text-right">
                        <button class="btn-cancel-user-order px-4 py-2 text-sm font-bold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm" data-order="${order.id}">
                            <i class="fa-solid fa-xmark mr-1"></i> Batalkan Pesanan
                        </button>
                    </div>`;
                } else {
                    // Jika admin sudah terima, tombol terkunci
                    cancelBtnHtml = `
                    <div class="mt-4 text-right">
                        <button disabled class="px-4 py-2 text-sm font-bold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed opacity-70">
                            <i class="fa-solid fa-lock mr-1"></i> Diproses Admin (Tidak dapat dibatalkan)
                        </button>
                    </div>`;
                }
            }

            html += `
                <div class="order-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all">
                    <div class="p-5">
                        <div class="flex flex-wrap justify-between items-start gap-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">${escapeHtml(order.nama)}</h3>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                    <span><i class="fa-solid fa-cubes mr-1"></i> ${order.jumlah} item</span>
                                    <span><i class="fa-regular fa-calendar mr-1"></i> ${order.tgl}</span>
                                </div>
                                <p class="text-sabanaRed font-bold text-xl mt-2">Rp ${hargaAsli.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="status-badge flex items-center gap-1" style="background: ${progressColor}20; color: ${progressColor}; border:1px solid ${progressColor}40;">
                                    <i class="fa-solid ${statusIcon} text-xs"></i> ${statusText}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="relative w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mb-4">
                                <div class="progress-bar absolute top-0 left-0 h-full rounded-full" style="width: ${progressPercent}%; background-color: ${progressColor};"></div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span style="color: ${step >= 0 ? progressColor : '#9ca3af'}">Disiapkan</span>
                                <span style="color: ${step >= 1 ? progressColor : '#9ca3af'}">Dimasak</span>
                                <span style="color: ${step >= 2 ? progressColor : '#9ca3af'}">Dikirim</span>
                                <span style="color: ${step >= 3 ? progressColor : '#9ca3af'}">Sampai</span>
                                <span style="color: ${step >= 4 ? progressColor : '#9ca3af'}">Selesai</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-2 text-right">
                                <i class="fa-regular fa-clock"></i> Estimasi selesai: ${order.status === 'disiapkan' ? '9-12' : (order.status === 'dimasak' ? '6-9' : (order.status === 'dikirim' ? '3-6' : (order.status === 'diterima' ? '0-3' : 'Selesai')))} menit
                            </div>
                            
                            ${cancelBtnHtml}

                        </div>
                    </div>
                </div>
            `;
        }

        // ================= PESANAN SELESAI =================
        for (const order of pesananSelesai) {
            const hargaAsli = order.totalMenu || order.total;
            const ratingHtml = `
                <div class="mt-3 pt-3 border-t border-gray-100" id="rating-form-${order.id}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-medium text-gray-700">Beri rating:</span>
                        <div class="star-rating-select flex gap-1" data-order="${order.id}">
                            ${[1,2,3,4,5].map(star => `<i class="fa-regular fa-star text-gray-400 hover:text-yellow-400 cursor-pointer transition" data-rate="${star}"></i>`).join('')}
                        </div>
                    </div>
                    <textarea id="komentar-${order.id}" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm" placeholder="Tulis komentar (opsional)"></textarea>
                    <button class="btn-submit-rating mt-2 bg-sabanaRed text-white px-4 py-1 rounded-full text-sm" data-order="${order.id}">Kirim Rating & Komentar</button>
                </div>
            `;

            html += `
                <div class="order-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all">
                    <div class="p-5">
                        <div class="flex flex-wrap justify-between items-start gap-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">${escapeHtml(order.nama)}</h3>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                    <span><i class="fa-solid fa-cubes mr-1"></i> ${order.jumlah} item</span>
                                    <span><i class="fa-regular fa-calendar mr-1"></i> ${order.tgl}</span>
                                </div>
                                <p class="text-sabanaRed font-bold text-xl mt-2">Rp ${hargaAsli.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="status-badge flex items-center gap-1" style="background: #22c55e20; color: #22c55e; border:1px solid #22c55e40;">
                                    <i class="fa-solid fa-check-circle text-xs"></i> Selesai
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <div class="relative w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mb-4">
                                <div class="progress-bar absolute top-0 left-0 h-full rounded-full" style="width: 100%; background-color: #22c55e;"></div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span style="color: #22c55e;">Disiapkan</span>
                                <span style="color: #22c55e;">Dimasak</span>
                                <span style="color: #22c55e;">Dikirim</span>
                                <span style="color: #22c55e;">Sampai</span>
                                <span style="color: #22c55e;">Selesai</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-2 text-right">
                                <i class="fa-regular fa-clock"></i> Selesai pada ${order.tgl}
                            </div>
                            ${ratingHtml}
                        </div>
                    </div>
                </div>
            `;
        }

        html += `</div></div>`;
        contentDiv.innerHTML = html;

        // Terapkan rating sementara dari sessionStorage
        document.querySelectorAll('.star-rating-select').forEach(container => {
            const orderId = container.dataset.order;
            const savedRating = sessionStorage.getItem(`temp_rating_${orderId}`);
            if (savedRating) {
                const rate = parseInt(savedRating);
                const stars = container.querySelectorAll('i');
                stars.forEach(s => {
                    const val = parseInt(s.dataset.rate);
                    s.className = val <= rate ? 'fa-solid fa-star text-yellow-400' : 'fa-regular fa-star text-gray-400';
                });
            }
        });

        // Event Pilih Bintang
        document.querySelectorAll('.star-rating-select').forEach(container => {
            const stars = container.querySelectorAll('i');
            stars.forEach(star => {
                star.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const rate = parseInt(this.getAttribute('data-rate'));
                    stars.forEach(s => {
                        const val = parseInt(s.getAttribute('data-rate'));
                        if (val <= rate) {
                            s.className = 'fa-solid fa-star text-yellow-400';
                        } else {
                            s.className = 'fa-regular fa-star text-gray-400';
                        }
                    });
                    const orderId = container.getAttribute('data-order');
                    sessionStorage.setItem(`temp_rating_${orderId}`, rate);
                });
            });
        });
        
        // Polling handler saat isi ulasan
        function stopPolling() { pollingActive = false; }
        function resumePolling() { pollingActive = true; }
        document.querySelectorAll('textarea[id^="komentar-"]').forEach(textarea => {
            textarea.removeEventListener('focus', stopPolling);
            textarea.removeEventListener('blur', resumePolling);
            textarea.addEventListener('focus', stopPolling);
            textarea.addEventListener('blur', resumePolling);
        });

        // Event Kirim Rating
        document.querySelectorAll('.btn-submit-rating').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const orderId = btn.getAttribute('data-order');
                const starContainer = document.querySelector(`.star-rating-select[data-order="${orderId}"]`);
                const solidStars = starContainer ? starContainer.querySelectorAll('i.fa-solid') : [];

                if (solidStars.length === 0) {
                    showToast('Pilih rating terlebih dahulu', 'error');
                    return;
                }
                let ratingValue = solidStars.length;
                const komentar = document.getElementById(`komentar-${orderId}`).value.trim();
                
                try {
                    const response = await fetch('../api/save_rating_comment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId, rating: ratingValue, komentar })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast('Rating dan komentar terkirim!', 'success');
                        sessionStorage.removeItem(`temp_rating_${orderId}`);
                        await loadContent(currentMenu);
                    } else {
                        showToast(result.message || 'Gagal menyimpan', 'error');
                    }
                } catch (err) {
                    showToast('Error: ' + err.message, 'error');
                }
            });
        });

        // ==========================================
        // EVENT LISTENER BARU: BATALKAN PESANAN USER
        // ==========================================
        document.querySelectorAll('.btn-cancel-user-order').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const orderId = btn.getAttribute('data-order');
                // Panggil fungsi modal cantik yang akan kita buat di bawah
                openCancelOrderUserModal(orderId); 
            });
        });
    }

    async function ratingSubmitHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        const btn = e.currentTarget;
        const orderId = btn.dataset.order;
        const starContainer = document.querySelector(`.star-rating-select[data-order="${orderId}"]`);
        const selectedStar = starContainer ? starContainer.querySelector('i.fa-solid') : null;
        if (!selectedStar) {
            showToast('Pilih rating terlebih dahulu', 'error');
            return;
        }
        const ratingValue = parseInt(selectedStar.dataset.rate);
        const komentar = document.getElementById(`komentar-${orderId}`).value.trim();
        
        try {
            const response = await fetch('../api/save_rating_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, rating: ratingValue, komentar })
            });
            const result = await response.json();
            if (result.success) {
                showToast('Rating dan komentar terkirim!', 'success');
                sessionStorage.removeItem(`temp_rating_${orderId}`);
                await renderPesananSaya();
                await renderRiwayat();
                await updateStats();
            } else {
                showToast(result.message || 'Gagal menyimpan', 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        }
    }

    // ========== RENDER RIWAYAT ==========
    async function renderRiwayat() {
        const orders = await fetchUserOrders();
        const riwayatPesanan = orders.filter(o => o.status === 'selesai');
        if (riwayatPesanan.length === 0) {
            contentDiv.innerHTML = `
                <div class="max-w-3xl mx-auto py-20 px-6">
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-xl overflow-hidden">
                        <div class="p-10 text-center">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-100 text-sabanaRed text-3xl shadow-inner">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-900 mb-3">Riwayat pesanan kosong</h3>
                            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Kamu belum menyelesaikan pesanan apapun. Nantikan penawaran spesial dan cobalah menu baru kami.</p>
                            <a href="../menu_utama.php#menu" class="inline-flex items-center justify-center gap-2 rounded-full bg-sabanaRed text-white px-6 py-3 text-sm font-semibold hover:bg-red-600 hover:-translate-y-0.5 active:scale-95 active:translate-y-0.5 transform transition-all duration-200 ease-out shadow-sm hover:shadow-lg active:shadow-sm">Jelajahi Menu</a>
                        </div>
                    </div>
                </div>`;
            return;
        }

        function getImageForMenu(nama) {
            // Ubah nama dari database menjadi huruf kecil semua agar mudah dideteksi
            const lowerName = nama.toLowerCase();

            // ========================================================
            // PRIORITAS 1: MENU PAKET & COMBO (Paling atas agar tidak bentrok)
            // ========================================================
            // Paket
            if (lowerName.includes('dada + nasi')) return '../../img/paket1.png';
            if (lowerName.includes('sayap + nasi')) return '../../img/paket2.png';
            if (lowerName.includes('geprek')) return '../../img/paket3.png';
            if (lowerName.includes('ijo')) return '../../img/paket4.png';
            
            // Combo
            if (lowerName.includes('3 pcs')) return '../../img/combo1.png';
            if (lowerName.includes('5 pcs')) return '../../img/combo2.png';
            if (lowerName.includes('7 pcs')) return '../../img/combo3.png';

            // ========================================================
            // PRIORITAS 2: MENU REGULER (Ayam Satuan)
            // ========================================================
            if (lowerName.includes('paha atas')) return '../../img/Ayam_pahaatas.png';
            if (lowerName.includes('paha bawah')) return '../../img/paha_bawah.png';
            // Harus ditaruh di bawah paha, agar kata 'dada' & 'sayap' tidak menimpa paket
            if (lowerName.includes('dada')) return '../../img/Ayam_dada.png'; 
            if (lowerName.includes('sayap')) return '../../img/sayap.png';

            // ========================================================
            // PRIORITAS 3: MENU TAMBAHAN & MINUMAN
            // ========================================================
            if (lowerName.includes('burger')) return '../../img/burger_ayam.png';
            if (lowerName.includes('rice box')) return '../../img/rice_box.png';
            if (lowerName.includes('kentang')) return '../../img/kentang.png';
            if (lowerName.includes('nasi putih') || lowerName === 'nasi') return '../../img/nasi.png';
            if (lowerName.includes('kulit')) return '../../img/kulit.png';
            if (lowerName.includes('strips')) return '../../img/strips.png';
            if (lowerName.includes('bakso')) return '../../img/bakso.png';
            if (lowerName.includes('roll')) return '../../img/roll.png';
            if (lowerName.includes('es teh') || lowerName.includes('esteh')) return '../../img/esteh.png';

            // Gambar default (jaga-jaga jika sewaktu-waktu Anda menambah menu baru di database tapi lupa update JS)
            return '../../img/Ayam_dada.png';
        }

        let html = `
            <div class="max-w-5xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 border-l-4 border-sabanaRed pl-4">Riwayat Pesanan</h2>
                    <p class="text-gray-500 mt-2 ml-4">Semua pesanan yang telah selesai</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        `;
        for (const order of riwayatPesanan) {
            let menuName = order.items && order.items.length > 0 ? order.items[0].name : order.nama;
            let jumlahItem = order.jumlah;
            let totalHarga = order.total;
            let tgl = order.tgl;
            let metodeBayar = order.metodePembayaran || 'QRIS';
            const imgFile = getImageForMenu(menuName);
            html += `
                <div class="riwayat-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 relative">
                    <button class="btn-delete-riwayat absolute top-2 right-2 bg-red-100 text-red-600 rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-500 hover:text-white transition" data-order="${order.id}"><i class="fa-solid fa-trash-can"></i></button>
                    <div class="flex items-center gap-4 p-4">
                        <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                            <img src="${imgFile}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/64x64/e11d48/white?text=Food'">
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-lg">${escapeHtml(menuName)}${order.items && order.items.length > 1 ? ' dan lainnya' : ''}</h3>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600 mt-1">
                                <span><img src="../../img/box-icon.png" alt="item" class="w-4 h-4 inline mr-1" onerror="this.style.display='none'"> ${jumlahItem} item</span>
                                <span><i class="fa-regular fa-calendar mr-1"></i> ${tgl}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-sabanaRed font-bold text-base">Rp ${totalHarga.toLocaleString('id-ID')}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${metodeBayar === 'QRIS' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}">
                                    <i class="fa-solid ${metodeBayar === 'QRIS' ? 'fa-qrcode' : 'fa-truck'} mr-1"></i> ${metodeBayar}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        html += `</div></div>`;
        contentDiv.innerHTML = html;

        // Sambungkan tombol tong sampah dengan Modal Custom
        document.querySelectorAll('.btn-delete-riwayat').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const orderId = btn.dataset.order;
                openDeleteRiwayatModal(orderId); // Panggil modal
            });
        });
    }

    // ========== RENDER EDIT PROFIL ==========
    function renderEditProfil() {
        contentDiv.innerHTML = `
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 border-l-4 border-sabanaRed pl-4">Edit Profil</h2>
                    <p class="text-gray-500 mt-2 ml-4">Perbarui informasi akun Anda di sini</p>
                </div>
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="p-6 md:p-8">
                        <div class="flex flex-col md:flex-row gap-8">
                            <div class="flex flex-col items-center md:w-1/3">
                                <div class="relative group">
                                    <div class="w-40 h-40 rounded-full bg-gradient-to-br from-sabanaRed to-sabanaGold p-1 shadow-lg">
                                        <div class="w-full h-full rounded-full bg-white overflow-hidden">
                                            <img id="profileImg" src="${userData.foto || '../../img/avatar-saya.png'}" class="w-full h-full object-cover" onerror="this.src='../../img/avatar-saya.png'">
                                        </div>
                                    </div>
                                    <label class="absolute bottom-0 right-0 bg-sabanaRed hover:bg-red-700 text-white rounded-full p-2 cursor-pointer shadow-lg transition-all hover:scale-110">
                                        <i class="fa-solid fa-camera text-sm"></i>
                                        <input type="file" id="uploadFoto" class="hidden" accept="image/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-400 mt-3">Klik kamera untuk ganti foto</p>
                                <button id="removeFotoBtn" type="button" class="mt-3 hidden px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 rounded-lg text-sm font-medium transition-all">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus Foto
                                </button>
                            </div>
                            <div class="flex-1 space-y-5">
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fa-regular fa-user mr-2 text-sabanaRed"></i> Nama Lengkap</label><input type="text" id="fullname" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sabanaRed focus:border-transparent transition-all" value="${userData.nama}"></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fa-regular fa-envelope mr-2 text-sabanaRed"></i> Email</label><input type="email" id="email" class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-100 text-gray-500 cursor-not-allowed" value="${userData.email}" disabled></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1"><i class="fa-solid fa-location-dot mr-2 text-sabanaRed"></i> Alamat</label><textarea id="alamat" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sabanaRed focus:border-transparent transition-all">${userData.alamat || ''}</textarea></div>
                                <button id="updateProfilBtn" class="w-full md:w-auto bg-sabanaRed text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-md hover:bg-red-700 hover:shadow-lg transform hover:scale-105 active:scale-95 active:bg-[#7f1d1d]"><i class="fa-regular fa-floppy-disk mr-2"></i> Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        const uploadInput = document.getElementById('uploadFoto');
        const profileImg = document.getElementById('profileImg');
        const removeFotoBtn = document.getElementById('removeFotoBtn');
        let tempFoto = userData.foto || null;
        if (uploadInput) uploadInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => { profileImg.src = ev.target.result; tempFoto = ev.target.result; removeFotoBtn?.classList.remove('hidden'); };
                reader.readAsDataURL(file);
            }
        });
        if (removeFotoBtn) {
            removeFotoBtn.addEventListener('click', () => {
                profileImg.src = '../../img/avatar-saya.png';
                tempFoto = null;
                removeFotoBtn.classList.add('hidden');
                showToast('Foto profil akan dihapus saat Anda klik Simpan Perubahan.', 'info');
            });
        }
        document.getElementById('updateProfilBtn')?.addEventListener('click', () => {
            userData.nama = document.getElementById('fullname').value;
            userData.alamat = document.getElementById('alamat').value;
            userData.foto = tempFoto;
            saveUserProfile(userData);
            window.userData = userData;
            showToast('✅ Perubahan berhasil disimpan.', 'success');
            if (userData.foto) removeFotoBtn?.classList.remove('hidden');
            else removeFotoBtn?.classList.add('hidden');
        });
        if (userData.foto) removeFotoBtn?.classList.remove('hidden');
    }

    // ========== RENDER BANTUAN ==========
    function renderBantuan() {
        contentDiv.innerHTML = `
            <div class="max-w-5xl mx-auto">
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 border-l-4 border-sabanaRed pl-4">Bantuan & Laporan</h2>
                    <p class="text-gray-500 mt-2 ml-4">Kami siap membantu Anda 24/7</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bantuan-card bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-4"><i class="fa-solid fa-handshake-angle text-2xl text-sabanaRed"></i></div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Pusat Bantuan</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">Temukan jawaban cepat untuk pertanyaan umum tentang pemesanan, pembayaran, dan pengiriman.</p>
                        <button id="openFaqBtn" class="inline-flex items-center gap-1 text-sabanaRed font-medium text-sm hover:gap-2 transition-all">Baca selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i></button>
                    </div>
                    <div class="bantuan-card bg-gradient-to-br from-sabanaRed/5 via-white to-yellow-50 rounded-2xl shadow-xl p-6 border border-sabanaRed/20 hover:shadow-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-4"><i class="fa-solid fa-phone-volume text-2xl text-sabanaRed"></i></div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Hubungi Tim Kami</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4">Punya keluhan atau saran? Tim support kami siap mendengarkan dan membantu Anda.</p>
                        <a href="whatsapp://send?phone=628882269963&text=Halo%20Sabana%2C%20saya%20membutuhkan%20bantuan" class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#128C7E] text-white px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105" <i class="fab fa-whatsapp"></i> Hubungi Kami </a>
                    </div>
                </div>
                <div class="mt-8 bantuan-card bg-white rounded-2xl shadow-md p-6 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-4"><div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center"><i class="fa-regular fa-star text-yellow-500 text-xl"></i></div><div><h4 class="font-semibold text-gray-800">Berikan masukan</h4><p class="text-sm text-gray-500">Kritik dan saran membantu kami menjadi lebih baik</p></div></div>
                    <button id="openFeedbackBtn" class="bg-sabanaRed text-white px-5 py-2 rounded-full text-sm font-medium transition-all duration-200 hover:bg-red-700 active:bg-[#7f1d1d] shadow-md hover:shadow-lg">Kirim Masukan</button>
                </div>
            </div>
        `;

        function createFaqModal() {
            if (document.getElementById('faqModal')) return;
            const modalDiv = document.createElement('div');
            modalDiv.id = 'faqModal';
            modalDiv.className = 'fixed inset-0 bg-black/60 z-[10001] flex items-center justify-center hidden transition-all duration-300 p-4';
            modalDiv.innerHTML = `
                <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl transform transition-all scale-95 opacity-0" id="faqModalContent">
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fa-regular fa-circle-question text-sabanaRed"></i> Pusat Bantuan</h3>
                        <button id="closeFaqModal" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="faq-item"><button class="faq-question-btn w-full text-left flex justify-between items-center p-3 rounded-xl"><span class="font-semibold">Bagaimana cara memesan menu?</span><i class="fa-solid fa-chevron-down text-gray-400"></i></button><div class="faq-answer hidden mt-2 text-gray-600 text-sm p-3 bg-gray-50 rounded-lg">Pilih menu, klik "Pesan Sekarang", atur jumlah, tambah ke keranjang, lalu checkout.</div></div>
                        <div class="faq-item"><button class="faq-question-btn w-full text-left flex justify-between items-center p-3 rounded-xl"><span class="font-semibold">Metode pembayaran apa saja?</span><i class="fa-solid fa-chevron-down text-gray-400"></i></button><div class="faq-answer hidden mt-2 text-gray-600 text-sm p-3 bg-gray-50 rounded-lg">Kami menerima QRIS (scan) dan COD (bayar di tempat).</div></div>
                        <div class="faq-item"><button class="faq-question-btn w-full text-left flex justify-between items-center p-3 rounded-xl"><span class="font-semibold">Berapa lama estimasi pengiriman?</span><i class="fa-solid fa-chevron-down text-gray-400"></i></button><div class="faq-answer hidden mt-2 text-gray-600 text-sm p-3 bg-gray-50 rounded-lg">Estimasi 20-40 menit tergantung lokasi. Status bisa dilihat di "Pesanan Saya".</div></div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalDiv);
            modalDiv.querySelectorAll('.faq-question-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const answer = btn.parentElement.querySelector('.faq-answer');
                    const chevron = btn.querySelector('.fa-chevron-down');
                    answer.classList.toggle('hidden');
                    chevron.style.transform = answer.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            });
            return modalDiv;
        }
        createFaqModal();
        const faqModal = document.getElementById('faqModal');
        const faqContent = document.getElementById('faqModalContent');
        const openFaqBtn = document.getElementById('openFaqBtn');
        const closeFaq = document.getElementById('closeFaqModal');
        if (openFaqBtn && faqModal) {
            openFaqBtn.onclick = () => {
                faqModal.classList.remove('hidden');
                setTimeout(() => faqContent?.classList.remove('scale-95', 'opacity-0'), 10);
            };
        }
        if (closeFaq && faqModal) {
            closeFaq.onclick = () => {
                faqContent?.classList.add('scale-95', 'opacity-0');
                setTimeout(() => faqModal.classList.add('hidden'), 300);
            };
        }

        function createFeedbackModal() {
            if (document.getElementById('feedbackModal')) return;
            const modalDiv = document.createElement('div');
            modalDiv.id = 'feedbackModal';
            modalDiv.className = 'fixed inset-0 bg-black/60 z-[10001] flex items-center justify-center hidden transition-all duration-300 p-3';
            
            // PERBAIKAN: Menambahkan animasi CSS "ngeper" dan warna Tailwind hijau terang
            modalDiv.innerHTML = `
                <style>
                    @keyframes animasiNgeper {
                        0% { transform: scale(1); }
                        40% { transform: scale(0.85); }
                        75% { transform: scale(1.05); }
                        100% { transform: scale(1); }
                    }
                    .efek-ngeper {
                        animation: animasiNgeper 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    }
                    /* Class khusus saat tombol sudah terpilih (Warna Hijau) */
                    .feedback-type-btn.terpilih {
                        background-color: #22c55e !important; /* Tailwind green-500 */
                        border-color: #22c55e !important;
                        color: white !important;
                        transform: translateY(0) !important;
                        box-shadow: 0 4px 10px rgba(34, 197, 94, 0.4) !important;
                    }
                </style>
                <div class="bg-white rounded-[28px] max-w-lg w-full shadow-2xl transform transition-all scale-95 opacity-0" id="feedbackModalContent">
                    <div class="p-4 space-y-4">
                        <div class="rounded-[28px] bg-sabanaRed p-4 text-white"><div class="flex items-start gap-3"><div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/15 text-xl"><i class="fa-regular fa-star"></i></div><div><p class="text-[10px] uppercase tracking-[0.24em] text-white/80">Kirim Masukan</p><h3 class="mt-1 text-xl font-black">Beri tahu kami pendapatmu</h3><p class="mt-2 text-sm text-white/90">Pilih jenis masukan dan tulis pesan.</p></div></div></div>
                        
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" data-feedback-type="saran" class="feedback-type-btn flex items-center gap-3 rounded-xl border-2 border-blue-100 bg-blue-50 p-3.5 text-left font-bold text-blue-600 transition-all duration-300 hover:bg-blue-100 hover:border-blue-300 hover:-translate-y-1 hover:shadow-md focus:border-blue-500 focus:bg-blue-100 focus:ring-4 focus:ring-blue-100 focus:outline-none">
                                <i class="fa-solid fa-lightbulb text-xl w-6 text-center"></i> Saran
                            </button>
                            
                            <button type="button" data-feedback-type="kritik" class="feedback-type-btn flex items-center gap-3 rounded-xl border-2 border-orange-100 bg-orange-50 p-3.5 text-left font-bold text-orange-600 transition-all duration-300 hover:bg-orange-100 hover:border-orange-300 hover:-translate-y-1 hover:shadow-md focus:border-orange-500 focus:bg-orange-100 focus:ring-4 focus:ring-orange-100 focus:outline-none">
                                <i class="fa-solid fa-comment-dots text-xl w-6 text-center"></i> Kritik
                            </button>
                            
                            <button type="button" data-feedback-type="pujian" class="feedback-type-btn flex items-center gap-3 rounded-xl border-2 border-emerald-100 bg-emerald-50 p-3.5 text-left font-bold text-emerald-600 transition-all duration-300 hover:bg-emerald-100 hover:border-emerald-300 hover:-translate-y-1 hover:shadow-md focus:border-emerald-500 focus:bg-emerald-100 focus:ring-4 focus:ring-emerald-100 focus:outline-none">
                                <i class="fa-solid fa-heart text-xl w-6 text-center"></i> Pujian
                            </button>
                            
                            <button type="button" data-feedback-type="laporan_masalah" class="feedback-type-btn flex items-center gap-3 rounded-xl border-2 border-red-100 bg-red-50 p-3.5 text-left font-bold text-red-600 transition-all duration-300 hover:bg-red-100 hover:border-red-300 hover:-translate-y-1 hover:shadow-md focus:border-red-500 focus:bg-red-100 focus:ring-4 focus:ring-red-100 focus:outline-none">
                                <i class="fa-solid fa-triangle-exclamation text-xl w-6 text-center"></i> Laporan
                            </button>
                        </div>

                        <form id="feedbackForm"><textarea id="feedbackMessage" rows="3" class="w-full rounded-2xl border-2 border-gray-200 p-3 mt-1 focus:outline-none focus:border-sabanaRed transition-colors" placeholder="Tulis masukan..."></textarea><div class="flex gap-2 mt-3"><button type="button" id="cancelFeedbackBtn" class="btn-cancel font-medium rounded-3xl px-4 py-3 hover:bg-gray-100 transition-colors w-1/3">Batal</button><button type="submit" class="btn-submit-feedback font-bold rounded-3xl px-4 py-3 bg-sabanaRed text-white flex-1 hover:bg-red-700 hover:shadow-lg transition transform hover:-translate-y-0.5">Kirim</button></div></form>
                    </div>
                </div>
            `;
            document.body.appendChild(modalDiv);
            return modalDiv;
        }
        createFeedbackModal();
        const feedbackModal = document.getElementById('feedbackModal');
        const feedbackContent = document.getElementById('feedbackModalContent');
        const openFeedback = document.getElementById('openFeedbackBtn');
        const cancelFeedback = document.getElementById('cancelFeedbackBtn');
        
        function attachFeedbackTypeEvents() {
            document.querySelectorAll('.feedback-type-btn').forEach(btn => {
                btn.removeEventListener('click', feedbackTypeHandler);
                btn.addEventListener('click', feedbackTypeHandler);
            });
        }
        function feedbackTypeHandler(e) {
            const btn = e.currentTarget;
            document.querySelectorAll('.feedback-type-btn').forEach(b => {
                b.classList.remove('terpilih', 'efek-ngeper');
            });
            btn.classList.add('terpilih', 'efek-ngeper');
            setTimeout(() => {
                btn.classList.remove('efek-ngeper');
            }, 400); 
        }

        function openFeedbackModal() {
            if (feedbackModal) {
                feedbackModal.classList.remove('hidden');
                setTimeout(() => feedbackContent?.classList.remove('scale-95', 'opacity-0'), 10);
                attachFeedbackTypeEvents();
            }
        }
        function closeFeedbackModal() {
            feedbackContent?.classList.add('scale-95', 'opacity-0');
            setTimeout(() => feedbackModal?.classList.add('hidden'), 300);
            
            // Reset form dan pilihan tombol saat modal ditutup
            const feedbackForm = document.getElementById('feedbackForm');
            if (feedbackForm) feedbackForm.reset();
            document.querySelectorAll('.feedback-type-btn').forEach(b => {
                b.classList.remove('terpilih', 'efek-ngeper');
            });
        }

        if (openFeedback) openFeedback.onclick = openFeedbackModal;
        if (cancelFeedback) cancelFeedback.onclick = closeFeedbackModal;

        const feedbackFormElement = document.getElementById('feedbackForm');
        if (feedbackFormElement) {
            feedbackFormElement.onsubmit = async (e) => {
                e.preventDefault();
                const selectedBtn = document.querySelector('.feedback-type-btn.terpilih');
                if (!selectedBtn) {
                    showToast('Silakan pilih jenis masukan terlebih dahulu.', 'error');
                    return;
                }
                const jenis = selectedBtn.getAttribute('data-feedback-type');
                const pesan = document.getElementById('feedbackMessage').value.trim();
                if (!pesan) {
                    showToast('Masukan tidak boleh kosong.', 'error');
                    return;
                }
                try {
                    const response = await fetch('../api/send_feedback.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ jenis, pesan })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast('Terima kasih atas masukannya!', 'success');
                        closeFeedbackModal();
                    } else {
                        showToast(result.message || 'Gagal mengirim masukan.', 'error');
                    }
                } catch (err) {
                    showToast('Error: ' + err.message, 'error');
                }
            };
        }
        if (feedbackModal) {
            feedbackModal.addEventListener('click', (e) => {
                if (e.target === feedbackModal) closeFeedbackModal();
            });
        }
        if (faqModal) {
            faqModal.addEventListener('click', (e) => {
                if (e.target === faqModal) {
                    faqContent?.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => faqModal.classList.add('hidden'), 300);
                }
            });
        }
    }

    // ========== NAVIGASI & POLLING ==========
    async function loadContent(menu) {
        currentMenu = menu;
        if (menu === 'pesanan-saya') await renderPesananSaya();
        else if (menu === 'riwayat') await renderRiwayat();
        else if (menu === 'edit-profil') renderEditProfil();
        else if (menu === 'bantuan') renderBantuan();
        await updateStats();
    }

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
            if (currentMenu === 'pesanan-saya' && pollingActive) {
                renderPesananSaya();
                updateStats();
            } else if (currentMenu === 'riwayat') {
                renderRiwayat();
                updateStats();
            }
        }, 5000);
    }

    const sidebarItems = document.querySelectorAll('.nav-item');
    sidebarItems.forEach(item => {
        if (item.id === 'logoutBtn') return;
        item.addEventListener('click', (e) => {
            e.preventDefault();
            sidebarItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            loadContent(item.dataset.menu);
            item.classList.add('nav-item-clicked');
            setTimeout(() => item.classList.remove('nav-item-clicked'), 350);
        });
    });

    const defaultActive = document.querySelector('.nav-item[data-menu="edit-profil"]');
    if (defaultActive) {
        defaultActive.classList.add('active');
        loadContent('edit-profil');
    } else {
        loadContent('edit-profil');
    }

    startPolling();

    // ========== LOGOUT MODAL ==========
    function createLogoutModal() {
        if (document.getElementById('logoutModal')) return;
        const modalDiv = document.createElement('div');
        modalDiv.id = 'logoutModal';
        modalDiv.className = 'fixed inset-0 bg-black/60 z-[10002] flex items-center justify-center hidden transition-all duration-300 p-4';
        modalDiv.innerHTML = `
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all scale-95 opacity-0" id="logoutModalContent">
                <div class="p-8"><div class="flex justify-center mb-4"><div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center"><i class="fa-solid fa-sign-out-alt text-2xl text-orange-600"></i></div></div><h3 class="text-xl font-bold text-gray-800 text-center mb-2">Konfirmasi Logout</h3><p class="text-gray-500 text-center mb-6">Apakah Anda yakin ingin logout?</p><div class="flex gap-3"><button id="logoutNo" class="flex-1 px-4 py-2 rounded-lg font-medium transition">Tidak</button><button id="logoutYes" class="flex-1 px-4 py-2 bg-sabanaRed text-white rounded-lg font-medium transition hover:bg-red-700">Iya, Logout</button></div></div>
            </div>`;
        document.body.appendChild(modalDiv);
        return modalDiv;
    }
    let logoutModal = document.getElementById('logoutModal');
    if (!logoutModal) logoutModal = createLogoutModal();
    const logoutModalContent = document.getElementById('logoutModalContent');
    const logoutYes = document.getElementById('logoutYes');
    const logoutNo = document.getElementById('logoutNo');
    function openLogoutModal() {
        logoutModal.classList.remove('hidden');
        setTimeout(() => logoutModalContent?.classList.remove('scale-95', 'opacity-0'), 10);
    }
    function closeLogoutModal() {
        logoutModalContent?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => logoutModal.classList.add('hidden'), 300);
    }
    if (logoutBtn) logoutBtn.addEventListener('click', (e) => { e.preventDefault(); openLogoutModal(); });
    if (logoutYes) logoutYes.addEventListener('click', () => logoutForm.submit());
    if (logoutNo) logoutNo.addEventListener('click', closeLogoutModal);

    // ========== DROPDOWN & HAMBURGER ==========
    const menuDropdownBtn = document.getElementById('menuDropdownBtn');
    const menuDropdownContent = document.getElementById('menuDropdownContent');
    const menuArrow = document.getElementById('menuArrow');
    if (menuDropdownBtn && menuDropdownContent) {
        menuDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menuDropdownContent.classList.toggle('hidden');
            if (menuArrow) menuArrow.classList.toggle('rotate');
        });
        document.addEventListener('click', (e) => {
            if (!menuDropdownBtn.contains(e.target)) {
                menuDropdownContent.classList.add('hidden');
                if (menuArrow) menuArrow.classList.remove('rotate');
            }
        });
    }
    const hamburger = document.getElementById('hamburgerMenu');
    const navContainer = document.getElementById('navContainer');
    if (hamburger && navContainer) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navContainer.classList.toggle('active');
        });
        navContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navContainer.classList.remove('active');
            });
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
    }
    // ========== MODAL KONFIRMASI HAPUS RIWAYAT ==========
    function createDeleteRiwayatModal() {
        if (document.getElementById('deleteRiwayatModal')) return;
        const modalDiv = document.createElement('div');
        modalDiv.id = 'deleteRiwayatModal';
        modalDiv.className = 'fixed inset-0 bg-black/60 z-[10005] flex items-center justify-center hidden transition-all duration-300 p-4';
        modalDiv.innerHTML = `
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl transform transition-all scale-95 opacity-0" id="deleteRiwayatModalContent">
                <div class="p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-5">
                        <i class="fa-solid fa-trash-can text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 mb-2">Hapus Riwayat?</h3>
                    <p class="text-gray-500 mb-6 text-sm">Pesanan ini akan dihapus dari tampilan riwayat. <span class="font-semibold text-sabanaRed">Rating yang sudah Anda berikan tidak akan terhapus.</span></p>
                    <div class="flex gap-3">
                        <button id="cancelDeleteRiwayat" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl font-bold transition-colors">Tidak</button>
                        <button id="confirmDeleteRiwayat" class="flex-1 px-4 py-3 bg-sabanaRed text-white hover:bg-red-700 rounded-xl font-bold transition-all shadow-md hover:shadow-lg active:scale-95">Ya, Hapus</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modalDiv);
        return modalDiv;
    }

    let deleteRiwayatModal = document.getElementById('deleteRiwayatModal');
    if (!deleteRiwayatModal) deleteRiwayatModal = createDeleteRiwayatModal();
    const deleteRiwayatModalContent = document.getElementById('deleteRiwayatModalContent');
    const cancelDeleteRiwayat = document.getElementById('cancelDeleteRiwayat');
    const confirmDeleteRiwayat = document.getElementById('confirmDeleteRiwayat');
    let orderIdToDelete = null;

    function openDeleteRiwayatModal(id) {
        orderIdToDelete = id;
        deleteRiwayatModal.classList.remove('hidden');
        setTimeout(() => deleteRiwayatModalContent?.classList.remove('scale-95', 'opacity-0'), 10);
    }

    function closeDeleteRiwayatModal() {
        deleteRiwayatModalContent?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => deleteRiwayatModal.classList.add('hidden'), 300);
        orderIdToDelete = null;
    }

    if (cancelDeleteRiwayat) cancelDeleteRiwayat.addEventListener('click', closeDeleteRiwayatModal);
    if (deleteRiwayatModal) deleteRiwayatModal.addEventListener('click', (e) => {
        if (e.target === deleteRiwayatModal) closeDeleteRiwayatModal();
    });
    
    if (confirmDeleteRiwayat) {
        confirmDeleteRiwayat.addEventListener('click', async () => {
            if (!orderIdToDelete) return;
            
            const originalText = confirmDeleteRiwayat.innerHTML;
            confirmDeleteRiwayat.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Proses...';
            confirmDeleteRiwayat.disabled = true;

            try {
                const response = await fetch('../api/delete_riwayat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderIdToDelete })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Riwayat berhasil dihapus', 'success');
                    closeDeleteRiwayatModal();
                    await renderRiwayat(); // Render ulang riwayat
                    await updateStats();   // Update angka statistik
                } else {
                    showToast(result.message || 'Gagal hapus', 'error');
                    closeDeleteRiwayatModal();
                }
            } catch (err) {
                showToast('Error: ' + err.message, 'error');
                closeDeleteRiwayatModal();
            } finally {
                confirmDeleteRiwayat.innerHTML = originalText;
                confirmDeleteRiwayat.disabled = false;
            }
        });
    }
    // ========== MODAL KONFIRMASI BATALKAN PESANAN (USER) ==========
    function createCancelOrderUserModal() {
        if (document.getElementById('cancelOrderUserModal')) return;
        const modalDiv = document.createElement('div');
        modalDiv.id = 'cancelOrderUserModal';
        modalDiv.className = 'fixed inset-0 bg-black/60 z-[10005] flex items-center justify-center hidden transition-all duration-300 p-4';
        
        modalDiv.innerHTML = `
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl transform transition-all scale-95 opacity-0" id="cancelOrderUserModalContent">
                <div class="p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-5">
                        <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 mb-2">Batalkan Pesanan?</h3>
                    <p class="text-gray-500 mb-6 text-sm">Apakah Anda yakin ingin membatalkan pesanan ini? Stok pesanan akan otomatis dikembalikan.</p>
                    <div class="flex gap-3">
                        <button id="cancelOrderNoBtn" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl font-bold transition-colors">Tidak</button>
                        <button id="cancelOrderYesBtn" class="flex-1 px-4 py-3 bg-sabanaRed text-white hover:bg-red-700 rounded-xl font-bold transition-all shadow-md hover:shadow-lg active:scale-95">Ya, Batalkan</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modalDiv);
        return modalDiv;
    }

    let cancelOrderUserModal = document.getElementById('cancelOrderUserModal');
    if (!cancelOrderUserModal) cancelOrderUserModal = createCancelOrderUserModal();
    const cancelOrderUserModalContent = document.getElementById('cancelOrderUserModalContent');
    const cancelOrderNoBtn = document.getElementById('cancelOrderNoBtn');
    const cancelOrderYesBtn = document.getElementById('cancelOrderYesBtn');
    let orderIdToCancelUser = null;

    // Fungsi untuk memunculkan modal
    function openCancelOrderUserModal(id) {
        orderIdToCancelUser = id;
        cancelOrderUserModal.classList.remove('hidden');
        setTimeout(() => cancelOrderUserModalContent?.classList.remove('scale-95', 'opacity-0'), 10);
    }

    // Fungsi untuk menyembunyikan modal
    function closeCancelOrderUserModal() {
        cancelOrderUserModalContent?.classList.add('scale-95', 'opacity-0');
        setTimeout(() => cancelOrderUserModal.classList.add('hidden'), 300);
        orderIdToCancelUser = null;
    }

    // Jika klik "Tidak" atau klik di luar kotak, tutup modal
    if (cancelOrderNoBtn) cancelOrderNoBtn.addEventListener('click', closeCancelOrderUserModal);
    if (cancelOrderUserModal) {
        cancelOrderUserModal.addEventListener('click', (e) => {
            if (e.target === cancelOrderUserModal) closeCancelOrderUserModal();
        });
    }

    // Jika klik "Ya, Batalkan", eksekusi API
    if (cancelOrderYesBtn) {
        cancelOrderYesBtn.addEventListener('click', async () => {
            if (!orderIdToCancelUser) return;
            
            const originalText = cancelOrderYesBtn.innerHTML;
            cancelOrderYesBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Proses...';
            cancelOrderYesBtn.disabled = true;

            try {
                const response = await fetch('../api/cancel_order_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderIdToCancelUser })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Pesanan berhasil dibatalkan!', 'success');
                    closeCancelOrderUserModal();
                    await loadContent(currentMenu); // Render ulang layar secara instan
                } else {
                    showToast(result.message || 'Gagal membatalkan pesanan', 'error');
                    closeCancelOrderUserModal();
                }
            } catch (err) {
                showToast('Error: ' + err.message, 'error');
                closeCancelOrderUserModal();
            } finally {
                cancelOrderYesBtn.innerHTML = originalText;
                cancelOrderYesBtn.disabled = false;
            }
        });
    }
});
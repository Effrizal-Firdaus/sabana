// ==========================================
// STATE MANAGEMENT & KONFIGURASI
// ==========================================
let allRawReviews = []; // Menyimpan semua data asli dari API
let filteredReviews = []; // Menyimpan data setelah difilter & di-search
let menuListCaches = new Set(); // Menyimpan daftar menu unik

// State Kontrol (Point 2 & 4)
let currentPage = 1;
const itemsPerPage = 8; // Jumlah ulasan yang tampil per halaman
let currentSearch = '';
let currentSort = 'terbaru';
let currentFilterMenu = 'semua';

// DOM Elements
const ulasanContainer = document.getElementById('ulasanContainer');
const searchInput = document.getElementById('searchUlasan');
const sortSelect = document.getElementById('sortUlasan');
const filterMenuSelect = document.getElementById('filterMenuUlasan');

// ==========================================
// KONTROL EVENT LISTENER
// ==========================================
searchInput.addEventListener('input', (e) => {
    currentSearch = e.target.value.toLowerCase();
    currentPage = 1; // Kembali ke halaman 1 saat mencari
    updateUI();
});

sortSelect.addEventListener('change', (e) => {
    currentSort = e.target.value;
    currentPage = 1;
    updateUI();
});

filterMenuSelect.addEventListener('change', (e) => {
    currentFilterMenu = e.target.value;
    currentPage = 1;
    updateUI();
});

document.getElementById('btnPrevPage').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        updateUI(true); // true = abaikan kalkulasi analitik agar performa enteng
    }
});

document.getElementById('btnNextPage').addEventListener('click', () => {
    const maxPage = Math.ceil(filteredReviews.length / itemsPerPage);
    if (currentPage < maxPage) {
        currentPage++;
        updateUI(true);
    }
});


// ==========================================
// FUNGSI UTAMA (UPDATE UI)
// ==========================================
function updateUI(skipAnalytics = false) {
    if(allRawReviews.length === 0) {
        renderKosong();
        return;
    }

    // 1. Ekstrak Daftar Menu (Untuk Filter Dropdown)
    populateMenuFilter();

    // 2. Kalkulasi Analitik dari Data Asli (Point 1)
    if (!skipAnalytics) {
        renderAnalytics(allRawReviews);
    }

    // 3. Proses Search & Filter (Point 2)
    filteredReviews = allRawReviews.filter(item => {
        const matchSearch = item.pengguna.toLowerCase().includes(currentSearch) || 
                            item.pesanan_id.toString().includes(currentSearch);
        const matchMenu = currentFilterMenu === 'semua' || item.nama_menu === currentFilterMenu;
        return matchSearch && matchMenu;
    });

    // 4. Proses Sorting (Point 2)
    filteredReviews.sort((a, b) => {
        if (currentSort === 'terbaru') return new Date(b.dibuat_pada) - new Date(a.dibuat_pada);
        if (currentSort === 'terlama') return new Date(a.dibuat_pada) - new Date(b.dibuat_pada);
        if (currentSort === 'tertinggi') return b.rating - a.rating;
        if (currentSort === 'terendah') return a.rating - b.rating;
    });

    // 5. Proses Pagination (Point 4)
    const totalItems = filteredReviews.length;
    const maxPage = Math.ceil(totalItems / itemsPerPage) || 1;
    
    // Keamanan jika filter membuat halaman out of bounds
    if (currentPage > maxPage) currentPage = maxPage;
    
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedItems = filteredReviews.slice(startIndex, endIndex);

    // 6. Render Ulasan & Info Halaman
    renderUlasanList(paginatedItems);
    updatePaginationInfo(totalItems, startIndex, endIndex);
}

// ==========================================
// POINT 1: RENDER RINGKASAN ANALITIK
// ==========================================
function renderAnalytics(data) {
    let totalScore = 0;
    let counts = {5: 0, 4: 0, 3: 0, 2: 0, 1: 0};
    
    data.forEach(item => {
        totalScore += parseInt(item.rating);
        counts[item.rating] = (counts[item.rating] || 0) + 1;
    });

    const totalReviews = data.length;
    const avgScore = totalReviews > 0 ? (totalScore / totalReviews).toFixed(1) : '0.0';

    // Update Angka dan Bintang Rata-Rata
    document.getElementById('totalUlasan').innerText = totalReviews;
    document.getElementById('avgRatingScore').innerText = avgScore;
    
    let starsHtml = '';
    const avgNum = parseFloat(avgScore);
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(avgNum)) starsHtml += `<i class="fa-solid fa-star"></i>`;
        else if (i === Math.ceil(avgNum) && !Number.isInteger(avgNum)) starsHtml += `<i class="fa-solid fa-star-half-stroke"></i>`;
        else starsHtml += `<i class="fa-regular fa-star"></i>`;
    }
    document.getElementById('avgRatingStars').innerHTML = starsHtml;

    // Render Bar Chart (5 sampai 1 Bintang)
    let barsHtml = '';
    [5, 4, 3, 2, 1].forEach(star => {
        const count = counts[star];
        const percentage = totalReviews > 0 ? (count / totalReviews) * 100 : 0;
        let colorClass = 'bg-yellow-400';
        if(star <= 2) colorClass = 'bg-rose-400'; // Beri warna merah untuk rating jelek
        
        barsHtml += `
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-gray-500 w-4">${star}</span>
                <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full ${colorClass} rounded-full progress-bar-fill" style="width: ${percentage}%"></div>
                </div>
                <span class="text-xs font-bold text-gray-400 w-8 text-right">${count}</span>
            </div>
        `;
    });
    document.getElementById('ratingDistribution').innerHTML = barsHtml;
}

// ==========================================
// HELPER & RENDER HTML
// ==========================================
function populateMenuFilter() {
    let currentCount = menuListCaches.size;
    allRawReviews.forEach(item => menuListCaches.add(item.nama_menu));
    
    // Hanya re-render dropdown jika ada menu baru (agar fokus dropdown tidak hilang saat terbuka)
    if(menuListCaches.size > currentCount) {
        let html = `<option value="semua">Semua Menu</option>`;
        Array.from(menuListCaches).sort().forEach(menu => {
            // Tetap pilih yang sedang aktif
            const selected = (currentFilterMenu === menu) ? 'selected' : '';
            html += `<option value="${menu}" ${selected}>${menu}</option>`;
        });
        filterMenuSelect.innerHTML = html;
    }
}

function updatePaginationInfo(totalItems, start, end) {
    const infoText = document.getElementById('pageInfo');
    const btnPrev = document.getElementById('btnPrevPage');
    const btnNext = document.getElementById('btnNextPage');
    
    if (totalItems === 0) {
        infoText.innerText = "Menampilkan 0 Ulasan";
        btnPrev.disabled = true;
        btnNext.disabled = true;
        return;
    }

    let actualEnd = end > totalItems ? totalItems : end;
    infoText.innerText = `Menampilkan ${start + 1} - ${actualEnd} dari ${totalItems} Ulasan`;

    btnPrev.disabled = currentPage === 1;
    btnNext.disabled = currentPage >= Math.ceil(totalItems / itemsPerPage);
}

function renderUlasanList(data) {
    if (data.length === 0) {
        renderKosong();
        return;
    }
    
    let html = '';
    data.forEach(item => {
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<i class="fa-${i <= item.rating ? 'solid' : 'regular'} fa-star rating-star"></i>`;
        }
        
        // Desain Komentar Premium
        const komentarHtml = item.komentar && item.komentar.trim() !== '' 
            ? `<div class="mt-4 bg-slate-50/50 p-4 rounded-xl text-gray-600 text-sm italic border border-gray-100 shadow-inner relative">
                   <i class="fa-solid fa-quote-left absolute -top-2 -left-2 text-2xl text-emerald-500/20"></i>
                   <p class="relative z-10 font-medium ml-2">“${escapeHtml(item.komentar)}”</p>
               </div>`
            : `<div class="mt-4 text-xs text-gray-400 font-medium bg-gray-50 inline-block px-3 py-1.5 rounded-lg border border-gray-100">Tidak meninggalkan komentar tertulis.</div>`;
        
        html += `
            <div class="p-6 md:p-8 hover:bg-emerald-50/20 transition-all duration-300">
                <div class="flex flex-col md:flex-row gap-5 md:gap-6">
                    <!-- Avatar Lingkaran Besar -->
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex-shrink-0 flex items-center justify-center font-black text-2xl text-white shadow-md shadow-emerald-500/20 border-2 border-white">
                        ${escapeHtml(item.pengguna.charAt(0).toUpperCase())}
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex flex-wrap justify-between items-start gap-2 mb-2">
                            <div>
                                <h4 class="font-extrabold text-lg text-gray-800 leading-none">${escapeHtml(item.pengguna)}</h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-blue-100">#${item.pesanan_id}</span>
                                    <span class="text-xs font-bold text-gray-400"><i class="fa-solid fa-utensils text-gray-300 mr-1"></i> ${escapeHtml(item.nama_menu)}</span>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100"><i class="fa-regular fa-clock mr-1"></i> ${formatDate(item.dibuat_pada)}</div>
                        </div>
                        
                        <div class="flex items-center gap-2 mt-3">
                            <div class="flex gap-1 text-sm">${starsHtml}</div>
                            <span class="text-xs font-bold text-gray-500 ml-1">(${item.rating}/5)</span>
                        </div>
                        
                        ${komentarHtml}
                    </div>
                </div>
            </div>
        `;
    });
    ulasanContainer.innerHTML = html;
}

function renderKosong() {
    ulasanContainer.innerHTML = `
        <div class="p-16 text-center text-gray-400 flex flex-col items-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fa-regular fa-face-frown text-3xl text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-600 mb-1">Data Tidak Ditemukan</h4>
            <p class="text-sm">Tidak ada ulasan yang sesuai dengan pencarian atau filter Anda.</p>
        </div>`;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('id-ID', { 
        day: '2-digit', month: 'short', year: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    }).replace(/\./g, ':');
}

// ==========================================
// POLLING (API FETCH)
// ==========================================
function fetchDataUlasan() {
    fetch('api/load_ulasan.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching data:', data.error);
                return;
            }
            
            // Simpan data asli ke state global
            allRawReviews = data;
            
            // Panggil Fungsi Master (Akan mengkalkulasi semuanya otomatis)
            updateUI();
        })
        .catch(error => console.error('Koneksi Gagal:', error));
}

// Modal Logout Logic
document.addEventListener('DOMContentLoaded', () => {
    const btnTrigger = document.getElementById('btnTriggerLogout');
    const modal = document.getElementById('logoutModal');
    const modalBox = document.getElementById('logoutModalBox');
    const cancelBtn = document.getElementById('btnCancelLogout');
    const overlay = document.getElementById('logoutOverlay');

    function showModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function hideModal() {
        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
    
    if (btnTrigger) btnTrigger.addEventListener('click', (e) => { e.preventDefault(); showModal(); });
    if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
    if (overlay) overlay.addEventListener('click', hideModal);

    // Initial Load & Interval
    fetchDataUlasan();
    setInterval(fetchDataUlasan, 5000);
});
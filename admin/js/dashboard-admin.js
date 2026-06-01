// ====== Admin Dashboard ======
let currentFilter = 'disiapkan';

const urlParams = new URLSearchParams(window.location.search);
const filterParam = urlParams.get('filter');
if (filterParam && ['disiapkan','dimasak','dikirim','sampai','selesai'].includes(filterParam)) {
    currentFilter = filterParam;
}

async function fetchData(status) {
    try {
        const response = await fetch(`api/get_dashboard_data.php?status=${status}`);
        const data = await response.json();
        if (data.error) throw new Error(data.error);
        return data;
    } catch (err) {
        console.error('Gagal mengambil data:', err);
        return null;
    }
}

function updateStatsAndBadges(stats, badges) {
    document.getElementById('totalOrders').innerText = stats.totalOrders;
    document.getElementById('totalCustomers').innerText = stats.totalCustomers;
    document.getElementById('totalMenuItems').innerText = stats.totalMenuItems;
    
    // =========================================================
    // LOGIKA MODE BACA (100% AKURAT DARI DATABASE)
    // =========================================================
    const totalOmzet = stats.totalRevenue || 0;
    const qrisRevenue = stats.qrisRevenue || 0;
    const cashRevenue = stats.cashRevenue || 0;

    // Cek riwayat setoran manual dari kasir di memori browser
    let simulatedSetoran = parseInt(localStorage.getItem('sabana_setoran')) || 0;

    // Proteksi: Setoran tidak mungkin melebihi uang fisik yang diterima
    if (simulatedSetoran > cashRevenue) {
        simulatedSetoran = cashRevenue;
        localStorage.setItem('sabana_setoran', simulatedSetoran);
    }

    // Kalkulasi Real-time
    const kasDiLaci = cashRevenue - simulatedSetoran;
    const saldoBank = qrisRevenue + simulatedSetoran;

    // Render ke UI
    const elTotalRev = document.getElementById('totalRevenue');
    const elSaldoBank = document.getElementById('saldoBank');
    const elKasBelum = document.getElementById('kasBelumDisetor');

    if(elTotalRev) elTotalRev.innerText = `Rp ${totalOmzet.toLocaleString('id-ID')}`;
    if(elSaldoBank) elSaldoBank.innerText = `Rp ${saldoBank.toLocaleString('id-ID')}`;
    if(elKasBelum) elKasBelum.innerText = `Rp ${kasDiLaci.toLocaleString('id-ID')}`;

    // Logika Tombol "Setorkan ke Bank"
    const btnSetor = document.getElementById('btnTriggerSetor');
    if (btnSetor) {
        if (kasDiLaci <= 0) {
            btnSetor.classList.add('opacity-50', 'cursor-not-allowed');
            btnSetor.classList.remove('hover:bg-emerald-600', 'active:scale-95');
            btnSetor.disabled = true;
        } else {
            btnSetor.classList.remove('opacity-50', 'cursor-not-allowed');
            btnSetor.classList.add('hover:bg-emerald-600', 'active:scale-95');
            btnSetor.disabled = false;
        }
    }
    // =========================================================

    for (let s in badges) {
        let badge = document.getElementById(`badge-${s}`);
        if (badge) badge.innerText = badges[s];
    }
}

function renderOrders(orders) {
    const container = document.getElementById('ordersContainer');
    if (!orders.length) {
        container.innerHTML = `<div class="col-span-full text-center text-gray-400 py-16">
            <i class="fa-solid fa-file-circle-check text-5xl mb-4 opacity-30"></i>
            <p>Tidak ada pesanan di tahap ini.</p>
        </div>`;
        return;
    }
    let html = '';
    orders.forEach(order => {
        const itemsHtml = order.items.map(item => `
            <div class="flex justify-between text-sm text-gray-600 mb-2 gap-3">
                <div class="flex gap-2 flex-1"><span class="font-bold">${item.qty}x</span> <span>${item.name}</span></div>
                <span class="font-semibold">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
            </div>
        `).join('');
        
        let displayStatus = order.status;
        if (displayStatus === 'diterima') displayStatus = 'sampai';
        
        let statusColor = {
            disiapkan: 'orange-500',
            dimasak: 'yellow-500',
            dikirim: 'blue-500',
            sampai: 'green-500',
            selesai: 'gray-500'
        }[displayStatus] || 'gray-500';
        
        html += `
            <div class="modern-card p-6 flex flex-col justify-between bg-white rounded-2xl border border-gray-100 hover:shadow-xl transition-all">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-3 items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-emerald-600 font-bold border">${order.customer.charAt(0)}</div>
                            <div><h3 class="text-lg font-extrabold">${order.customer}</h3><p class="text-[10px] text-gray-400">ID: ${order.id}</p></div>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white uppercase shadow-sm bg-${statusColor}">${displayStatus}</span>
                    </div>
                    <div class="bg-gray-50/80 rounded-xl p-4 border">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase mb-3">Item Pesanan</p>
                        ${itemsHtml}
                        <div class="mt-4 pt-3 border-t border-dashed flex justify-between">
                            <p class="text-[10px] font-extrabold">Metode Pembayaran</p>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase flex items-center gap-1.5 ${order.payment === 'QRIS' ? 'bg-sky-100 text-sky-600' : 'bg-orange-100 text-orange-600'}">
                                <i class="fa-solid ${order.payment === 'QRIS' ? 'fa-qrcode' : 'fa-money-bill-wave'}"></i> ${order.payment}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="border-t-2 border-dashed border-gray-100 mb-4"></div>
                    <div class="flex justify-between items-end">
                        <div><p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Total</p><p class="text-xl font-black text-[#e11d48]">Rp ${order.total.toLocaleString('id-ID')}</p></div>
                        <button data-id="${order.rawId}" data-status="${order.status}" class="action-btn bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-[11px] font-extrabold uppercase shadow-md transition flex items-center gap-2 active:scale-95">
                            ${order.status === 'selesai' ? '<i class="fa-solid fa-box-archive"></i> Arsipkan' : 'Konfirmasi <i class="fa-solid fa-arrow-right"></i>'}
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const status = btn.dataset.status;
            if (status === 'selesai') {
                selectedOrderIdForArchive = id;
                openArsipModal();
            } else {
                await updateOrder(id, 'next');
            }
        });
    });
}

async function updateOrder(orderId, action) {
    if (action === 'archive') {
        try {
            const response = await fetch('api/arsipkan_pesanan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            });
            const result = await response.json();
            
            if (result.success) {
                showToast('✅ Pesanan berhasil diarsipkan', 'success');
                await loadDashboard(currentFilter);
            } else {
                showToast('Gagal: ' + (result.message || 'Terjadi kesalahan'), 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        }
    } 
    else {
        const formData = new FormData();
        formData.append('id', orderId);
        formData.append('action', action);
        try {
            const response = await fetch('api/update_order_status.php', { method: 'POST', body: formData });
            const result = await response.json();
            
            if (result.success) {
                showToast(`✅ Pesanan berhasil diupdate ke ${result.newStatus === 'diterima' ? 'sampai' : result.newStatus}`, 'success');
                await loadDashboard(currentFilter);
            } else {
                showToast('Gagal: ' + (result.message || 'Terjadi kesalahan'), 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        }
    }
}

async function loadDashboard(status) {
    const data = await fetchData(status);
    if (data) {
        updateStatsAndBadges(data.stats, data.badges);
        renderOrders(data.orders);
    }
}

document.querySelectorAll('.status-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        currentFilter = btn.dataset.status;
        document.querySelectorAll('.status-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        loadDashboard(currentFilter);
    });
});

document.querySelectorAll('.status-filter-btn').forEach(btn => {
    if (btn.dataset.status === currentFilter) btn.classList.add('active');
    else btn.classList.remove('active');
});
loadDashboard(currentFilter);

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================================
    // MODAL LOGOUT
    // ==========================================================
    const btnTriggerLogout = document.getElementById('btnTriggerLogout');
    const modalLogout = document.getElementById('logoutModal');
    const boxLogout = document.getElementById('logoutModalBox');
    const cancelLogout = document.getElementById('btnCancelLogout');
    const overlayLogout = document.getElementById('logoutOverlay');
    
    function showLogout() { 
        modalLogout.classList.remove('hidden'); 
        setTimeout(() => { 
            boxLogout.classList.remove('scale-95','opacity-0'); 
            boxLogout.classList.add('scale-100','opacity-100'); 
        }, 10); 
    }
    function hideLogout() { 
        boxLogout.classList.remove('scale-100','opacity-100'); 
        boxLogout.classList.add('scale-95','opacity-0'); 
        setTimeout(() => modalLogout.classList.add('hidden'), 300); 
    }
    if (btnTriggerLogout) btnTriggerLogout.addEventListener('click', (e) => { e.preventDefault(); showLogout(); });
    if (cancelLogout) cancelLogout.addEventListener('click', hideLogout);
    if (overlayLogout) overlayLogout.addEventListener('click', hideLogout);

    // ==========================================================
    // MODAL SETOR TUNAI & LOGIKA DINAMIS
    // ==========================================================
    const btnTriggerSetor = document.getElementById('btnTriggerSetor');
    const modalSetor = document.getElementById('modalSetorTunai');
    const modalContentSetor = document.getElementById('modalSetorContent');
    const overlaySetor = document.getElementById('setorOverlay');
    const btnBatalSetor1 = document.getElementById('btnBatalSetor');
    const btnBatalSetor2 = document.getElementById('btnBatalSetorX');
    const btnKonfirmasiSetor = document.getElementById('btnKonfirmasiSetor');
    const inputNominal = document.getElementById('inputNominalSetor');
    const errorText = document.getElementById('errorSetor');
    const displayMaksimalKas = document.getElementById('displayMaksimalKas');
    
    let maksimalKas = 0;

    // Buka Pop-up & Ambil Data Real-Time
    if (btnTriggerSetor) {
        btnTriggerSetor.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            // Ambil teks dari dashboard (misal: "Rp 24.000") dan ubah jadi angka murni (24000)
            let kasText = document.getElementById('kasBelumDisetor').innerText;
            maksimalKas = parseInt(kasText.replace(/[^0-9]/g, ''), 10);
            if (isNaN(maksimalKas)) maksimalKas = 0;
            
            displayMaksimalKas.innerText = kasText;
            
            modalSetor.classList.remove('hidden');
            setTimeout(() => {
                modalContentSetor.classList.remove('scale-95', 'opacity-0');
                modalContentSetor.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            inputNominal.value = ''; 
            errorText.classList.add('hidden');
        });
    }

    // Tutup Pop-up
    function tutupModalSetor() {
        modalContentSetor.classList.add('scale-95', 'opacity-0');
        modalContentSetor.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => modalSetor.classList.add('hidden'), 300);
    }
    
    if(btnBatalSetor1) btnBatalSetor1.addEventListener('click', tutupModalSetor);
    if(btnBatalSetor2) btnBatalSetor2.addEventListener('click', tutupModalSetor);
    if(overlaySetor) overlaySetor.addEventListener('click', tutupModalSetor);

    // Validasi & Simpan Nominal yang diketik
    if(btnKonfirmasiSetor) {
        btnKonfirmasiSetor.addEventListener('click', function() {
            const nominal = parseInt(inputNominal.value);

            if (isNaN(nominal) || nominal <= 0) {
                errorText.textContent = "Masukkan nominal setor yang valid!";
                errorText.classList.remove('hidden');
                return;
            }

            if (nominal > maksimalKas) {
                errorText.textContent = "Nominal melebihi Kas Belum Disetor!";
                errorText.classList.remove('hidden');
                return;
            }

            errorText.classList.add('hidden');
            
            // Simpan nominal spesifik ke memori lokal
            let currentSetoran = parseInt(localStorage.getItem('sabana_setoran')) || 0;
            localStorage.setItem('sabana_setoran', currentSetoran + nominal);
            
            if (typeof showToast === 'function') {
                showToast('✅ Berhasil menyetorkan Rp ' + nominal.toLocaleString('id-ID') + ' ke Bank', 'success');
            } else {
                alert('Berhasil disetorkan ke Bank!');
            }
            
            loadDashboard(currentFilter);
            tutupModalSetor();
        });
    }
});

let selectedOrderIdForArchive = null;
const arsipModal = document.getElementById('arsipModal');
const arsipModalBox = document.getElementById('arsipModalBox');
const btnCancelArsip = document.getElementById('btnCancelArsip');
const btnConfirmArsip = document.getElementById('btnConfirmArsip');
const arsipOverlay = document.getElementById('arsipOverlay');

function openArsipModal() {
    if (!arsipModal) return;
    arsipModal.classList.remove('hidden');
    setTimeout(() => {
        arsipModalBox.classList.remove('scale-95', 'opacity-0');
        arsipModalBox.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeArsipModal() {
    if (!arsipModalBox) return;
    arsipModalBox.classList.remove('scale-100', 'opacity-100');
    arsipModalBox.classList.add('scale-95', 'opacity-0');
    setTimeout(() => arsipModal.classList.add('hidden'), 300);
    selectedOrderIdForArchive = null;
}

if (btnCancelArsip) btnCancelArsip.addEventListener('click', closeArsipModal);
if (arsipOverlay) arsipOverlay.addEventListener('click', closeArsipModal);

if (btnConfirmArsip) {
    btnConfirmArsip.addEventListener('click', async () => {
        if (selectedOrderIdForArchive) {
            const originalText = btnConfirmArsip.innerHTML;
            btnConfirmArsip.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Proses...';
            btnConfirmArsip.disabled = true;
            
            await updateOrder(selectedOrderIdForArchive, 'archive');
            
            btnConfirmArsip.innerHTML = originalText;
            btnConfirmArsip.disabled = false;
            closeArsipModal();
        }
    });
}
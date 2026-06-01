document.addEventListener('DOMContentLoaded', function() {
    const btnFilter = document.getElementById('btnFilter');
    const tglMulai = document.getElementById('tglMulai');
    const tglSelesai = document.getElementById('tglSelesai');
    const tipeLaporan = document.getElementById('tipeLaporan');
    const container = document.getElementById('laporanContainer');

    let currentChartInstance = null;

    const today = new Date();
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(today.getMonth() - 1);
    tglSelesai.value = today.toISOString().slice(0,10);
    tglMulai.value = oneMonthAgo.toISOString().slice(0,10);

    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#64748b'; 
    Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b'; 
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: '600' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };

    function getMenuColor(namaMenu) {
        const name = namaMenu.toLowerCase();
        if (name.includes('pcs') || name.includes('combo')) return '#a855f7'; 
        if (name.includes('paket') || name.includes('+')) return '#ef4444'; 
        if (name.includes('ayam goreng')) return '#f97316'; 
        return '#3b82f6'; 
    }

    async function loadLaporan() {
        const mulai = tglMulai.value;
        const selesai = tglSelesai.value;
        const tipe = tipeLaporan.value;
        
        if (!mulai || !selesai) {
            if (typeof showToast === 'function') showToast('Pilih tanggal mulai dan selesai', 'error');
            return;
        }
        
        container.innerHTML = `
            <div class="text-center text-gray-400 py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <i class="fa-solid fa-circle-notch fa-spin text-4xl text-emerald-500 mb-3"></i>
                <p class="text-sm font-medium text-gray-500">Menghimpun data analitik...</p>
            </div>`;
        
        try {
            const response = await fetch(`api/get_laporan.php?tipe=${tipe}&mulai=${mulai}&selesai=${selesai}`);
            const data = await response.json();
            
            if (data.success) {
                renderLaporan(data, tipe);
            } else {
                container.innerHTML = `<div class="text-center text-red-500 py-16">${data.message || 'Gagal memuat data'}</div>`;
            }
        } catch (err) {
            console.error("Error Detail:", err);
            container.innerHTML = `
                <div class="text-center text-red-500 py-16 bg-red-50 rounded-2xl border border-red-100 max-w-2xl mx-auto">
                    <i class="fa-solid fa-triangle-exclamation text-4xl mb-3"></i><br>
                    <strong class="text-lg block mb-1 text-red-700">Gagal Memuat Analitik</strong>
                    <span class="text-sm text-red-600/80">${err.message}</span>
                </div>`;
        }
    }

    function renderLaporan(data, tipe) {
        window.currentLaporanData = data.laporan;
        if (currentChartInstance) {
            currentChartInstance.destroy();
            currentChartInstance = null;
        }

        const hasData = Array.isArray(data.laporan) && data.laporan.length > 0;

        if (tipe === 'penjualan') {
            let html = `
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Analisis Pendapatan Penjualan</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Grafik pergerakan omzet harian toko</p>
                    </div>
                </div>
                
                <div class="mb-10 bg-white p-6 border border-gray-100 rounded-2xl shadow-sm ${!hasData ? 'hidden' : ''}">
                    <div class="w-full relative h-[320px]">
                        <canvas id="laporanChart"></canvas>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm bg-white">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="p-4">Tanggal Transaksi</th>
                                <th class="p-4 text-center">Volume Pesanan</th>
                                <th class="p-4 text-right">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">`;
                        
            if (hasData) {
                data.laporan.forEach(row => {
                    html += `<tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-medium">${row.tanggal}</td>
                                <td class="p-4 text-center"><span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md font-medium text-xs">${row.jumlah} Order</span></td>
                                <td class="p-4 font-semibold text-emerald-600 text-right">Rp ${row.total.toLocaleString('id-ID')}</td>
                             </tr>`;
                });
            } else {
                html += `<tr><td colspan="3" class="p-12 text-center text-gray-400 italic">Tidak ada catatan penjualan pada rentang tanggal ini.</td></tr>`;
            }
            
            const totalKeseluruhan = data.total_keseluruhan || 0;
            html += `   </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end">
                    <div class="bg-white border border-gray-100 shadow-sm rounded-xl px-6 py-4 text-right">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wider block">Akumulasi Pendapatan</span>
                        <span class="text-2xl font-black text-emerald-600 mt-0.5 block">Rp ${totalKeseluruhan.toLocaleString('id-ID')}</span>
                    </div>
                </div>`;
            
            container.innerHTML = html;

            if (hasData) {
                const ctx = document.getElementById('laporanChart').getContext('2d');
                currentChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.laporan.map(row => row.tanggal),
                        datasets: [{
                            label: 'Pendapatan',
                            data: data.laporan.map(row => row.total),
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderColor: '#10b981', 
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.38
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [5, 5] },
                                ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        } 
        else if (tipe === 'produk') {
            let html = `
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Peringkat Produk Terlaris</h2>
                    <p class="text-xs text-gray-400 mt-0.5 mb-6">Perbandingan volume penjualan berdasarkan Kategori (Oranye: Reguler, Biru: Tambahan, Merah: Paket, Ungu: Combo)</p>
                </div>
                
                <div class="mb-10 bg-white p-6 border border-gray-100 rounded-2xl shadow-sm ${!hasData ? 'hidden' : ''}">
                    <div class="w-full relative h-[380px]">
                        <canvas id="laporanChart"></canvas>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm bg-white">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="p-4">Nama Menu</th>
                                <th class="p-4 text-center">Kuantitas Terjual</th>
                                <th class="p-4 text-right">Kontribusi Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">`;
                        
            if (hasData) {
                data.laporan.forEach(item => {
                    html += `<tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-semibold text-gray-800">${item.nama_menu}</td>
                                <td class="p-4 text-center"><span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">${item.total_terjual} Porsi</span></td>
                                <td class="p-4 text-gray-800 font-semibold text-right">Rp ${item.total_pendapatan.toLocaleString('id-ID')}</td>
                             </tr>`;
                });
            } else {
                html += `<tr><td colspan="3" class="p-12 text-center text-gray-400 italic">Belum ada item produk yang terjual.</td></tr>`;
            }
            html += `</tbody></table></div>`;
            
            container.innerHTML = html;

            if (hasData) {
                const ctx = document.getElementById('laporanChart').getContext('2d');
                const dynamicColors = data.laporan.map(item => getMenuColor(item.nama_menu));

                currentChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.laporan.map(item => item.nama_menu),
                        datasets: [{
                            data: data.laporan.map(item => item.total_terjual),
                            backgroundColor: dynamicColors,
                            hoverBackgroundColor: dynamicColors, 
                            borderRadius: 6, 
                            borderSkipped: false,
                            barThickness: 16 
                        }]
                    },
                    options: { 
                        indexAxis: 'y', 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [5, 5] } },
                            y: { grid: { display: false }, ticks: { font: { weight: '500' } } }
                        }
                    }
                });
            }
        } 
        else if (tipe === 'stok') {
            let html = `
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Status Kontrol Inventaris</h2>
                    <p class="text-xs text-gray-400 mt-0.5 mb-6">Monitoring sisa ketersediaan porsi menu</p>
                </div>
                
                <div class="mb-10 bg-white p-6 border border-gray-100 rounded-2xl shadow-sm ${!hasData ? 'hidden' : ''}">
                    <div class="w-full relative h-[320px]">
                        <canvas id="laporanChart"></canvas>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm bg-white">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50/80 text-gray-600 font-semibold border-b border-gray-100">
                            <tr>
                                <th class="p-4">Nama Menu</th>
                                <th class="p-4 text-center">Sisa Stok</th>
                                <th class="p-4 text-center">Status Barang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">`;
                        
            if (hasData) {
                data.laporan.forEach(menu => {
                    let statusBadge = '';
                    let textStyle = '';

                    if (menu.stok === 0) {
                        statusBadge = '<span class="bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Habis</span>';
                        textStyle = 'text-red-600 font-bold text-base';
                    } else if (menu.stok <= 5) {
                        statusBadge = '<span class="bg-orange-50 text-orange-600 px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-orange-100"><span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span> Hampir Habis</span>';
                        textStyle = 'text-orange-600 font-bold text-base';
                    } else {
                        statusBadge = '<span class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia</span>';
                        textStyle = 'text-emerald-600 font-semibold';
                    }
                    
                    html += `<tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 font-medium text-gray-800">${menu.nama_menu}</td>
                                <td class="p-4 text-center ${textStyle}">${menu.stok}</td>
                                <td class="p-4 text-center">${statusBadge}</td>
                             </tr>`;
                });
            } else {
                html += `<tr><td colspan="3" class="p-12 text-center text-gray-400 italic">Data inventaris tidak ditemukan.</td></tr>`;
            }
            html += `</tbody></table></div>`;
            
            container.innerHTML = html;

            if (hasData) {
                const ctx = document.getElementById('laporanChart').getContext('2d');
                
                const solidStatusColors = data.laporan.map(menu => {
                    if (menu.stok === 0) return '#ef4444'; 
                    if (menu.stok <= 5) return '#f97316';  
                    return '#10b981';                      
                });

                currentChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.laporan.map(menu => menu.nama_menu),
                        datasets: [{
                            data: data.laporan.map(menu => menu.stok),
                            backgroundColor: solidStatusColors,
                            borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 }, 
                            borderSkipped: false,
                            barThickness: 20
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { 
                            y: { 
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [5, 5] }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    }

    document.getElementById('exportExcelBtn').addEventListener('click', async () => {
        const tipe = tipeLaporan.value;
        const mulai = tglMulai.value;
        const selesai = tglSelesai.value;

        if (!currentChartInstance || !window.currentLaporanData || window.currentLaporanData.length === 0) {
            if (typeof showToast === 'function') showToast('Tidak ada data untuk diexport', 'error');
            else alert('Tidak ada data untuk diexport');
            return;
        }

        const btn = document.getElementById('exportExcelBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Excel...';
        btn.disabled = true;

        try {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Laporan Sabana');

            let columns = [];
            let rows = [];

            if (tipe === 'penjualan') {
                columns = ['Tanggal Transaksi', 'Volume Pesanan', 'Total Pendapatan (Rp)'];
                rows = window.currentLaporanData.map(item => [item.tanggal, item.jumlah, item.total]);
            } else if (tipe === 'produk') {
                columns = ['Nama Menu', 'Kuantitas Terjual', 'Kontribusi Omzet (Rp)'];
                rows = window.currentLaporanData.map(item => [item.nama_menu, item.total_terjual, item.total_pendapatan]);
            } else if (tipe === 'stok') {
                columns = ['Nama Menu', 'Sisa Stok'];
                rows = window.currentLaporanData.map(item => [item.nama_menu, item.stok]);
            }

            worksheet.addRow(columns);
            worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };
            worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF059669' } };

            rows.forEach(row => worksheet.addRow(row));
            worksheet.columns.forEach(column => { column.width = 25; });

            const base64Image = currentChartInstance.toBase64Image();
            const imageId = workbook.addImage({
                base64: base64Image,
                extension: 'png',
            });

            const startRowForImage = rows.length + 3; 
            worksheet.addImage(imageId, {
                tl: { col: 0, row: startRowForImage },
                ext: { width: 800, height: 400 } 
            });

            const buffer = await workbook.xlsx.writeBuffer();
            saveAs(new Blob([buffer]), `Laporan_Sabana_${tipe}_${mulai}_sd_${selesai}.xlsx`);
            
            if (typeof showToast === 'function') showToast('Berhasil mengunduh Excel!', 'success');
            
        } catch (err) {
            console.error("Gagal export excel:", err);
            if (typeof showToast === 'function') showToast('Gagal membuat file Excel', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    document.getElementById('exportPdfBtn').addEventListener('click', () => { window.print(); });
    btnFilter.addEventListener('click', loadLaporan);
    loadLaporan();
});
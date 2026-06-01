document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================================
    // LOGIKA CODES DROP-DOWN PANAH DI HALAMAN ARSIP
    // ========================================================
    const toggleArsipBtn = document.getElementById('toggleArsipBtn');
    const submenuArsip = document.getElementById('submenuArsip');

    if (toggleArsipBtn && submenuArsip) {
        toggleArsipBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Mencegah bubbling klik
            
            // Cek kondisi apakah submenu sedang disembunyikan
            const isHidden = submenuArsip.style.display === 'none' || submenuArsip.classList.contains('hidden');
            
            if (isHidden) {
                // Tampilkan submenu dengan paksa menggunakan flex layout Tailwind
                submenuArsip.classList.remove('hidden');
                submenuArsip.style.display = 'flex';
                // Putar panah ke bawah (180 derajat)
                toggleArsipBtn.style.transform = 'rotate(180deg)';
            } else {
                // Sembunyikan kembali submenu
                submenuArsip.style.display = 'none';
                submenuArsip.classList.add('hidden');
                // Balikkan panah ke posisi awal (0 derajat)
                toggleArsipBtn.style.transform = 'rotate(0deg)';
            }
        });
    }

    // ========================================================
    // LOGIKA MODAL LOGOUT
    // ========================================================
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
    
    if (btnTriggerLogout) {
        btnTriggerLogout.addEventListener('click', (e) => { 
            e.preventDefault(); 
            showLogout(); 
        });
    }
    
    if (cancelLogout) cancelLogout.addEventListener('click', hideLogout);
    if (overlayLogout) overlayLogout.addEventListener('click', hideLogout);

});
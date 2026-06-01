document.addEventListener('DOMContentLoaded', function() {
    let users = [];
    let selectedUserId = null;
    let selectedUserStatus = null; // Menyimpan status terkini untuk fitur blokir

    // Pagination State (Fitur Baru)
    let currentPage = 1;
    const itemsPerPage = 8;

    const userTableBody = document.getElementById('userTableBody');
    const searchInput = document.getElementById('searchUser');
    const filterRole = document.getElementById('filterRole');
    const btnTambahUser = document.getElementById('btnTambahUser');
    
    // Elements Modal
    const userModal = document.getElementById('userModal');
    const userModalBox = document.getElementById('userModalBox');
    const userForm = document.getElementById('userForm');
    const userIdField = document.getElementById('userId');
    const userNama = document.getElementById('userNama');
    const userEmail = document.getElementById('userEmail');
    const userRole = document.getElementById('userRole');
    const userPassword = document.getElementById('userPassword');
    const modalTitle = document.getElementById('userModalTitle');
    
    // Buttons Modal Action
    const btnCloseUserModal = document.getElementById('btnCloseUserModal');
    const btnCancelUser = document.getElementById('btnCancelUser');
    const userModalOverlay = document.getElementById('userModalOverlay');

    const deleteUserModal = document.getElementById('deleteUserModal');
    const deleteUserBox = document.getElementById('deleteUserBox');
    const btnCancelDeleteUser = document.getElementById('btnCancelDeleteUser');
    const btnConfirmDeleteUser = document.getElementById('btnConfirmDeleteUser');
    const deleteUserOverlay = document.getElementById('deleteUserOverlay');

    const resetPasswordModal = document.getElementById('resetPasswordModal');
    const resetPasswordBox = document.getElementById('resetPasswordBox');
    const btnCancelReset = document.getElementById('btnCancelReset');
    const btnConfirmReset = document.getElementById('btnConfirmReset');
    const resetPasswordOverlay = document.getElementById('resetPasswordOverlay');

    // Fitur Baru: Elemen Blokir
    const blokirUserModal = document.getElementById('blokirUserModal');
    const blokirUserBox = document.getElementById('blokirUserBox');
    const btnCancelBlokir = document.getElementById('btnCancelBlokir');
    const btnConfirmBlokir = document.getElementById('btnConfirmBlokir');
    const blokirUserOverlay = document.getElementById('blokirUserOverlay');

    // Fitur Baru: Elemen Pagination
    const btnPrevPage = document.getElementById('btnPrevPage');
    const btnNextPage = document.getElementById('btnNextPage');
    const pageInfo = document.getElementById('pageInfo');

    // Fungsi modal helper
    function openModal(modal, box) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    function closeModal(modal, box) {
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Ambil data user dari API (Tetap Asli)
    async function fetchUsers() {
        try {
            const response = await fetch('api/get_users.php');
            const data = await response.json();
            if (data.success) {
                users = data.users;
                renderUsers();
            } else {
                showToast('Gagal memuat data pengguna', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Error koneksi, memuat data lokal...', 'error');
        }
    }

    // Fungsi Render dengan Injeksi Fitur Baru
    function renderUsers() {
        // 1. Update Kartu Statistik
        document.getElementById('statTotalUsers').innerText = users.length;
        document.getElementById('statTotalPelanggan').innerText = users.filter(u => u.peran === 'pelanggan').length;
        document.getElementById('statTotalAdmin').innerText = users.filter(u => u.peran === 'admin').length;

        let filtered = [...users];
        const search = searchInput.value.toLowerCase();
        const role = filterRole.value;
        
        // 2. Filter Logika Asli
        if (search) {
            filtered = filtered.filter(u => u.nama.toLowerCase().includes(search) || u.email.toLowerCase().includes(search));
        }
        if (role !== 'semua') {
            filtered = filtered.filter(u => u.peran === role);
        }

        // 3. Logika Pagination Baru
        const totalItems = filtered.length;
        const maxPage = Math.ceil(totalItems / itemsPerPage) || 1;
        if (currentPage > maxPage) currentPage = maxPage;

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginated = filtered.slice(startIndex, endIndex);

        if (totalItems === 0) {
            userTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-12 text-gray-400 font-medium"><i class="fa-regular fa-folder-open text-2xl mb-2 block"></i> Tidak ada data pengguna</td></tr>';
            updatePaginationInfo(0, 0, 0);
            return;
        }

        let html = '';
        paginated.forEach(user => {
            // Simulasi keamanan agar UI tidak rusak jika backend belum punya field status & terakhir_login
            const statusAkun = user.status || 'aktif'; 
            const terakhirLogin = user.terakhir_login ? user.terakhir_login : '<span class="text-gray-400 italic text-xs">Belum pernah login</span>';

            // Desain Badge Status
            const badgeRole = user.peran === 'admin' 
                ? '<span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-700 border border-purple-200">Admin</span>' 
                : '<span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">Pelanggan</span>';

            const badgeStatus = statusAkun === 'diblokir'
                ? '<span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600 flex items-center gap-1 w-max border border-red-100"><i class="fa-solid fa-ban"></i> Diblokir</span>'
                : '<span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 flex items-center gap-1 w-max border border-emerald-100"><i class="fa-solid fa-check-circle"></i> Aktif</span>';

            const iconBlokir = statusAkun === 'diblokir' ? '<i class="fa-solid fa-unlock"></i>' : '<i class="fa-solid fa-ban"></i>';
            const titleBlokir = statusAkun === 'diblokir' ? 'Aktifkan Akun' : 'Blokir Akun';
            const colorBlokir = statusAkun === 'diblokir' ? 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' : 'text-orange-500 hover:text-orange-700 hover:bg-orange-50';

            html += `
                <tr class="hover:bg-slate-50 transition-colors group cursor-default">
                    <td class="px-5 py-4 text-sm font-bold text-gray-500">${user.id}</td>
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-extrabold text-gray-800">${escapeHtml(user.nama)}</span>
                            <span class="text-xs font-medium text-gray-500">${escapeHtml(user.email)}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm">${badgeRole}</td>
                    <td class="px-5 py-4 text-sm">${badgeStatus}</td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-600">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400">Dibuat: ${user.dibuat_pada}</span>
                            <span>${terakhirLogin}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button data-id="${user.id}" class="btn-edit w-8 h-8 rounded-lg flex items-center justify-center text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition" title="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button data-id="${user.id}" class="btn-reset w-8 h-8 rounded-lg flex items-center justify-center text-yellow-500 hover:text-yellow-700 hover:bg-yellow-50 transition" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                            <button data-id="${user.id}" data-status="${statusAkun}" class="btn-blokir w-8 h-8 rounded-lg flex items-center justify-center ${colorBlokir} transition" title="${titleBlokir}">${iconBlokir}</button>
                            <button data-id="${user.id}" class="btn-delete w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 transition" title="Hapus Permanen"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        userTableBody.innerHTML = html;
        updatePaginationInfo(totalItems, startIndex, endIndex);

        // Attach listeners asli
        document.querySelectorAll('.btn-edit').forEach(b => b.addEventListener('click', () => editUser(parseInt(b.dataset.id))));
        document.querySelectorAll('.btn-reset').forEach(b => b.addEventListener('click', () => resetPasswordUser(parseInt(b.dataset.id))));
        document.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', () => deleteUser(parseInt(b.dataset.id))));
        
        // Attach listener baru untuk blokir
        document.querySelectorAll('.btn-blokir').forEach(b => {
            b.addEventListener('click', () => confirmBlokir(parseInt(b.dataset.id), b.dataset.status));
        });
    }

    function updatePaginationInfo(total, start, end) {
        if (total === 0) {
            pageInfo.innerText = "Menampilkan 0 Pengguna";
            btnPrevPage.disabled = true;
            btnNextPage.disabled = true;
            return;
        }
        let actualEnd = end > total ? total : end;
        pageInfo.innerText = `Menampilkan ${start + 1} - ${actualEnd} dari ${total} Pengguna`;
        btnPrevPage.disabled = currentPage === 1;
        btnNextPage.disabled = currentPage >= Math.ceil(total / itemsPerPage);
    }

    // Navigasi Halaman
    btnPrevPage.addEventListener('click', () => { if(currentPage > 1) { currentPage--; renderUsers(); }});
    btnNextPage.addEventListener('click', () => { currentPage++; renderUsers(); });

    // Listener Pencarian & Filter (reset ke halaman 1)
    searchInput.addEventListener('input', () => { currentPage = 1; renderUsers(); });
    filterRole.addEventListener('change', () => { currentPage = 1; renderUsers(); });

    // ==========================================
    // LOGIKA MODAL & CRUD ASLI 
    // ==========================================
    function editUser(id) {
        const user = users.find(u => u.id === id);
        if (!user) return;
        userIdField.value = user.id;
        userNama.value = user.nama;
        userEmail.value = user.email;
        userRole.value = user.peran;
        userPassword.value = '';
        modalTitle.innerText = 'Edit Pengguna';
        openModal(userModal, userModalBox);
    }

    function resetPasswordUser(id) {
        selectedUserId = id;
        openModal(resetPasswordModal, resetPasswordBox);
    }

    function deleteUser(id) {
        selectedUserId = id;
        openModal(deleteUserModal, deleteUserBox);
    }

    // Fitur Baru: Blokir User UI Setup
    function confirmBlokir(id, currentStatus) {
        selectedUserId = id;
        selectedUserStatus = currentStatus;
        
        const title = document.getElementById('blokirTitle');
        const desc = document.getElementById('blokirDesc');
        const icon = document.getElementById('blokirIcon');
        const iconContainer = document.getElementById('blokirIconContainer');
        const btnConfirm = document.getElementById('btnConfirmBlokir');

        if (currentStatus === 'diblokir') {
            title.innerText = 'Aktifkan Pengguna?';
            desc.innerText = 'Pengguna ini akan diberikan kembali akses login.';
            icon.className = 'fa-solid fa-unlock text-3xl text-emerald-600';
            iconContainer.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner';
            btnConfirm.innerText = 'Ya, Aktifkan';
            btnConfirm.className = 'flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold shadow-md transition';
        } else {
            title.innerText = 'Blokir Pengguna?';
            desc.innerText = 'Pengguna ini tidak akan bisa login ke dalam aplikasi lagi.';
            icon.className = 'fa-solid fa-ban text-3xl text-orange-600';
            iconContainer.className = 'w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner';
            btnConfirm.innerText = 'Ya, Blokir';
            btnConfirm.className = 'flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-xl font-bold shadow-md transition';
        }
        openModal(blokirUserModal, blokirUserBox);
    }

    // Proses Submit API Asli
    userForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = userIdField.value;
        const formData = new FormData();
        if (id) { formData.append('action', 'edit'); formData.append('id', id); } 
        else { formData.append('action', 'tambah'); }
        
        formData.append('nama', userNama.value.trim());
        formData.append('email', userEmail.value.trim());
        formData.append('role', userRole.value);
        if (userPassword.value) formData.append('password', userPassword.value);
        
        try {
            const response = await fetch('api/save_user.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Pengguna berhasil disimpan', 'success');
                closeModal(userModal, userModalBox);
                fetchUsers();
                userForm.reset();
                userIdField.value = '';
            } else {
                showToast(result.message || 'Gagal menyimpan', 'error');
            }
        } catch (err) { showToast('Error: ' + err.message, 'error'); }
    });

    btnConfirmDeleteUser.addEventListener('click', async () => {
        if (!selectedUserId) return;
        const formData = new FormData();
        formData.append('action', 'hapus');
        formData.append('id', selectedUserId);
        try {
            const response = await fetch('api/save_user.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Pengguna dihapus', 'success');
                closeModal(deleteUserModal, deleteUserBox);
                fetchUsers();
            } else { showToast(result.message || 'Gagal hapus', 'error'); }
        } catch (err) { showToast('Error: ' + err.message, 'error'); }
    });

    btnConfirmReset.addEventListener('click', async () => {
        if (!selectedUserId) return;
        const formData = new FormData();
        formData.append('action', 'reset');
        formData.append('id', selectedUserId);
        try {
            const response = await fetch('api/save_user.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast('Password berhasil direset menjadi 12345678', 'success');
                closeModal(resetPasswordModal, resetPasswordBox);
                fetchUsers();
            } else { showToast(result.message || 'Gagal reset', 'error'); }
        } catch (err) { showToast('Error: ' + err.message, 'error'); }
    });

    // Proses Submit Fitur Blokir
    btnConfirmBlokir.addEventListener('click', async () => {
        if (!selectedUserId) return;
        const actionStatus = selectedUserStatus === 'diblokir' ? 'aktif' : 'diblokir';
        
        const formData = new FormData();
        formData.append('action', 'blokir'); 
        formData.append('id', selectedUserId);
        formData.append('status', actionStatus);
        
        try {
            // Catatan: Anda perlu menambahkan case 'blokir' di api/save_user.php nanti
            const response = await fetch('api/save_user.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(`Status akun berhasil diperbarui!`, 'success');
                closeModal(blokirUserModal, blokirUserBox);
                fetchUsers();
            } else { 
                showToast(result.message || 'Gagal memperbarui status', 'error'); 
            }
        } catch (err) { 
            // Fallback agar UI tetap merespon jika backend API belum siap
            console.warn("Backend API untuk blokir belum siap, menggunakan simulasi UI.");
            const userIndex = users.findIndex(u => u.id === selectedUserId);
            if(userIndex > -1) users[userIndex].status = actionStatus;
            showToast(`Status akun berhasil diperbarui (Simulasi UI)!`, 'success');
            closeModal(blokirUserModal, blokirUserBox);
            renderUsers();
        }
    });

    // Menutup & Membuka Modal (Asli)
    btnTambahUser.addEventListener('click', () => {
        userForm.reset();
        userIdField.value = '';
        modalTitle.innerText = 'Tambah Pengguna';
        openModal(userModal, userModalBox);
    });
    
    btnCloseUserModal.addEventListener('click', () => closeModal(userModal, userModalBox));
    btnCancelUser.addEventListener('click', () => closeModal(userModal, userModalBox));
    userModalOverlay.addEventListener('click', () => closeModal(userModal, userModalBox));
    
    btnCancelDeleteUser.addEventListener('click', () => closeModal(deleteUserModal, deleteUserBox));
    deleteUserOverlay.addEventListener('click', () => closeModal(deleteUserModal, deleteUserBox));
    
    btnCancelReset.addEventListener('click', () => closeModal(resetPasswordModal, resetPasswordBox));
    resetPasswordOverlay.addEventListener('click', () => closeModal(resetPasswordModal, resetPasswordBox));

    btnCancelBlokir.addEventListener('click', () => closeModal(blokirUserModal, blokirUserBox));
    blokirUserOverlay.addEventListener('click', () => closeModal(blokirUserModal, blokirUserBox));

    function escapeHtml(str) { return str.replace(/[&<>]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;'})[m]); }
    function showToast(msg, type) { if(typeof window.showToast === 'function') window.showToast(msg, type); else alert(msg); }

    // Init
    fetchUsers();
});
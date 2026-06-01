// ====== Unified Login Script (Admin & User) with Auto-Hide Alert ======

function handleLogin(event) {
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value.trim();
  if (!email || !password) {
    event.preventDefault();
    showAlert('Email dan password wajib diisi.', false);
    return false;
  }
}

let alertTimeout;

function showAlert(msg, success) {
  const alertBox = document.getElementById('alertBox');
  if (!alertBox) return;
  if (alertTimeout) clearTimeout(alertTimeout);
  
  alertBox.textContent = msg;
  alertBox.classList.add('show');
  
  // Jika pakai style.css Anda, alert.success bisa mengubah warna menjadi hijau
  alertBox.classList.toggle('success', !!success);
  
  alertTimeout = setTimeout(() => {
    alertBox.classList.remove('show');
    alertBox.textContent = '';
    alertTimeout = null;
  }, 4000);
}

window.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get('error');
  const success = urlParams.get('success');

  // Menangkap notifikasi dari register.php
  if (success === 'registered') {
    showAlert('Registrasi berhasil! Silakan login dengan akun Anda.', true);
    const url = new URL(window.location.href);
    url.searchParams.delete('success');
    window.history.replaceState({}, document.title, url);
  }

  // Menangkap semua kemungkinan error dari login.php
  if (error) {
    let message = '';
    switch(error) {
      case 'not_registered': 
        message = 'Data Anda belum terdaftar. Silakan registrasi terlebih dahulu.'; 
        break;
      case 'not_found': 
        message = 'Akun dengan email tersebut tidak ditemukan!'; 
        break;
      case 'wrong_password': 
        message = 'Password yang Anda masukkan salah. Silakan coba lagi.'; 
        break;
      case 'empty_fields': 
      case 'empty':
        message = 'Email dan password wajib diisi.'; 
        break;
        
      // ==========================================
      // [FITUR BARU]: PENANGANAN PESAN ERROR BLOKIR
      // ==========================================
      case 'blocked':
        message = 'Maaf, Akun Anda telah ditangguhkan/diblokir oleh Admin.';
        break;
      // ==========================================
        
      default: 
        message = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    }
    
    showAlert(message, false);
    
    // Membersihkan URL setelah alert muncul
    const url = new URL(window.location.href);
    url.searchParams.delete('error');
    window.history.replaceState({}, document.title, url);
  }

  // ===== TOGGLE PASSWORD VISIBILITY =====
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('loginPassword');
  
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  }
});
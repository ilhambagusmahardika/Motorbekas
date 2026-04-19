/**
 * Utility JS - JualMotor
 * Currency formatting, toast notifications, search filter
 */

// === FORMAT RUPIAH (titik separator) ===
function formatRupiah(angka) {
  const num = String(angka).replace(/\D/g, '');
  if (!num) return '';
  return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function unformatRupiah(str) {
  return String(str).replace(/\./g, '');
}

// Auto-format semua input dengan class "rupiah-input"
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.rupiah-input').forEach(function(el) {
    // Create hidden input for raw value
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = el.name;
    el.name = el.name + '_display';
    el.parentNode.insertBefore(hidden, el.nextSibling);

    el.addEventListener('input', function() {
      const raw = unformatRupiah(this.value);
      this.value = formatRupiah(raw);
      hidden.value = raw;
    });

    // Init if already has value
    if (el.value) {
      const raw = unformatRupiah(el.value);
      el.value = formatRupiah(raw);
      hidden.value = raw;
    }
  });
});

// === TOAST NOTIFICATION ===
function showToast(message, type = 'success') {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add('show'), 10);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// === SEARCH / FILTER TABLE ===
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  
  input.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  });
}

// === PREVIEW FOTO ===
function previewFoto(inputId, previewId) {
  const input = document.getElementById(inputId);
  if (!input) return;

  input.addEventListener('change', function() {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
}

// === WhatsApp Share ===
function shareWhatsApp(text) {
  const url = 'https://wa.me/?text=' + encodeURIComponent(text);
  window.open(url, '_blank');
}

// === SWEETALERT CONFIRM DELETE ===
function confirmDelete(ev, url, textMsg = "Data ini mungkin tidak bisa dikembalikan!") {
  ev.preventDefault();
  Swal.fire({
    title: 'Hapus data?',
    text: textMsg,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}

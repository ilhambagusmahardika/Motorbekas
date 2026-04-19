<?php
$pageTitle = "Transaksi - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Get motors available for sale (Siap Jual) & calculate total modal
$motorStok = mysqli_query($conn, "SELECT m.motor_id, m.merek, m.model, m.plat_nomor, m.harga_beli,
  COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan 
  FROM motor m WHERE m.status_motor = 'Siap Jual' ORDER BY m.merek");

// Get sales history
$penjualanList = mysqli_query($conn, "SELECT p.*, m.merek, m.model, m.plat_nomor, m.harga_beli, m.foto_motor,
  COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan
  FROM penjualan p JOIN motor m ON p.motor_id = m.motor_id ORDER BY p.created_at DESC");
?>

<div class="page-header">
  <h2><i class="ph ph-wallet" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Transaksi Penjualan</h2>
  <p>Catat penjualan motor</p>
</div>

<?php if ($success): ?>
  <div class="alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<script>document.addEventListener('DOMContentLoaded',function(){<?php if($success): ?>showToast('<?= addslashes($success) ?>');<?php endif; ?>});</script>

<div class="glass-card">
  <h3>Form Penjualan Motor</h3>
  <form action="simpan.php" method="POST">
    <div class="form-grid">
      <div class="form-group">
        <label>Pilih Motor dari Stok</label>
        <select name="motor_id" id="pilih_motor" required>
          <option value="" data-modal="0">-- Pilih Motor --</option>
          <?php while ($m = mysqli_fetch_assoc($motorStok)): 
            $modalMotor = $m['harga_beli'] + $m['total_perbaikan'];
          ?>
            <option value="<?= $m['motor_id'] ?>" data-modal="<?= $modalMotor ?>">
              <?= htmlspecialchars($m['merek'].' '.$m['model'].' — '.$m['plat_nomor']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Tanggal Jual</label>
        <input type="date" name="tgl_jual" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="form-group">
        <label>Nama Pembeli</label>
        <input type="text" name="nama_pembeli" placeholder="Nama pembeli motor" required>
      </div>
      <div class="form-group">
        <label>Harga Jual (Rp)</label>
        <input type="text" name="harga_jual" class="rupiah-input" placeholder="Contoh: 8.000.000" required>
      </div>
      <div class="form-group">
        <label>Dijual Melalui</label>
        <select name="dijual_melalui" required>
          <option value="">-- Pilih --</option>
          <option value="Facebook">Facebook</option>
          <option value="WhatsApp">WhatsApp</option>
          <option value="Instagram">Instagram</option>
          <option value="Marketplace">Marketplace</option>
          <option value="OLX">OLX</option>
          <option value="Langsung">Langsung / Offline</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <div class="form-group" style="grid-column: 1 / -1; margin-top: 5px;">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
          <input type="checkbox" id="izin_rugi" value="1" style="width:16px; height:16px;">
          <span style="font-weight:normal; color:var(--text-secondary);">Izinkan Jual Rugi (Abaikan Validasi Modal)</span>
        </label>
      </div>
      <?php endif; ?>
    </div>
    <div class="mt-2">
      <button type="submit" class="btn btn-success">Simpan Transaksi</button>
    </div>
  </form>
</div>

<!-- Riwayat Penjualan -->
<div class="glass-card">
  <h3>Riwayat Penjualan</h3>
  <div class="search-bar">
    <input type="text" id="searchTrx" placeholder="Cari motor, pembeli, plat...">
  </div>
  <div class="table-wrapper">
    <table id="trxTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Motor</th>
          <th>Plat</th>
          <th>Pembeli</th>
          <th>Harga Beli</th>
          <th>Perbaikan</th>
          <th>Modal</th>
          <th>Harga Jual</th>
          <th>Profit</th>
          <th>Via</th>
          <th style="text-align:center">Aksi / Nota</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($penjualanList) > 0): $no=1; ?>
          <?php while ($row = mysqli_fetch_assoc($penjualanList)):
            $modal = $row['harga_beli'] + $row['total_perbaikan'];
            $profit = $row['harga_jual'] - $modal;
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d/m/Y', strtotime($row['tgl_jual'])) ?></td>
            <td><strong><?= htmlspecialchars($row['merek'].' '.$row['model']) ?></strong></td>
            <td><?= htmlspecialchars($row['plat_nomor']) ?></td>
            <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
            <td>Rp <?= number_format($row['harga_beli'],0,',','.') ?></td>
            <td class="text-orange">Rp <?= number_format($row['total_perbaikan'],0,',','.') ?></td>
            <td>Rp <?= number_format($modal,0,',','.') ?></td>
            <td class="fw-bold">Rp <?= number_format($row['harga_jual'],0,',','.') ?></td>
            <td class="fw-bold <?= $profit >= 0 ? 'text-green' : 'text-red' ?>">
              Rp <?= number_format($profit,0,',','.') ?>
            </td>
            <td><span class="badge badge-new"><?= htmlspecialchars($row['dijual_melalui']) ?></span></td>
            <td style="text-align:center; white-space:nowrap;">
              <a href="nota.php?id=<?= $row['penjualan_id'] ?>" target="_blank" class="btn btn-outline btn-sm" title="Cetak Nota">
                <i class="ph ph-printer"></i>
              </a>
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <a href="hapus.php?id=<?= $row['penjualan_id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Batalkan transaksi ini? Motor akan dihapus dari riwayat penjualan dan otomatis kembali ke stok Siap Jual.')" title="Batalkan Transaksi">
                <i class="ph ph-trash"></i>
              </a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="12" class="text-center text-muted" style="padding:40px">
            <div class="empty-state">
              <div class="icon">🛒</div>
              <p>Belum ada transaksi penjualan</p>
            </div>
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  filterTable('searchTrx', 'trxTable');

  document.querySelector('form').addEventListener('submit', function(e) {
    const selector = document.getElementById('pilih_motor');
    const selectedOption = selector.options[selector.selectedIndex];
    const targetModal = parseInt(selectedOption.getAttribute('data-modal')) || 0;
    
    // Parse input harga_jual (text to number)
    const hargaJualInput = document.querySelector('input[name="harga_jual"]').value;
    const hargaJual = parseInt(hargaJualInput.replace(/\D/g, '')) || 0;
    
    // Cek apakah mode izinkan rugi dicentang (hanya admin)
    const isIzinRugi = document.getElementById('izin_rugi') !== null && document.getElementById('izin_rugi').checked;
    
    // Validasi kerugian
    if (!isIzinRugi && targetModal > 0 && hargaJual < targetModal) {
        e.preventDefault(); // Batalkan aksi simpan
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Peringatan Merugi!',
                html: 'Harga Jual <b>Rp ' + hargaJual.toLocaleString('id-ID') + '</b> lebih rendah dari Modal Motor <b>Rp ' + targetModal.toLocaleString('id-ID') + '</b>.<br>Transaksi ditolak untuk mencegah kerugian toko.',
                confirmButtonColor: '#ef4444' // red
            });
        } else {
            alert('Gagal! Harga Jual berada di bawah garis Modal Motor. Transaksi ditolak.');
        }
    }
  });
});
</script>

<?php include "../layout/footer.php"; ?>
<?php
$pageTitle = "Inventori Barang - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Get motors in inventory (status = Dibeli)
$motors = mysqli_query($conn, "SELECT m.*, 
  COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan
  FROM motor m WHERE m.status_motor = 'Dibeli' ORDER BY m.created_at DESC");

// If viewing detail
$detail = null;
$perbaikanList = null;
if (isset($_GET['detail'])) {
  $detailId = intval($_GET['detail']);
  $detail = mysqli_fetch_assoc(mysqli_query($conn, "SELECT m.*, 
    COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan
    FROM motor m WHERE m.motor_id = $detailId"));
  if ($detail) {
    $perbaikanList = mysqli_query($conn, "SELECT * FROM perbaikan WHERE motor_id = $detailId ORDER BY tgl_perbaikan DESC");
  }
}
?>

<div class="page-header">
  <h2> Inventori Barang</h2>
  <p>Kelola perbaikan motor sebelum dijual</p>
</div>

<?php if ($success): ?>
  <div style="background:rgba(0,214,143,0.1);border:1px solid rgba(0,214,143,0.3);color:var(--accent-green);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
      <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>

<?php if ($detail): ?>
<!-- Detail Motor -->
<div class="glass-card fade-in">
  <h3>  Detail Motor: <?= htmlspecialchars($detail['merek'].' '.$detail['model']) ?> — <?= htmlspecialchars($detail['plat_nomor']) ?></h3>
  
  <div class="form-grid" style="margin-bottom:20px">
    <div>
      <p class="text-muted mb-2">Informasi Motor</p>
      <table>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Merek/Model</td><td style="padding:6px 0"><strong><?= htmlspecialchars($detail['merek'].' '.$detail['model']) ?></strong></td></tr>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Kapasitas Mesin</td><td style="padding:6px 0"><?= $detail['cc'] ? '<span class="badge badge-info">'.$detail['cc'].'</span>' : '<span class="text-muted">-</span>' ?></td></tr>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Warna</td><td style="padding:6px 0"><?= htmlspecialchars($detail['warna']) ?></td></tr>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Tahun</td><td style="padding:6px 0"><?= $detail['tahun_pembuatan'] ?></td></tr>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Plat Nomor</td><td style="padding:6px 0"><strong><?= htmlspecialchars($detail['plat_nomor']) ?></strong></td></tr>
        <tr><td class="text-muted" style="padding:6px 16px 6px 0">Harga Beli</td><td style="padding:6px 0"><strong>Rp <?= number_format($detail['harga_beli'],0,',','.') ?></strong></td></tr>
      </table>
    </div>
    <div>
      <p class="text-muted mb-2">Kalkulasi Harga</p>
      <div class="card-summary blue" style="margin-bottom:12px">
        <div class="card-label">Harga Beli</div>
        <div class="card-value" style="font-size:20px">Rp <?= number_format($detail['harga_beli'],0,',','.') ?></div>
      </div>
      <div class="card-summary orange" style="margin-bottom:12px">
        <div class="card-label">Total Biaya Perbaikan</div>
        <div class="card-value" style="font-size:20px">Rp <?= number_format($detail['total_perbaikan'],0,',','.') ?></div>
      </div>
      <div class="card-summary green">
        <div class="card-label">Harga Siap Jual (Modal)</div>
        <div class="card-value" style="font-size:20px">Rp <?= number_format($detail['harga_beli'] + $detail['total_perbaikan'],0,',','.') ?></div>
      </div>
    </div>
  </div>

  <!-- Form tambah perbaikan -->
  <div style="background:var(--bg-input);border:1px solid var(--border-color);border-radius:12px;padding:24px;margin-bottom:20px">
    <h3 style="margin-bottom:16px;font-size:16px"> Tambah Biaya Perbaikan</h3>
    <form action="update.php" method="POST">
      <input type="hidden" name="motor_id" value="<?= $detail['motor_id'] ?>">
      <input type="hidden" name="action" value="tambah_perbaikan">
      <div class="form-grid">
        <div class="form-group">
          <label>Biaya Perbaikan (Rp)</label>
          <input type="text" name="biaya" id="biayaInput" class="rupiah-input" placeholder="Contoh: 150.000" required autocomplete="off">
        </div>
        <div class="form-group">
          <label>Tanggal Perbaikan</label>
          <input type="date" name="tgl_perbaikan" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group full-width">
          <label>Keterangan</label>
          <textarea name="keterangan" placeholder="Contoh: Ganti oli, servis mesin, ganti ban, dll" required></textarea>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-sm" id="btnSimpanPerbaikan">💾 Simpan Perbaikan</button>
    </form>
  </div>

  <!-- Riwayat perbaikan -->
  <?php if ($perbaikanList && mysqli_num_rows($perbaikanList) > 0): ?>
  <h3 style="font-size:16px;margin-bottom:12px">📝 Riwayat Perbaikan</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Keterangan</th>
          <th>Biaya</th>
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($pb = mysqli_fetch_assoc($perbaikanList)): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= date('d/m/Y', strtotime($pb['tgl_perbaikan'])) ?></td>
          <td><?= htmlspecialchars($pb['keterangan']) ?></td>
          <td>Rp <?= number_format($pb['biaya'],0,',','.') ?></td>
          <td style="text-align:center;white-space:nowrap">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <button class="btn btn-warning btn-sm" onclick="bukaEditPerbaikan(<?= $pb['perbaikan_id'] ?>, '<?= date('Y-m-d', strtotime($pb['tgl_perbaikan'])) ?>', '<?= addslashes(htmlspecialchars($pb['keterangan'])) ?>', <?= $pb['biaya'] ?>)" title="Edit"><i class="ph ph-pencil"></i></button>
            <a href="update.php?hapus_perbaikan=<?= $pb['perbaikan_id'] ?>&motor_id=<?= $detail['motor_id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus catatan perbaikan ini?')" title="Hapus"><i class="ph ph-trash"></i></a>
            <?php else: ?>
            <span class="text-muted"><i class="ph ph-lock-key"></i></span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div class="mt-3" style="display:flex;gap:12px">
    <a href="index.php" class="btn btn-outline">← Kembali</a>
    <form action="update.php" method="POST" style="display:inline">
      <input type="hidden" name="motor_id" value="<?= $detail['motor_id'] ?>">
      <input type="hidden" name="action" value="siap_jual">
      <button type="submit" class="btn btn-success"><i class="ph ph-check-circle"></i> Siap Jual / Ready</button>
    </form>
  </div>
</div>

<!-- Modal Edit Perbaikan -->
<div class="modal-overlay" id="modalEditPerbaikan">
  <div class="modal" style="max-width:480px">
    <button class="modal-close" onclick="tutupModal('modalEditPerbaikan')">&times;</button>
    <h3><i class="ph ph-pencil" style="color:var(--accent-orange)"></i> Edit Biaya Perbaikan</h3>
    <form action="update.php" method="POST">
      <input type="hidden" name="action" value="edit_perbaikan">
      <input type="hidden" name="motor_id" value="<?= $detail['motor_id'] ?>">
      <input type="hidden" name="perbaikan_id" id="editPerbaikanId">
      <div class="form-group">
        <label>Tanggal Perbaikan</label>
        <input type="date" name="tgl_perbaikan" id="editTgl" required>
      </div>
      <div class="form-group">
        <label>Biaya Perbaikan (Rp)</label>
        <input type="text" name="biaya" id="editBiayaDisplay" class="rupiah-input" placeholder="Contoh: 150.000" required autocomplete="off">
      </div>
      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" id="editKeterangan" required></textarea>
      </div>
      <div style="display:flex;gap:10px;margin-top:8px">
        <button type="submit" class="btn btn-warning"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
        <button type="button" class="btn btn-outline" onclick="tutupModal('modalEditPerbaikan')">Batal</button>
      </div>
    </form>
  </div>
</div>

<?php else: ?>
<!-- Daftar motor di inventori -->
<div class="glass-card fade-in">
  <h3> Motor di Inventori</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>Merek / Model</th>
          <th>Warna</th>
          <th>Plat Nomor</th>
          <th>Harga Beli</th>
          <th>Biaya Perbaikan</th>
          <th>Modal Total</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($motors) > 0): $no=1; ?>
          <?php while ($row = mysqli_fetch_assoc($motors)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td>
              <?php if ($row['foto_motor'] && file_exists($row['foto_motor'])): ?>
                <img src="../pembelian/<?= $row['foto_motor'] ?>" alt="foto" style="width:60px;height:45px;object-fit:cover;border-radius:6px">
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($row['merek']) ?></strong> <?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['warna']) ?></td>
            <td><strong><?= htmlspecialchars($row['plat_nomor']) ?></strong></td>
            <td>Rp <?= number_format($row['harga_beli'],0,',','.') ?></td>
            <td class="text-orange">Rp <?= number_format($row['total_perbaikan'],0,',','.') ?></td>
            <td class="fw-bold">Rp <?= number_format($row['harga_beli']+$row['total_perbaikan'],0,',','.') ?></td>
            <td>
              <a href="index.php?detail=<?= $row['motor_id'] ?>" class="btn btn-primary btn-sm">🔧 Detail</a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center text-muted" style="padding:40px">
            <div class="empty-state">
              <p>Belum ada motor di inventori<br><small>Motor yang baru dibeli akan muncul di sini</small></p>
            </div>
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<script>
// ── Modal Edit Perbaikan ────────────────────────────────
// app.js sudah handle .rupiah-input (auto-create hidden + rename display field)
// Kita cukup set nilai dan dispatch 'input' event agar hidden input ikut ter-update
function bukaEditPerbaikan(id, tgl, ket, biaya) {
  document.getElementById('editPerbaikanId').value = id;
  document.getElementById('editTgl').value = tgl;
  document.getElementById('editKeterangan').value = ket;

  // Set nilai display & trigger event agar app.js sync ke hidden input
  var displayEl = document.getElementById('editBiayaDisplay');
  // Cari hidden input yg dibuat app.js (nextSibling setelah display input)
  var hiddenEl = displayEl.parentNode.querySelector('input[type="hidden"]');
  var raw = String(biaya).replace(/\D/g, '');
  displayEl.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  if (hiddenEl) hiddenEl.value = raw;

  document.getElementById('modalEditPerbaikan').classList.add('active');
}

function tutupModal(id) {
  document.getElementById(id).classList.remove('active');
}
</script>

<?php include "../layout/footer.php"; ?>
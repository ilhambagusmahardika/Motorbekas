<?php
$pageTitle = "Pembelian Motor - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

// Flash message
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Get existing motors
$motors = mysqli_query($conn, "SELECT * FROM motor ORDER BY created_at DESC");

// Get master merek
$master_mereks = mysqli_query($conn, "SELECT nama_merek FROM master_merek ORDER BY nama_merek ASC");
?>

<div class="page-header">
  <h2><i class="ph ph-shopping-cart" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Pembelian Motor</h2>
  <p>Input data motor yang baru dibeli</p>
</div>

<?php if ($success): ?>
  <div style="background:rgba(0,214,143,0.1);border:1px solid rgba(0,214,143,0.3);color:var(--accent-green);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px;">
    <i class="ph ph-check-circle" style="font-size:20px;"></i> <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>

<div class="glass-card fade-in">
  <h3><i class="ph ph-plus-circle"></i> Tambah Data Motor</h3>
  <form action="simpan.php" method="POST" enctype="multipart/form-data">
    <div class="form-grid">
      <div class="form-group">
        <label>Tanggal Pembelian</label>
        <input type="date" name="tgl_pembelian" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="form-group">
        <label>Beli Dari (Nama Penjual)</label>
        <input type="text" name="nama_penjual" placeholder="Nama penjual motor" required>
      </div>
      <div class="form-group">
        <label>Merek</label>
        <select name="merek" id="merek" required>
          <option value="">-- Pilih Merek --</option>
          <?php while($m = mysqli_fetch_assoc($master_mereks)): ?>
            <option value="<?= htmlspecialchars($m['nama_merek']) ?>"><?= htmlspecialchars($m['nama_merek']) ?></option>
          <?php endwhile; ?>
          <option value="lainnya">Lainnya (Tulis Manual)</option>
        </select>
      </div>
      <div class="form-group" id="merek_lain_group" style="display:none">
        <label>Merek Lainnya</label>
        <input type="text" id="merek_lain" name="merek_lain" placeholder="Tulis merek">
      </div>
      <div class="form-group" id="model_group">
        <label>Model</label>
        <select name="model" id="model" required>
          <option value="">-- Pilih Model --</option>
        </select>
      </div>
      <div class="form-group" id="model_lain_group" style="display:none">
        <label>Model Lainnya</label>
        <input type="text" id="model_lain" name="model_lain" placeholder="Tulis model">
      </div>
      <div class="form-group">
        <label>Kapasitas Mesin (CC)</label>
        <select name="cc" required>
          <option value="">-- Pilih CC --</option>
          <option value="100cc">100 cc</option>
          <option value="110cc">110 cc</option>
          <option value="113cc">113 cc</option>
          <option value="115cc">115 cc</option>
          <option value="125cc">125 cc</option>
          <option value="150cc">150 cc</option>
          <option value="155cc">155 cc</option>
          <option value="160cc">160 cc</option>
          <option value="200cc">200 cc</option>
          <option value="250cc">250 cc</option>
          <option value="300cc">300 cc</option>
          <option value="400cc">400 cc</option>
          <option value="500cc">500 cc</option>
          <option value="650cc">650 cc</option>
          <option value="1000cc">1000 cc</option>
        </select>
      </div>
      <div class="form-group">
        <label>Warna</label>
        <input type="text" name="warna" placeholder="Warna motor" required>
      </div>
      <div class="form-group">
        <label>Tahun Pembuatan</label>
        <select name="tahun_pembuatan" required>
          <?php for($y=date('Y');$y>=1990;$y--): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Nama BPKB / STNK</label>
        <input type="text" name="nama_bpkb" placeholder="Atas nama BPKB/STNK" required>
      </div>
      <div class="form-group">
        <label>Plat Nomor</label>
        <input type="text" name="plat_nomor" id="plat" placeholder="B 1234 PQS" required>
      </div>
      <div class="form-group">
        <label>Asal Wilayah</label>
        <input type="text" name="asal_wilayah" id="wilayah" readonly placeholder="Otomatis">
      </div>
      <div class="form-group">
        <label>Asal Kota</label>
        <input type="text" name="asal_kota" id="kota" readonly placeholder="Otomatis">
      </div>
      <div class="form-group">
        <label>SAMSAT Terdaftar</label>
        <input type="text" name="samsat_terdaftar" id="samsat" readonly placeholder="Otomatis">
      </div>
      <div class="form-group">
        <label>No Rangka</label>
        <input type="text" name="no_rangka" placeholder="Nomor rangka" required>
      </div>
      <div class="form-group">
        <label>No Mesin</label>
        <input type="text" name="no_mesin" placeholder="Nomor mesin" required>
      </div>
      <div class="form-group">
        <label>Pajak Berlaku</label>
        <input type="date" name="pajak_berlaku" required>
      </div>
      <div class="form-group">
        <label>Foto Motor</label>
        <input type="file" name="foto_motor" accept="image/*" id="fotoInput">
        <img id="fotoPreview" src="" alt="preview" style="display:none;margin-top:8px;max-width:200px;border-radius:8px;border:1px solid var(--border-color)">
      </div>
      <div class="form-group">
        <label>Harga Beli (Rp)</label>
        <input type="text" name="harga_beli" class="rupiah-input" placeholder="Contoh: 5.000.000" required>
      </div>
    </div>
    <div class="mt-2">
      <button type="submit" class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Simpan Data Motor</button>
    </div>
  </form>
</div>

<!-- Tabel daftar motor yang sudah dibeli -->
<div class="glass-card">
  <h3>Daftar Motor</h3>
  <div class="search-bar">
    <input type="text" id="searchMotor" placeholder="Cari merek, model, plat nomor...">
  </div>
  <div class="table-wrapper">
    <table id="motorTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>Tanggal</th>
          <th>Merek / Model</th>
          <th>CC</th>
          <th>Warna</th>
          <th>Plat Nomor</th>
          <th>Harga Beli</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($motors) > 0): $no = 1; ?>
          <?php while ($row = mysqli_fetch_assoc($motors)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td>
              <?php if ($row['foto_motor'] && file_exists($row['foto_motor'])): ?>
                <img src="<?= $row['foto_motor'] ?>" alt="foto">
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y', strtotime($row['tgl_pembelian'])) ?></td>
            <td><strong><?= htmlspecialchars($row['merek']) ?></strong> <?= htmlspecialchars($row['model']) ?></td>
            <td><?= $row['cc'] ? '<span class="badge badge-info">'.$row['cc'].'</span>' : '<span class="text-muted">-</span>' ?></td>
            <td><?= htmlspecialchars($row['warna']) ?></td>
            <td><strong><?= htmlspecialchars($row['plat_nomor']) ?></strong></td>
            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
            <td>
              <?php
                if ($row['status_motor'] === 'Dibeli') echo '<span class="badge badge-new">Dibeli</span>';
                elseif ($row['status_motor'] === 'Siap Jual') echo '<span class="badge badge-ready">Siap Jual</span>';
                else echo '<span class="badge badge-sold">Terjual</span>';
              ?>
            </td>
            <td>
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="edit.php?id=<?= $row['motor_id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="ph ph-pencil"></i></a>
                <a href="hapus.php?id=<?= $row['motor_id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus motor <?= htmlspecialchars($row['merek'].' '.$row['model']) ?> secara permanen?')" title="Hapus"><i class="ph ph-trash"></i></a>
              <?php else: ?>
                <span class="text-muted"><i class="ph ph-lock-key"></i></span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center text-muted" style="padding:40px">Belum ada data motor</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// ===== MEREK -> MODEL OTOMATIS (VIA FETCH API) =====
const merek = document.getElementById('merek');
const model = document.getElementById('model');
const merekLainGroup = document.getElementById('merek_lain_group');
const modelGroup = document.getElementById('model_group');
const modelLainGroup = document.getElementById('model_lain_group');
const modelLain = document.getElementById('model_lain');

merek.addEventListener('change', () => {
  model.innerHTML = '<option value="">-- Pilih Model --</option>';
  merekLainGroup.style.display = 'none';
  modelGroup.style.display = 'block';
  modelLainGroup.style.display = 'none';
  model.required = true;
  modelLain.required = false;

  if (merek.value === 'lainnya') {
    merekLainGroup.style.display = 'block';
    modelGroup.style.display = 'none';
    modelLainGroup.style.display = 'block';
    model.required = false;
    modelLain.required = true;
    document.getElementById('merek_lain').required = true;
  } else if (merek.value) {
    document.getElementById('merek_lain').required = false;
    // Fetch data model dari database API
    fetch('get_model.php?merek=' + encodeURIComponent(merek.value))
      .then(res => res.json())
      .then(data => {
        data.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m; opt.textContent = m;
          model.appendChild(opt);
        });
      });
  }
});

// ===== PLAT -> ASAL OTOMATIS (VIA FETCH API DB) =====
let platTimeout;
document.getElementById('plat').addEventListener('keyup', function(){
  const wilayahEl = document.getElementById('wilayah');
  const kotaEl = document.getElementById('kota');
  const samsatEl = document.getElementById('samsat');
  const p = this.value.trim();
  
  clearTimeout(platTimeout);

  if (!p) {
    wilayahEl.value = '';
    kotaEl.value = '';
    samsatEl.value = '';
    return;
  }

  // Gunakan delay untuk mencegah request terlalu sering as you type (debounce)
  platTimeout = setTimeout(() => {
    fetch('get_plat.php?plat=' + encodeURIComponent(p))
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          wilayahEl.value = data.wilayah;
          kotaEl.value = data.kota;
          samsatEl.value = data.samsat;
        } else {
          wilayahEl.value = '';
          kotaEl.value = '';
          samsatEl.value = '';
          document.getElementById('plat').value = '';
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Plat Tidak Ditemukan',
              text: 'Plat belum tersedia, silahkan hubungi admin untuk menambahkan',
              confirmButtonColor: 'var(--accent-primary)'
            });
          } else {
            alert('Plat belum tersedia, silahkan hubungi admin untuk menambahkan');
          }
        }
      })
      .catch(err => console.error(err));
  }, 300);
});

// Init event DOM onload
document.addEventListener('DOMContentLoaded', function() {
  // Init foto preview
  previewFoto('fotoInput', 'fotoPreview');

  // Init search filter
  filterTable('searchMotor', 'motorTable');
});
</script>

<?php include "../layout/footer.php"; ?>
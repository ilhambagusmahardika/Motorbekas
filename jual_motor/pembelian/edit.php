<?php
$pageTitle = "Edit Data Motor - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$motor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM motor WHERE motor_id = $id"));

if (!$motor) {
    header("Location: index.php");
    exit;
}

// Get master merek
$master_mereks = mysqli_query($conn, "SELECT nama_merek FROM master_merek ORDER BY nama_merek ASC");

// Prepare custom merek checks
$merek_exists = false;
$mereks_arr = [];
while($m = mysqli_fetch_assoc($master_mereks)) {
    $mereks_arr[] = $m['nama_merek'];
    if ($m['nama_merek'] === $motor['merek']) {
        $merek_exists = true;
    }
}
$is_merek_lain = !$merek_exists;

// Cc options
$ccs = ['100cc','110cc','113cc','115cc','125cc','150cc','155cc','160cc','200cc','250cc','300cc','400cc','500cc','650cc','1000cc'];
?>

<div class="page-header">
  <h2><i class="ph ph-pencil" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Edit Motor</h2>
  <p>Revisi data motor yang telah dibeli</p>
</div>

<div class="glass-card fade-in">
  <form action="update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="motor_id" value="<?= $motor['motor_id'] ?>">
    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($motor['foto_motor']) ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Tanggal Pembelian</label>
        <input type="date" name="tgl_pembelian" value="<?= date('Y-m-d', strtotime($motor['tgl_pembelian'])) ?>" required>
      </div>
      <div class="form-group">
        <label>Beli Dari (Nama Penjual)</label>
        <input type="text" name="nama_penjual" value="<?= htmlspecialchars($motor['nama_penjual']) ?>" required>
      </div>
      <div class="form-group">
        <label>Merek</label>
        <select name="merek" id="merek" required>
          <option value="">-- Pilih Merek --</option>
          <?php foreach($mereks_arr as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= (!$is_merek_lain && $m === $motor['merek']) ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
          <?php endforeach; ?>
          <option value="lainnya" <?= $is_merek_lain ? 'selected' : '' ?>>Lainnya (Tulis Manual)</option>
        </select>
      </div>
      <div class="form-group" id="merek_lain_group" style="display: <?= $is_merek_lain ? 'block' : 'none' ?>">
        <label>Merek Lainnya</label>
        <input type="text" id="merek_lain" name="merek_lain" value="<?= $is_merek_lain ? htmlspecialchars($motor['merek']) : '' ?>">
      </div>
      <div class="form-group" id="model_group" style="display: <?= $is_merek_lain ? 'none' : 'block' ?>">
        <label>Model</label>
        <select name="model" id="model" <?= $is_merek_lain ? '' : 'required' ?>>
          <option value="<?= htmlspecialchars($motor['model']) ?>"><?= htmlspecialchars($motor['model']) ?></option>
        </select>
      </div>
      <div class="form-group" id="model_lain_group" style="display: <?= $is_merek_lain ? 'block' : 'none' ?>">
        <label>Model Lainnya</label>
        <input type="text" id="model_lain" name="model_lain" value="<?= $is_merek_lain ? htmlspecialchars($motor['model']) : '' ?>">
      </div>
      <div class="form-group">
        <label>Kapasitas Mesin (CC)</label>
        <select name="cc" required>
          <option value="">-- Pilih CC --</option>
          <?php foreach($ccs as $cc): ?>
            <option value="<?= $cc ?>" <?= ($cc === $motor['cc']) ? 'selected' : '' ?>><?= $cc ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Warna</label>
        <input type="text" name="warna" value="<?= htmlspecialchars($motor['warna']) ?>" required>
      </div>
      <div class="form-group">
        <label>Tahun Pembuatan</label>
        <select name="tahun_pembuatan" required>
          <?php for($y=date('Y');$y>=1990;$y--): ?>
            <option value="<?= $y ?>" <?= ($y == $motor['tahun_pembuatan']) ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Nama BPKB / STNK</label>
        <input type="text" name="nama_bpkb" value="<?= htmlspecialchars($motor['nama_bpkb']) ?>" required>
      </div>
      <div class="form-group">
        <label>Plat Nomor</label>
        <input type="text" name="plat_nomor" id="plat" value="<?= htmlspecialchars($motor['plat_nomor']) ?>" required>
      </div>
      <div class="form-group">
        <label>Asal Wilayah</label>
        <input type="text" name="asal_wilayah" id="wilayah" value="<?= htmlspecialchars($motor['asal_wilayah']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Asal Kota</label>
        <input type="text" name="asal_kota" id="kota" value="<?= htmlspecialchars($motor['asal_kota']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>SAMSAT Terdaftar</label>
        <input type="text" name="samsat_terdaftar" id="samsat" value="<?= htmlspecialchars($motor['samsat_terdaftar']) ?>" readonly>
      </div>
      <div class="form-group">
        <label>No Rangka</label>
        <input type="text" name="no_rangka" value="<?= htmlspecialchars($motor['no_rangka']) ?>" required>
      </div>
      <div class="form-group">
        <label>No Mesin</label>
        <input type="text" name="no_mesin" value="<?= htmlspecialchars($motor['no_mesin']) ?>" required>
      </div>
      <div class="form-group">
        <label>Pajak Berlaku</label>
        <input type="date" name="pajak_berlaku" value="<?= htmlspecialchars($motor['pajak_berlaku']) ?>" required>
      </div>
      <div class="form-group">
        <label>Foto Motor (Biarkan kosong jika tidak diubah)</label>
        <input type="file" name="foto_motor" accept="image/*" id="fotoInput">
        <img id="fotoPreview" src="<?= htmlspecialchars($motor['foto_motor']) ?>" alt="preview" style="max-width:200px;border-radius:8px;border:1px solid var(--border-color); <?= empty($motor['foto_motor']) ? 'display:none;' : 'display:block;margin-top:8px;' ?>">
      </div>
      <div class="form-group">
        <label>Harga Beli (Rp)</label>
        <input type="text" name="harga_beli" class="rupiah-input" value="<?= number_format($motor['harga_beli'], 0, ',', '.') ?>" required autocomplete="off">
      </div>
      <div class="form-group">
        <label>Status Motor</label>
        <select name="status_motor" required>
          <option value="Dibeli" <?= $motor['status_motor'] === 'Dibeli' ? 'selected' : '' ?>>Dibeli (Inventori)</option>
          <option value="Siap Jual" <?= $motor['status_motor'] === 'Siap Jual' ? 'selected' : '' ?>>Siap Jual</option>
          <option value="Terjual" <?= $motor['status_motor'] === 'Terjual' ? 'selected' : '' ?>>Terjual</option>
        </select>
      </div>
    </div>
    <div class="mt-2 text-right">
      <a href="index.php" class="btn btn-outline" style="margin-right:10px;">Batal</a>
      <button type="submit" class="btn btn-warning"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
    </div>
  </form>
</div>

<script>
// ===== MEREK -> MODEL OTOMATIS =====
const merek = document.getElementById('merek');
const model = document.getElementById('model');
const merekLainGroup = document.getElementById('merek_lain_group');
const modelGroup = document.getElementById('model_group');
const modelLainGroup = document.getElementById('model_lain_group');
const modelLain = document.getElementById('model_lain');

merek.addEventListener('change', () => {
  if (merek.value === 'lainnya') {
    model.innerHTML = '<option value="">-- Pilih Model --</option>';
    merekLainGroup.style.display = 'block';
    modelGroup.style.display = 'none';
    modelLainGroup.style.display = 'block';
    model.required = false;
    modelLain.required = true;
    document.getElementById('merek_lain').required = true;
  } else if (merek.value) {
    merekLainGroup.style.display = 'none';
    modelGroup.style.display = 'block';
    modelLainGroup.style.display = 'none';
    model.required = true;
    modelLain.required = false;
    document.getElementById('merek_lain').required = false;
    
    // Fetch data model dari database API
    fetch('get_model.php?merek=' + encodeURIComponent(merek.value))
      .then(res => res.json())
      .then(data => {
        model.innerHTML = '<option value="">-- Pilih Model --</option>';
        data.forEach(m => {
          const opt = document.createElement('option');
          opt.value = m; opt.textContent = m;
          model.appendChild(opt);
        });
      });
  }
});

// ===== PLAT -> VALIDASI OTOMATIS =====
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

// Init foto preview
document.addEventListener('DOMContentLoaded', function() {
  previewFoto('fotoInput', 'fotoPreview');
});
</script>

<?php include "../layout/footer.php"; ?>

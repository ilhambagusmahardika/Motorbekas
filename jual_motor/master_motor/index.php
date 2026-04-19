<?php
$pageTitle = "Master Merek & Model - JualMotor";
include "../config/database.php";
include "../layout/header.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='glass-card fade-in text-center' style='padding:50px'><h2>Akses Ditolak!</h2><p>Hanya Admin yang dapat mengakses halaman ini.</p></div>");
}

include "../layout/sidebar.php";

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Fetch merek
$mereks = mysqli_query($conn, "SELECT * FROM master_merek ORDER BY nama_merek ASC");
$allMerek = [];
while ($m = mysqli_fetch_assoc($mereks)) {
    $allMerek[] = $m;
}

// Fetch model with merek
$models = mysqli_query($conn, "SELECT mm.id, m.nama_merek, mm.merek_id, mm.nama_model FROM master_model mm JOIN master_merek m ON mm.merek_id = m.id ORDER BY m.nama_merek ASC, mm.nama_model ASC");
?>

<div class="page-header">
  <h2><i class="ph ph-motorcycle" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Master Merek & Model Motor</h2>
  <p>Kelola data dropdown merek dan model kendaraan</p>
</div>

<?php if ($success): ?>
  <div style="background:rgba(0,214,143,0.1);border:1px solid rgba(0,214,143,0.3);color:var(--accent-green);padding:12px 16px;border-radius:8px;margin-bottom:20px;">
    ✅ <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>
<?php if ($error): ?>
  <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
    ❌ <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 2fr; gap: 20px;" class="fade-in">
  
    <!-- Kolom 1: Merek -->
    <div>
      <div class="glass-card">
        <h3><i class="ph ph-plus-circle"></i> Tambah Merek</h3>
        <form action="simpan_merek.php" method="POST">
          <div class="form-group">
            <label>Nama Merek Baru</label>
            <input type="text" name="nama_merek" placeholder="Ducati, KTM, dll" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%"><i class="ph ph-floppy-disk"></i> Simpan Merek</button>
        </form>
      </div>
      
      <div class="glass-card mt-4">
        <h3>Daftar Merek</h3>
        <div class="table-wrapper">
          <table>
            <thead><tr><th>Nama Merek</th><th style="text-align:right">Aksi</th></tr></thead>
            <tbody>
              <tr>
                <td><button type="button" class="btn btn-outline btn-sm" onclick="filterModelByMerek('')" style="width:100%;text-align:left;">Tampilkan Semua Model</button></td>
                <td></td>
              </tr>
              <?php foreach ($allMerek as $m): ?>
                <tr>
                  <td>
                    <button type="button" onclick="filterModelByMerek('<?= htmlspecialchars($m['nama_merek']) ?>')" style="background:none;border:none;color:var(--text-primary);cursor:pointer;font-weight:600;padding:5px 0;">
                      <i class="ph ph-funnel"></i> <?= htmlspecialchars($m['nama_merek']) ?>
                    </button>
                  </td>
                  <td style="text-align:right; white-space:nowrap;">
                    <button type="button" onclick="editMerek(<?= $m['id'] ?>, '<?= addslashes(htmlspecialchars($m['nama_merek'])) ?>')" class="btn btn-warning btn-sm" title="Edit"><i class="ph ph-pencil"></i></button>
                    <a href="hapus_merek.php?id=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus merek ini? Model di dalamnya mungkin ikut terhapus.')" title="Hapus"><i class="ph ph-trash"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Kolom 2: Model -->
    <div>
      <div class="glass-card">
        <h3><i class="ph ph-plus-circle"></i> Tambah Model (Tipe Motor)</h3>
        <form action="simpan_model.php" method="POST">
          <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
              <label>Pilih Merek Induk</label>
              <select name="merek_id" required>
                <option value="">-- Pilih Merek --</option>
                <?php foreach ($allMerek as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_merek']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Model (Tipe)</label>
              <input type="text" name="nama_model" placeholder="Misal: NMAX, Beat, dll" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Simpan Model</button>
        </form>
      </div>

      <div class="glass-card mt-4">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Daftar Model <span id="labelFilterModel" style="color:var(--accent-primary)"></span></h3>
            <div class="search-bar" style="margin-bottom:0;"><input type="text" id="searchModelTxt" placeholder="Cari model..."></div>
        </div>
        <div class="table-wrapper" style="margin-top:15px;">
          <table id="modelTable">
            <thead>
              <tr>
                <th>Merek</th>
                <th>Model</th>
                <th style="text-align:right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($models)): ?>
              <tr class="row-model" data-merek="<?= htmlspecialchars($row['nama_merek']) ?>">
                <td><?= htmlspecialchars($row['nama_merek']) ?></td>
                <td><strong><?= htmlspecialchars($row['nama_model']) ?></strong></td>
                <td style="text-align:right; white-space:nowrap;">
                  <button type="button" class="btn btn-warning btn-sm" onclick="editModel(<?= $row['id'] ?>, <?= $row['merek_id'] ?>, '<?= addslashes(htmlspecialchars($row['nama_model'])) ?>')" title="Edit"><i class="ph ph-pencil"></i></button>
                  <a href="hapus_model.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus model motor ini?')" title="Hapus"><i class="ph ph-trash"></i></a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

</div>

<!-- Modal Edit Merek -->
<div class="modal-overlay" id="modalEditMerek">
  <div class="modal" style="max-width:400px">
    <button class="modal-close" onclick="document.getElementById('modalEditMerek').classList.remove('active')">&times;</button>
    <h3><i class="ph ph-pencil"></i> Edit Merek</h3>
    <form action="update_merek.php" method="POST">
      <input type="hidden" name="id" id="editMerekId">
      <div class="form-group">
        <label>Nama Merek Baru</label>
        <input type="text" name="nama_merek" id="editMerekNama" required>
      </div>
      <button type="submit" class="btn btn-warning" style="width:100%"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<!-- Modal Edit Model -->
<div class="modal-overlay" id="modalEditModel">
  <div class="modal" style="max-width:400px">
    <button class="modal-close" onclick="document.getElementById('modalEditModel').classList.remove('active')">&times;</button>
    <h3><i class="ph ph-pencil"></i> Edit Model</h3>
    <form action="update_model.php" method="POST">
      <input type="hidden" name="id" id="editModelId">
      <div class="form-group">
        <label>Pilih Merek Induk</label>
        <select name="merek_id" id="editModelMerekId" required>
            <?php foreach ($allMerek as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_merek']) ?></option>
            <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Nama Model Lama/Baru</label>
        <input type="text" name="nama_model" id="editModelNama" required>
      </div>
      <button type="submit" class="btn btn-warning" style="width:100%"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<script>
function filterModelByMerek(merek) {
    let rows = document.querySelectorAll('.row-model');
    let label = document.getElementById('labelFilterModel');
    if (merek === '') {
        label.textContent = '';
        rows.forEach(row => row.style.display = '');
    } else {
        label.textContent = ' — ' + merek;
        rows.forEach(row => {
            if(row.getAttribute('data-merek') === merek) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

// Simple live text filter for Model
document.getElementById('searchModelTxt').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    let rows = document.querySelectorAll('.row-model');
    rows.forEach(row => {
        document.getElementById('labelFilterModel').textContent = '';
        let text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(val) > -1 ? '' : 'none';
    });
});

function editMerek(id, nama) {
    document.getElementById('editMerekId').value = id;
    document.getElementById('editMerekNama').value = nama;
    document.getElementById('modalEditMerek').classList.add('active');
}

function editModel(id, merek_id, nama) {
    document.getElementById('editModelId').value = id;
    document.getElementById('editModelMerekId').value = merek_id;
    document.getElementById('editModelNama').value = nama;
    document.getElementById('modalEditModel').classList.add('active');
}
</script>

<?php include "../layout/footer.php"; ?>

<?php
$pageTitle = "Master Plat Nomor - JualMotor";
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

// Pencarian
$search = $_GET['search'] ?? '';
$where = "1=1";
if ($search) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $where .= " AND (prefix LIKE '%$searchEscaped%' OR wilayah LIKE '%$searchEscaped%' OR kota LIKE '%$searchEscaped%')";
}

// Pagination
$limit = 15;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$totalResult = mysqli_query($conn, "SELECT COUNT(id) as total FROM master_plat WHERE $where");
$totalData = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalData / $limit);

$query = mysqli_query($conn, "SELECT * FROM master_plat WHERE $where ORDER BY prefix ASC LIMIT $limit OFFSET $offset");
?>

<div class="page-header">
  <h2><i class="ph ph-tag" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Master Plat Nomor</h2>
  <p>Kelola data dinamis kode wilayah plat nomor kendaraan</p>
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

<div class="glass-card fade-in">
  <h3><i class="ph ph-plus-circle"></i> Tambah Kode Plat Baru</h3>
  <form action="simpan.php" method="POST">
    <div class="form-grid">
      <div class="form-group">
        <label>Prefix Plat (Huruf Depan)</label>
        <input type="text" name="prefix" placeholder="Misal: AB" required style="text-transform:uppercase">
      </div>
      <div class="form-group">
        <label>Suffix Plat (Huruf Belakang)</label>
        <input type="text" name="suffix" placeholder="Opsi khusus Samsat (Misal: A/B/C)" style="text-transform:uppercase">
        <small class="text-muted">Kosongkan jika berlaku global untuk prefix tersebut.</small>
      </div>
      <div class="form-group">
        <label>Area Wilayah (Provinsi)</label>
        <input type="text" name="wilayah" placeholder="DI Yogyakarta" required>
      </div>
      <div class="form-group">
        <label>Area SAMSAT (Kota/Kabupaten)</label>
        <input type="text" name="kota" placeholder="Kota Yogyakarta" required>
      </div>
    </div>
    <div class="mt-2">
      <button type="submit" class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Simpan Master Plat</button>
    </div>
  </form>
</div>

<div class="glass-card mt-4">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:15px;">
      <h3>Daftar Kode Wilayah</h3>
      <div class="search-bar" style="margin-bottom:0px; max-width:300px; width:100%;">
          <form action="index.php" method="GET" style="display:flex; gap:10px;">
              <input type="text" name="search" placeholder="Cari prefix/kota..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn btn-secondary">🔍</button>
          </form>
      </div>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Kode Depan</th>
          <th>Kode Belakang</th>
          <th>Provinsi</th>
          <th>Kota / SAMSAT</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($query) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($query)): ?>
          <tr>
            <td><strong><?= htmlspecialchars($row['prefix']) ?></strong></td>
            <td><?= $row['suffix'] ? "Plat belakang <strong>" . htmlspecialchars($row['suffix']) . "</strong>" : "<span class='text-muted'>Semua huruf</span>" ?></td>
            <td><?= htmlspecialchars($row['wilayah']) ?></td>
            <td><?= htmlspecialchars($row['kota']) ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="ph ph-pencil-simple"></i> Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus master plat ini permanent?')"><i class="ph ph-trash"></i> Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:40px">Tidak ada data pelat nomor</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <!-- Sederhana Pagination -->
  <?php if ($totalPages > 1): ?>
  <div style="display:flex; justify-content:center; gap:5px; margin-top:20px;">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>" style="padding:5px 10px;"><?= $i ?></a>
      <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include "../layout/footer.php"; ?>
